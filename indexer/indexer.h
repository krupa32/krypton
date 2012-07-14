/*
 * indexer.h
 * Krupa Sivakumaran, June 2012
 */

#ifndef INDEXER_H
#define INDEXER_H

#include "list.h"

#define RESUME_DIR				"/uploads_txt"
#define INDEXER_SOCKET_PATH		"/tmp/indexer.sock"

#define MAX_FILENAME		64
#define MAX_TAGS_LEN		100
#define MAX_MATCHES			100

#define CMD_PARSE			0x01
#define CMD_MATCH			0x02
#define RSP_PARSE			0x81
#define RSP_MATCH			0x82

struct index_node
{
	struct index_node *child[26];
	struct list_head ref_list;
};

struct ref_node
{
	int id;
	int occurences;

	struct list_head cref_list;
	struct list_head list;
};

struct candidate_node
{
	int id;
	struct list_head ref_list;
	
	struct list_head list;
};

struct indexer_parse_cmd
{
	int id;
};

struct indexer_parse_rsp
{
	int status;
};

struct indexer_match_cmd
{
	char tags[MAX_TAGS_LEN];
};

struct indexer_match_rsp
{
	int n_matches;
	
	struct
	{
		int id;
		int score;
	} info[MAX_MATCHES];
};

struct indexer_msg
{
	int opcode;
	int len;
	union
	{
		struct indexer_parse_cmd parse_cmd;
		struct indexer_parse_rsp parse_rsp;
		struct indexer_match_cmd match_cmd;
		struct indexer_match_rsp match_rsp;
	} data;
};


#endif // INDEXER_H
