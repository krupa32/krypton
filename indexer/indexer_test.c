/*
 * indexer_test.c
 * Krupa Sivakumaran, June 2012
 */
#include <stdio.h>
#include <string.h>
#include <sys/ipc.h>
#include <sys/msg.h>
#include "indexer.h"

static void process_find(char *word, int msg_id)
{
	struct indexer_msg_cmd msg_cmd;
	struct indexer_msg_rsp msg_rsp;
	int i;

	memset(&msg_cmd, 0, sizeof(msg_cmd));
	memset(&msg_rsp, 0, sizeof(msg_rsp));

	msg_cmd.cmd = MSG_CMD_FIND;
	strcpy(msg_cmd.param, word);
	if (msgsnd(msg_id, &msg_cmd, MAX_MSG_CMD_LEN, 0) < 0) {
		perror(0);
		return;
	}

	if (msgrcv(msg_id, &msg_rsp, sizeof(struct indexer_msg_rsp_data), 0, 0) < 0) {
		perror(0);
		return;
	}

	for (i = 0; i < msg_rsp.data.n_entries; i++) {
		printf("name=%s, occurences=%d\n", msg_rsp.data.entry[i].name, msg_rsp.data.entry[i].occurences);
	}
}

int main(int argc, char **argv)
{
	char line[100], param[4][100], *p, *cmd;
	int msg_id, len;

	if ((msg_id = msgget(MSG_KEY, 0)) < 0) {
		perror(0);
		goto err;
	}

	while (1) {
		printf("\nindexer>");
		fflush(stdout);

		fgets(line, 99, stdin);
		len = strlen(line);
		line[len - 1] = 0;
		if (strcmp(line, "quit") == 0)
			break;

		cmd = strtok(line, " ");
		if (strcmp(cmd, "find") == 0) {
			p = strtok(NULL, " ");
			process_find(p, msg_id);
		}
	}

	return 0;

err:
	return -1;
}
