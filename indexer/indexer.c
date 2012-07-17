/*
 * indexer.c
 * Krupa Sivakumaran, June 2012
 */

#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <sys/socket.h>
#include <sys/un.h>
#include <mysql/mysql.h>
#include "indexer.h"

struct index_node *root;
struct list_head candidate_list;
struct list_head result_list;
int result_cnt;


char *ignored_words[] = {
	"a", "after", "all", "an", "and", "are", "as", "at",
	"be", "been", "both", "by",
	"can",
	"do",
	"for", "from",
	"has", "have",
	"i", "in", "is",
	"like",
	"my",
	"of", "on",
	"such",
	"that", "the", "their", "them", "thereby", "this", "to",
	"upon",
	"were", "which", "with",
	NULL
};

static int ignore(char *word)
{
	char **w = ignored_words;

	while (*w) {
		if (strcmp(*w, word) == 0)
			return 1;
		w++;
	}

	return 0;
}

static struct candidate_node *get_candidate_node(int id)
{
	struct candidate_node *cnode;
	
	klist_for_each_entry(cnode, &candidate_list, list) {
		if (cnode->id == id)
			return cnode;
	}
	
	return NULL;
}

static struct candidate_node *alloc_candidate_node(int id)
{
	struct candidate_node *ret;

	if ((ret = calloc(1, sizeof(*ret))) == NULL) {
		printf("No memory\n");
		return NULL;
	}

	INIT_LIST_HEAD(&ret->list);
	INIT_LIST_HEAD(&ret->ref_list);
	
	ret->id = id;

	return ret;
}

static void remove_all_ref_nodes(struct candidate_node *cnode)
{
	struct ref_node *rnode, *tmp;
	
	printf("Removing all ref nodes for candidate %d\n", cnode->id);
	
	klist_for_each_entry_safe(rnode, tmp, &cnode->ref_list, cref_list) {
		klist_del_init(&rnode->cref_list);
		klist_del_init(&rnode->list);
		free(rnode);
	}
}

static struct index_node *alloc_index_node(void)
{
	struct index_node *ret;

	if ((ret = calloc(1, sizeof(*ret))) == NULL) {
		printf("No memory\n");
		return NULL;
	}

	INIT_LIST_HEAD(&ret->ref_list);

	return ret;
}

static struct ref_node *alloc_ref_node(int id)
{
	struct ref_node *ret;
	struct candidate_node *cnode = NULL;

	if ((ret = calloc(1, sizeof(*ret))) == NULL) {
		printf("No memory\n");
		return NULL;
	}

	INIT_LIST_HEAD(&ret->list);
	INIT_LIST_HEAD(&ret->cref_list);
	
	if (cnode = get_candidate_node(id))
		klist_add_tail(&ret->cref_list, &cnode->ref_list);

	return ret;
}

static int index_word(char *word, int id)
{
	int ret = 0;
	char *c, ch_idx, w[128], *s;
	struct index_node *p = root, *new;
	struct ref_node *rnode;

	/* validate and convert word to lower case.
	 * validation is required because some non-ascii (>128) chars
	 * were found in some resumes.
	 */
	c = w;
	s = word;
	while (*s) {
		if (isascii(*s))
			*c++ = tolower(*s);
		s++;
	}
	*c = 0;

	/* ignore common words */
	if (ignore(w)) {
		//printf("Ignoring '%s'\n", w);
		goto out;
	}

	c = w;
	while (*c) {
		/* stop traversing if any non-alphabet is encountered.
		 * so if 'html5' is the word, index 'html' and ignore 5.
		 */
		if (!isalpha(*c))
			break;

		ch_idx = *c - 'a';

		if (p->child[ch_idx] == NULL) {
			if ((new = alloc_index_node()) == NULL)
				break;

			p->child[ch_idx] = new;
		}

		p = p->child[ch_idx];

		c++;
	}

	if (p == root) // empty string, dont index it
		goto out;

	/* 'word' was already indexed in 'file', then it would
	 * already be in the file_list. so just increment 'occurences'.
	 */
	klist_for_each_entry(rnode, &p->ref_list, list) {
		if (rnode->id == id) {
			rnode->occurences++;
			if (rnode->occurences > p->max_occurences)
				p->max_occurences = rnode->occurences;
			goto out;
		}
	}

	/* 'word' is found for the first time in 'file'.
	 * add a new rnode to ref_list.
	 */
	if ((rnode = alloc_ref_node(id)) == NULL) {
		ret = -1;
		goto out;
	}

	rnode->id = id;
	rnode->occurences++;
	if (rnode->occurences > p->max_occurences)
		p->max_occurences = rnode->occurences;
	klist_add_tail(&rnode->list, &p->ref_list);

out:
	return ret;
}

