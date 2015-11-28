---
title: "HOWTO:Phylogenetic Analysis Pipeline"
layout: default
---

Abstract
--------

This HOWTO describes aspects of building a pipeline for phylogenetic analyses. It is intended to describe how to start from a collection of genes, infer sets of orthologs and paralogs, compute alignments, and build phylogenetic trees.

Authors
-------

-   Jason Stajich <jason at bioperl.org> or <jason_stajich at berkeley.edu>

Copyright
---------

This document is copyright Jason Stajich. It can be copied and distributed under the terms of the Perl Artistic License.

Introduction
------------

Gene sets
---------

### Organizing datasets

Computing Sequence Similarities
-------------------------------

### A word about datafiles

Parsing these large sets of data can be time consuming. If we are only interested in single score values for each pair of significantly similar sequences then it makes sense to just keep a simple set of output that is the minimum information needed. Using "-outfmt 6" or "-outfmt 7" with BLAST+ is one option (-m8 or -m 9 format of [BLAST]). The -m 9 output from [FASTA] can be similarly processed to produce a compact tabular output using the fastam9_to_tabule script in the scripts/searchio/fastam9_to_table.

### BLAST vs FASTA vs Smith-Waterman

### All vs All

This approach is simply computed where there are a dataset of sequences and all pairwise

Practically if BLAST is used, this means running BLAST on a database of sequences in ''DB.fa'' and using the same file ''DB.fa'' as the input. If DB.fa is a database of proteins we would use the following options. We are requesting the

    -m 9

to generate

`formatdb -i db.fa -p T`
`blastall -p blastp -i db.fa -d db.fa -m 8 -e 1e-3 -o db-vs-db.BLASTP`

### All pairwise

This approach is only appropriate for whole genome comparisons where there is a single dataset for each species. All pairs of species combinations are enumerated and the a series of searches are performed. For example if there are three species A, B, C then all pairs are computed as follows.

`SEARCH A A > A-vs-A.search`
`SEARCH B B > B-vs-B.search`
`SEARCH C C > C-vs-C.search`
`SEARCH A B > A-vs-B.search`
`SEARCH B A > B-vs-A.search`
`SEARCH C A > C-vs-A.search`
`SEARCH C B > C-vs-B.search`
`SEARCH B C > B-vs-C.search`

This results in a matrix of results

|                     | '''A''' | '''B''' | '''C''' |
|---------------------|---------------------|---------------------|---------------------|
| '''A''' | A-vs-A              | A-vs-B              | A-vs-C              |
| '''B''' | B-vs-A              | B-vs-B              | B-vs-C              |
| '''C''' | C-vs-A              | C-vs-B              | C-vs-C              |

To add a fourth species, one only needs to fill in the missing values in the added row and column of the matrix (''in italics'').

|                     | '''A''' | '''B''' | '''C''' | ''D''      |
|---------------------|---------------------|---------------------|---------------------|--------------------|
| '''A''' | A-vs-A              | A-vs-B              | A-vs-C              | ''A-vs-D'' |
| '''B''' | B-vs-A              | B-vs-B              | B-vs-C              | ''B-vs-D'' |
| '''C''' | C-vs-A              | C-vs-B              | C-vs-C              | ''C-vs-D'' |
| ''D''       | ''D-vs-A''  | ''D-vs-B''  | ''D-vs-C''  | ''D-vs-D'' |

Ortholog identification
-----------------------

### Best hits

Sometimes called ''Best Reciprocal'', ''Best Bidirectional'' hits. This is typically the postprocessed from an All-vs-All

### OrthoMCL

[OrthoMCL]

### InParanoid

[InParanoid]

### Jaccard Clustering

[Jaccard Clustering] is available through TIGR code.

Multiple sequence alignments
----------------------------

-   this will contain code examples and overviews

### ClustalW

-   [Clustalw]

### T-Coffee

-   [Tcoffee]

### MUSCLE

-   [MUSCLE]

### Probcons

-   [PROBCONS]

### Others

-   kprank
-   kalign

### Protein to Codon alignments

Alignment and Cluster cleanup
-----------------------------

### Rejecting outlier sequences from clusters

### Removing poorly aligning columns

Phylogenetic analyses
---------------------

-   Protein vs DNA alignments vs Codon alignments

### Tree building methods

### Parsimony

-   [PAUP]
-   [ProtPars]

### Distance based

-   [PAUP]
-   [Quicktree]
-   [ProtML]
-   [NJTree]

### Maximum Likelihood

-   [PAUP]
-   [RAxML]
-   [GARLI]

### Bayesian

-   [MrBayes]
-   [PHYML]

### [Parsimony in PHYLIP]

### Distance [Distance in PHYLIP] + [NJ Tree in PHYLIP]

Rates of sequence evolution
---------------------------

-   Coding sequence evolution
-   Noncoding sequence evolution
-   Tools
    -   [HY-PHY]
    -   [PAML]
    -   [Xrates]'


