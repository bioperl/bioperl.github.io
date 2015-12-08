---
title: PSL_alignment_format.md
layout: default
---

PSL format and other UCSC tools.

Here are the columns as taken from the UCSC documentation.

1.  matches - Number of bases that match that aren't repeats
2.  misMatches - Number of bases that don't match
3.  repMatches - Number of bases that match but are part of repeats
4.  nCount - Number of 'N' bases
5.  qNumInsert - Number of inserts in query
6.  qBaseInsert - Number of bases inserted in query
7.  tNumInsert - Number of inserts in target
8.  tBaseInsert - Number of bases inserted in target
9.  strand - '+' or '-' for query strand. In mouse, second '+'or '-' is for genomic strand
10. qName - Query sequence name
11. qSize - Query sequence size
12. qStart - Alignment start position in query
13. qEnd - Alignment end position in query
14. tName - Target sequence name
15. tSize - Target sequence size
16. tStart - Alignment start position in target
17. tEnd - Alignment end position in target
18. blockCount - Number of blocks in the alignment
19. blockSizes - Comma-separated list of sizes of each block
20. qStarts - Comma-separated list of starting positions of each block in query
21. tStarts - Comma-separated list of starting positions of each block in target
