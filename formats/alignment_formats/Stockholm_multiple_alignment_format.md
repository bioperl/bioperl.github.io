---
title: Stockholm multiple alignment format
layout: default
---

Description
-----------

Stockholm format is a flatfile format for databases of annotated multiple sequence alignments. It is used by the Pfam (for protein family alignments) and Rfam (for RNA family alignments) databases.

Stockholm shows by-column alignment annotations, such as RNA secondary structure, in a compact and (if appropriately indented) human-readable way. It allows sequences to be split over multiple lines, although this is "discouraged" in the specification.

BioPerl currently supports reading single-line, multi-line, and interleaved Stockholm formats. Planned Stockholm alignment output using `write_aln()` will only support single-line and interleaved formats (i.e. similar to Pfam and Rfam output). Currently only Pfam-like single-line output is implemented.

Example
-------

```
    # STOCKHOLM 1.0
    #=GC SS_cons       .................<<<<<<<<...<<<<<<<........>>>>>>>..
    AP001509.1         UUAAUCGAGCUCAACACUCUUCGUAUAUCCUC-UCAAUAUGG-GAUGAGGGU
    #=GR AP001509.1 SS -----------------<<<<<<<<---..<<-<<-------->>->>..--
    AE007476.1         AAAAUUGAAUAUCGUUUUACUUGUUUAU-GUCGUGAAU-UGG-CACGA-CGU
    #=GR AE007476.1 SS -----------------<<<<<<<<-----<<.<<-------->>.>>----

    #=GC SS_cons       ......<<<<<<<.......>>>>>>>..>>>>>>>>...............
    AP001509.1         CUCUAC-AGGUA-CCGUAAA-UACCUAGCUACGAAAAGAAUGCAGUUAAUGU
    #=GR AP001509.1 SS -------<<<<<--------->>>>>--->>>>>>>>---------------
    AE007476.1         UUCUACAAGGUG-CCGG-AA-CACCUAACAAUAAGUAAGUCAGCAGUGAGAU
    #=GR AE007476.1 SS ------.<<<<<--------->>>>>.-->>>>>>>>---------------
    //
```