static int index_file(char *dir, char *file)
{
	int ret = 0, n = 0;
	char line[512], filename[100];
	FILE *fp;
	char *delim = " ,.@:()[]\n\t", *word, *s, *d;
	int id;
	struct candidate_node *cnode = NULL;
	
	/* parse id from filename */
	n = strlen(file);
	strncpy(line, file, n - 4);
	line[n - 3] = 0;
	id = strtol(line, NULL, 10);

	if ((cnode = get_candidate_node(id)) == NULL) {
		if ((cnode = alloc_candidate_node(id)) == NULL) {
			printf("Error allocating candidate node\n");
			ret = -1;
			goto err;
		}
		klist_add_tail(&cnode->list, &candidate_list);
	} else {
		remove_all_ref_nodes(cnode);
	}
	
	sprintf(filename, "%s/%s", dir, file);
	printf("Indexing file %s\n", filename);

	if ((fp = fopen(filename, "r")) == NULL) {
		perror(0);
		ret = -1;
		goto err;
	}

	while (fgets(line, 512, fp) != NULL) {
		word = strtok(line, delim);
		while (word) {
			index_word(word, id);

			word = strtok(NULL, delim);
		}
	}

err:
	return ret;
}

static int index_dir(char *dir)
{
	int ret = 0;
	DIR *dp;
	struct dirent *entry;

	printf("Indexing directory %s\n", dir);

	if ((dp = opendir(dir)) == NULL) {
		perror(NULL);
		ret = errno;
		goto err;
	}

	while (entry = readdir(dp)) {
		if (entry->d_type == DT_REG) {
			ret = index_file(dir, entry->d_name);
			if (ret < 0)
				goto err;
		}
	}

err:
	return ret;
}

static void dump_index(struct index_node *p, char *buf, int level)
{
	int i, j;
	struct ref_node *rnode;

	if (!klist_empty(&p->ref_list)) {
		for (i = 0; i < level; i++)
			printf("%c", buf[i]);
		printf("\n");
		/* print ref list */
		klist_for_each_entry(rnode, &p->ref_list, list) {
			printf("id=%d, occurences=%d\n", rnode->id, rnode->occurences);
		}

	}

	/* goto next level */
	for (i = 0; i < 26; i++) {
		if (p->child[i]) {
			buf[level] = i + 'a';
			dump_index(p->child[i], buf, level + 1);
		}
	}
}

static int recv_cmd(int cfd, struct indexer_msg *cmd)
{
	int ret = 0, remaining;
	char *p;
	
	/* recv opcode and len - 8 bytes */
	if ((ret = recv(cfd, cmd, 8, 0)) < 0) {
		ret = errno;
		goto err;
	}
	
	/* recv cmd->len bytes of data */
	remaining = cmd->len;
	p = (char *)cmd + 8;
	while (remaining) {
		if ((ret = recv(cfd, p, remaining, 0)) < 0) {
			if (errno != EINTR) {
				ret = errno;
				goto err;
			}
		}
		
		remaining -= ret;
		p += ret;
	}
	
	return 8 + cmd->len;

err:
	perror(0);
	return ret;
}

static int process_parse(struct indexer_msg *cmd, struct indexer_msg *rsp)
{
	int ret = 0;
	char file[100];
	
	//printf("process_parse: id=%d\n", cmd->data.parse_cmd.id);
	sprintf(file, "%d.txt", cmd->data.parse_cmd.id);
	
	ret = index_file(RESUME_DIR, file);
	
	rsp->opcode = RSP_PARSE;
	rsp->len = sizeof(struct indexer_parse_rsp);
	rsp->data.parse_rsp.status = ret;
	
	return ret;
}

static struct index_node *get_index_node(char *tag)
{
	int i;
	char *ch = tag;
	struct index_node *inode = root, *ret = NULL;
	
	while (*ch && inode) {
		i = *ch - 'a';
		inode = inode->child[i];
		ch++;
	}
	
	if (*ch == 0 && inode) // tag is found
		ret = inode;

	return ret;
}

static struct result_node *get_result_node(int id)
{
	struct result_node *res_node;
	
	klist_for_each_entry(res_node, &result_list, list) {
		if (res_node->id == id)
			return res_node;
	}
	
	return NULL;
}

static struct result_node *alloc_result_node(int id, int score)
{
	struct result_node *ret;

	if ((ret = calloc(1, sizeof(*ret))) == NULL) {
		printf("No memory\n");
		return NULL;
	}

	INIT_LIST_HEAD(&ret->list);
	
	ret->id = id;
	ret->score = score;

	return ret;	
}

