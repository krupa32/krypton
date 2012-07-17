all : indexer

indexer: indexer.c indexer.h
	gcc indexer.c -o indexer -lmysqlclient

clean:
	rm indexer
