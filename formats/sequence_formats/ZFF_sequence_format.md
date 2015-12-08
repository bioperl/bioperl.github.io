---
title: ZFF sequence format
layout: default
---

Description
-----------

Ian Korf's **Z**oe **F**ile **F**ormat. It looks a lot like GFF but has a `&gt;` header which defines the name of the sequence and lacks the seq_id column accordingly.

*The following is from the SNAP documenation about ZFF:*

What is ZFF? It is a non-standard format (ie. nobody uses it but me) that bears resemblance to FASTA and GFF (both true standards). ZFF is also the input format for training data to SNAP making it very easy to train SNAP on new genomes.

There are two styles of ZFF, the short format and the long format. In both cases, the sequence records are separated by a definition line, just like FASTA.

### Short format

In the short format, there are 4 fields: Label, Begin, End, Group. The 4th field is optional. Label is a controlled vocabulary (see zoeFeature.h for a complete list). All exons of a gene (or more appropriately a transcriptional unit) must share the same unique group name. The strand of the feature is implied in the coordinates, so if Begin &gt; End, the feature is on the minus strand. Here's and example of the short format with two sequences, each containing a single gene on the plus strand:

```
       >sequence-1
       Einit    201    325   Y73E7A.6
       Eterm   2175   2319   Y73E7A.6
       >sequence-2
       Einit    201    462   Y73E7A.7
       Exon    1803   2031   Y73E7A.7
       Exon    2929   3031   Y73E7A.7
       Exon    3467   3624   Y73E7A.7
       Exon    4185   4406   Y73E7A.7
       Eterm   5103   5280   Y73E7A.7

```
### Long format

The long format adds 5 fields between the coordinates and the group: Strand, Score, 5'-overhang, 3'-overhang, and Frame. Strand is +/-. Score is any floating point value. 5'- and 3'-overhang are the number of bp of an incomplete codon at each end of an exon. Frame is the reading frame (0..2 and *not* 1..3). Here's an example of the long format:

```
       >Y73E7A.6
       Einit    201    325   +    90   0   2   1   Y73E7A.6
       Eterm   2175   2319   +   295   1   0   2   Y73E7A.6
       >Y73E7A.7
       Einit    201    462   +   263   0   1   1   Y73E7A.7
       Exon    1803   2031   +   379   2   2   0   Y73E7A.7
       Exon    2929   3031   +   236   1   0   0   Y73E7A.7
       Exon    3467   3624   +   152   0   2   0   Y73E7A.7
       Exon    4185   4406   +   225   1   2   2   Y73E7A.7
       Eterm   5103   5280   +    46   1   0   2   Y73E7A.7"

```