static void sort_results()
{
	struct list_head tmp;
	struct result_node *res_node, *tmp_node, *ins_node;
	
	/* move result_list to tmp and clear result_list */
	INIT_LIST_HEAD(&tmp);
	klist_splice_init(&result_list, &tmp);
	
	klist_for_each_entry_safe(res_node, tmp_node, &tmp, list) {
		/* remove res_node from tmp list */
		klist_del_init(&res_node->list);

		/* find sorted position in result_list to insert res_node */
		klist_for_each_entry(ins_node, &result_list, list) {
			if (res_node->score > ins_node->score)
				break;
		}
		
		/* res_node should be inserted BEFORE insert pos */
		klist_add_tail(&res_node->list, &ins_node->list);

	}
	
}

static void free_all_results(void)
{
	struct result_node *res_node, *tmp;
	
	klist_for_each_entry_safe(res_node, tmp, &result_list, list) {
		klist_del_init(&res_node->list);
		free(res_node);
	}
	
	result_cnt = 0;
}

static int get_candidate_info(int id, int *experience, int *ctc, char *location)
{
	int ret = 0, i;
	char q[100];
	MYSQL *conn;
	MYSQL_RES *res;
	MYSQL_ROW row;
	
	if ((conn = mysql_init(NULL)) == NULL) {
		printf("Error creating mysql conn\n");
		ret = -1;
		goto out;
	}
	
	if (mysql_real_connect(conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 0, NULL, 0) == NULL) {
		printf("Error connecting to db\n");
		ret = -1;
		goto out;
	}
	
	sprintf(q, "select experience,location,min_ctc from candidates where id=%d", id);
	if (mysql_query(conn, q) != 0) {
		printf("Error executing query\n");
		ret = -1;
		goto out;
	}
	
	res = mysql_store_result(conn);
	if (!res || mysql_num_rows(res) == 0) {
		printf("No results found\n");
		ret = -1;
		goto out;
	}
	
	row = mysql_fetch_row(res);
	*experience = row[0] ? strtol(row[0], 0, 0) : 0;
	strcpy(location, row[1] ? row[1] : '\0');
	*ctc = row[2] ? strtol(row[2], 0, 0) : 0;
	
	mysql_free_result(res);

out:
	if (conn)
		mysql_close(conn);
	return ret;
}

static int process_match(struct indexer_msg *cmd, struct indexer_msg *rsp)
{
	int ret = 0, n_tags = 0, i = 0, experience, ctc, exp_diff, ctc_diff, n_locations = 0;
	float tag_ratio, tag_score, exp_score, ctc_score, loc_score;
	char *tags[50], *p, *locations[10], loc[100];
	struct index_node *inode;
	struct ref_node *rnode;
	struct result_node *res_node;
	
	printf("process_match: tags=%s, exp=%d, ctc=%d, location=%s\n",
		   cmd->data.match_cmd.tags, cmd->data.match_cmd.experience,
		   cmd->data.match_cmd.ctc, cmd->data.match_cmd.location);
	
	result_cnt = 0;
	
	/* split and store each tag, max 50 tags */
	i = 0;
	p = strtok(cmd->data.match_cmd.tags, ", ");
	while (p && i < 50) {
		n_tags++;
		tags[i++] = p;
		p = strtok(NULL, ", ");
	}

	/* split and store each location, max 10 locations */
	i = 0;
	p = strtok(cmd->data.match_cmd.location, ", ");
	while (p && i < 10) {
		n_locations++;
		locations[i++] = p;
		p = strtok(NULL, ", ");
	}
	
	/* for each tag */
	for (i = 0; i < n_tags; i++) {
		if ((inode = get_index_node(tags[i])) == NULL)
			continue;

		/* for each candidate with a matching tag */
		klist_for_each_entry(rnode, &inode->ref_list, list) {
			tag_ratio = (float)rnode->occurences / inode->max_occurences;
			tag_score = tag_ratio / n_tags * WEIGHT_TAG;
			
			if ((res_node = get_result_node(rnode->id)) == NULL) {
				/* candidate not found in result, add new */
				res_node = alloc_result_node(rnode->id, (int)tag_score);
				klist_add_tail(&res_node->list, &result_list);
			} else {
				/* candidate found in result, just update score */
				res_node->score += (int)tag_score;
			}
			
		}
	}
	
	/* for each result */
	klist_for_each_entry(res_node, &result_list, list) {
		if (get_candidate_info(res_node->id, &experience, &ctc, loc) != 0)
			continue;
		
		exp_diff = abs(cmd->data.match_cmd.experience - experience);
		exp_score = (float)(10 - exp_diff) / 10 * WEIGHT_EXPERIENCE;
		exp_score = (exp_score < 0) ? 0 : exp_score;
		
		ctc_diff = abs(cmd->data.match_cmd.ctc - ctc);
		ctc_score = (float)(500000 - ctc_diff) / 500000 * WEIGHT_SALARY;
		ctc_score = (ctc_score < 0) ? 0 : ctc_score;
		
		/* for each location given by user */
		loc_score = 0;
		for (i = 0; i < n_locations; i++) {
			if (strstr(loc, locations[i]))
				loc_score += (float)1 / n_locations * WEIGHT_LOCATION;
		}
		
		//printf("id=%d,t=%d,e=%d,s=%d,l=%d\n", res_node->id, res_node->score,
		//	   (int)exp_score, (int)ctc_score, (int)loc_score);
		res_node->score += (int)(exp_score + ctc_score + loc_score);
	}
	
	sort_results();
	
	/* store results in rsp  */
	i = 0;
	klist_for_each_entry(res_node, &result_list, list) {
		rsp->data.match_rsp.info[i].id = res_node->id;
		rsp->data.match_rsp.info[i].score = res_node->score;
		i++;
	}
	
	rsp->opcode = RSP_MATCH;
	rsp->len = sizeof(struct indexer_match_rsp);
	rsp->data.match_rsp.n_matches = i;

	/* free all result nodes */
	free_all_results();

	return ret;
}

