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
#include "indexer.h"

struct index_node *root;

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

static struct ref_node *alloc_ref_node(void)
{
	struct ref_node *ret;

	if ((ret = calloc(1, sizeof(*ret))) == NULL) {
		printf("No memory\n");
		return NULL;
	}

	INIT_LIST_HEAD(&ret->list);

	return ret;
}

static int index_word(char *word, int id)
{
	int ret = 0;
	char *c, ch_idx, w[128];
	struct index_node *p = root, *new;
	struct ref_node *rnode;

	/* convert word to lower case */
	strcpy(w, word);
	c = w;
	while (*c) {
		*c = tolower(*c);
		c++;
	}

	/* ignore common words */
	if (ignore(w)) {
		printf("Ignoring '%s'\n", w);
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
	list_for_each_entry(rnode, &p->ref_list, list) {
		if (rnode->id == id) {
			rnode->occurences++;
			goto out;
		}
	}

	/* 'word' is found for the first time in 'file'.
	 * add a new rnode to ref_list.
	 */
	if ((rnode = alloc_ref_node()) == NULL) {
		ret = -1;
		goto out;
	}

	rnode->id = id;
	rnode->occurences++;
	list_add_tail(&rnode->list, &p->ref_list);

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
	
	/* parse id from filename */
	n = strlen(file);
	strncpy(line, file, n - 4);
	line[n - 3] = 0;
	id = strtol(line, NULL, 10);

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

	if (!list_empty(&p->ref_list)) {
		for (i = 0; i < level; i++)
			printf("%c", buf[i]);
		printf("\n");
		/* print ref list */
		list_for_each_entry(rnode, &p->ref_list, list) {
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
	
}

static int process_cmd(struct indexer_msg *cmd, struct indexer_msg *rsp)
{
	
}

static int send_rsp(int cfd, struct indexer_msg *rsp)
{
	
}

int main(int argc, char **argv)
{
	int ret = 0, done = 0, msg_id, sfd = 0, cfd = 0, caddr_len;
	char buf[32];
	struct sockaddr_un saddr, caddr;

	if ((root = alloc_index_node()) == NULL) {
		ret = -ENOMEM;
		goto err;
	}

	if ((ret = index_dir(RESUME_DIR) < 0))
		goto err;

	// dump_index(root, buf, 0);
	
	/* create socket */
	if ((sfd = socket(AF_UNIX, SOCK_STREAM, 0)) < 0) {
		ret = errno;
		goto err;
	}
	
	/* bind to local path */
	memset(&saddr, 0, sizeof(saddr));
	saddr.sun_family = AF_UNIX;
	strcpy(saddr.sun_path, INDEXER_SOCKET_PATH);
	if (bind(sfd, &saddr, sizeof(saddr) < 0) {
		ret = errno;
		goto err;
	}
	
	listen(sfd, 5);
	
	while (!done) {
		
		/* accept connection */
		caddr_len = sizeof(caddr);
		if ((cfd = accept(sfd, &caddr, &caddr_len)) < 0) {
			ret = errno;
			goto err;
		}
		
		/* recv command and data */
		if (recv_cmd(cfd, &cmd) < 0) {
			ret = errno;
			goto err;
		}
		
		/* process command */
		if (process_cmd(&cmd, &rsp) < 0) {
			ret = errno;
			goto err;
		}
		
		/* send response */
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