static int process_cmd(struct indexer_msg *cmd, struct indexer_msg *rsp)
{
	int ret = 0;
	
	switch (cmd->opcode) {
	case CMD_PARSE:
		ret = process_parse(cmd, rsp);
		break;
	case CMD_MATCH:
		ret = process_match(cmd, rsp);
		break;
	default:
		printf("Unrecognized command: 0x%x", cmd->opcode);
	}
	
	return ret;
}

static int send_rsp(int cfd, struct indexer_msg *rsp)
{
	int ret = 0, remaining;
	char *p;
	
	remaining = 8 + rsp->len;
	p = (char *)rsp;
	while (remaining) {
		if ((ret = send(cfd, p, remaining, 0)) < 0) {
			if (errno != EINTR) {
				ret = errno;
				goto err;
			}
		}
		
		remaining -= ret;
		p += ret;
	}
	
	return 8 + rsp->len;

err:
	perror(0);
	return ret;
	
}

int main(int argc, char **argv)
{
	int ret = 0, done = 0, msg_id, sfd = 0, cfd = 0, caddr_len;
	char buf[32];
	struct sockaddr_un saddr, caddr;
	struct indexer_msg cmd, rsp;

	if ((root = alloc_index_node()) == NULL) {
		ret = -ENOMEM;
		goto err;
	}
	
	INIT_LIST_HEAD(&candidate_list);
	INIT_LIST_HEAD(&result_list);

	if ((ret = index_dir(RESUME_DIR) < 0))
		goto err;

	// dump_index(root, buf, 0);
	
	/* create socket */
	printf("Creating socket\n");
	if ((sfd = socket(AF_UNIX, SOCK_STREAM, 0)) < 0) {
		ret = errno;
		goto err;
	}
	
	/* bind to local path */
	umask(0);
	printf("Binding to %s\n", INDEXER_SOCKET_PATH);
	unlink(INDEXER_SOCKET_PATH);
	memset(&saddr, 0, sizeof(saddr));
	saddr.sun_family = AF_UNIX;
	strcpy(saddr.sun_path, INDEXER_SOCKET_PATH);
	if (bind(sfd, (struct sockaddr *)&saddr, sizeof(saddr)) < 0) {
		ret = errno;
		goto err;
	}
	
	listen(sfd, 5);
	
	while (!done) {
		
		/* accept connection */
		printf("Waiting for connection\n");
		caddr_len = sizeof(caddr);
		if ((cfd = accept(sfd, (struct sockaddr *)&caddr, &caddr_len)) < 0) {
			ret = errno;
			goto err;
		}
		
		/* recv command and data */
		printf("Receiving command\n");
		if (recv_cmd(cfd, &cmd) < 0) {
			ret = errno;
			goto err;
		}
		
		/* process command */
		printf("Processing command\n");
		if (process_cmd(&cmd, &rsp) < 0) {
			ret = errno;
			goto err;
		}
		
		/* send response */
		printf("Sending response len=%d\n", 8 + rsp.len);
		if (send_rsp(cfd, &rsp) < 0) {
			ret = errno;
			goto err;
		}
		
		close(cfd);
		cfd = 0;
	}
	
err:
	if (cfd)
		close(cfd);
	if (sfd)
		close(sfd);
	if (ret)
		perror(0);
		
	return ret;
}
