---
title: SELEX multiple alignment format
layout: default
---

Description
-----------

**SELEX** is an interleaved multiple alignment format that arose as an intuitive format, easy to write and manipulate manually with a text editor. It is usually easy to convert other alignment formats into SELEX format (though it can be harder to go the other way, since SELEX is more free-format than other alignment formats). For instance, GCG's MSF multiple alignment format and the output of the CLUSTALV multiple alignment program are similar interleaved formats. Because SELEX evolved to accomodate different user input styles, it is very tolerant of various inconsistencies such as different gap symbols, varying line lengths, etc.

SELEX format is used by Sean Eddy's HMMER package. It can store RNA secondary structure as part of the sequence annotation.

Example
-------

```
    #=SQ HSFAU  1.00 - - 0..0:0 H.sapiens fau mRNA
    #=SQ HSFAU1 1.00 - - 0..0:0 H.sapiens fau 1 gene

    HSFAU  ttcctctttctcgactccatcttcgcggtagctgggaccgccgttcagtc
    HSFAU1 ctaccattttccctctcgattctatatgtacactcgggacaagttctcct

    HSFAU  gccaatatgcagctctttgtccgcgcccaggagctacacaccttcgaggt
    HSFAU1 gatcgaaaacggcaaaactaaggccccaagtaggaatgccttagttttcg

    HSFAU  gaccggccaggaaacggtcgcccagatcaaggctcatgtagcctcactgg
    HSFAU1 gggttaacaatgattaacactgagcctcacacccacgcgatgccctcagc

    HSFAU  agggcattgccccggaagatcaagtcgtgctcctggcaggcgcgcccctg
    HSFAU1 tcctcgctcagcgctctcaccaacagccgtagcccgcagccccgctggac

    HSFAU  gaggatgaggccactctgggccagtgcggggtggaggccctgactaccct
    HSFAU1 accggttctccatccccgcagcgtagcccggaacatggtagctgccatct

    HSFAU  ggaagtagcaggccgcatgcttggaggtaaagttcatggttccctggccc
    HSFAU1 ttacctgctacgccagccttctgtgcgcgcaactgtctggtcccgccccg

    HSFAU  gtgctggaaaagtgagaggtcagactcctaaggtggccaaacaggagaag
    HSFAU1 tcctgcgcgagctgctgcccaggcaggttcgccggtgcgagcgtaaaggg

    HSFAU  aagaagaagaagacaggtcgggctaagcggcggatgcagtacaaccggcg
    HSFAU1 gcggagctaggactgccttgggcggtacaaatagcagggaaccgcgcggt

    HSFAU  ctttgtcaacgttgtgcccacctttggcaagaagaagggccccaatgcca
    HSFAU1 cgctcagcagtgacgtgacacgcagcccacggtctgtactgacgcgccct

    HSFAU  actcttaagtcttttgtaattctggctttctctaataaaaaagccactta
    HSFAU1 cgcttcttcctctttctcgactccatcttcgcggtagctgggaccgccgt

    HSFAU  gttcagtcaaaaaaaaaa                                
    HSFAU1 tcaggtaagaatggggccttggctggatccgaagggcttgtagcaggttg

    HSFAU                                                    
    HSFAU1 gctgcggggtcagaaggcgcggggggaaccgaagaacggggcctgctccg

    HSFAU                                                    
    HSFAU1 tggccctgctccagtccctatccgaactccttgggaggcactggccttcc

    HSFAU                                                    
    HSFAU1 gcacgtgagccgccgcgaccaccatcccgtcgcgatcgtttctggaccgc

    HSFAU                                                    
    HSFAU1 tttccactcccaaatctcctttatcccagagcatttcttggcttctctta

    HSFAU                                                    
    HSFAU1 caagccgtcttttctttactcagtcgccaatatgcagctctttgtccgcg

    HSFAU                                                    
    HSFAU1 cccaggagctacacaccttcgaggtgaccggccaggaaacggtcgcccag

    HSFAU                                                    
    HSFAU1 atcaaggtaaggctgcttggtgcgccctgggttccattttcttgtgctct

    HSFAU                                                    
    HSFAU1 tcactctcgcggcccgagggaacgcttacgagccttatctttccctgtag

    HSFAU                                                    
    HSFAU1 gctcatgtagcctcactggagggcattgccccggaagatcaagtcgtgct

    HSFAU                                                    
    HSFAU1 cctggcaggcgcgcccctggaggatgaggccactctgggccagtgcgggg

    HSFAU                                                    
    HSFAU1 tggaggccctgactaccctggaagtagcaggccgcatgcttggaggtgag

    HSFAU                                                    
    HSFAU1 tgagagaggaatgttctttgaagtaccggtaagcgtctagtgagtgtggg

    HSFAU                                                    
    HSFAU1 gtgcatagtcctgacagctgagtgtcacacctatggtaatagagtacttc

    HSFAU                                                    
    HSFAU1 tcactgtcttcagttcagagtgattcttcctgtttacatccctcatgttg

    HSFAU                                                    
    HSFAU1 aacacagacgtccatgggagactgagccagagtgtagttgtatttcagtc

    HSFAU                                                    
    HSFAU1 acatcacgagatcctagtctggttatcagcttccacactaaaaattaggt

    HSFAU                                                    
    HSFAU1 cagaccaggccccaaagtgctctataaattagaagctggaagatcctgaa

    HSFAU                                                    
    HSFAU1 atgaaacttaagatttcaaggtcaaatatctgcaactttgttctcattac

    HSFAU                                                    
    HSFAU1 ctattgggcgcagcttctctttaaaggcttgaattgagaaaagaggggtt

    HSFAU                                                    
    HSFAU1 ctgctgggtggcaccttcttgctcttacctgctggtgccttcctttccca

    HSFAU                                                    
    HSFAU1 ctacaggtaaagtccatggttccctggcccgtgctggaaaagtgagaggt

    HSFAU                                                    
    HSFAU1 cagactcctaaggtgagtgagagtattagtggtcatggtgttaggacttt

    HSFAU                                                    
    HSFAU1 ttttcctttcacagctaaaccaagtccctgggctcttactcggtttgcct

    HSFAU                                                    
    HSFAU1 tctccctccctggagatgagcctgagggaagggatgctaggtgtggaaga

    HSFAU                                                    
    HSFAU1 caggaaccagggcctgattaaccttcccttctccaggtggccaaacagga

    HSFAU                                                    
    HSFAU1 gaagaagaagaagaagacaggtcgggctaagcggcggatgcagtacaacc

    HSFAU                                                    
    HSFAU1 ggcgctttgtcaacgttgtgcccacctttggcaagaagaagggccccaat

    HSFAU                                                    
    HSFAU1 gccaactcttaagtcttttgtaattctggctttctctaataaaaaagcca

    HSFAU                                                    
    HSFAU1 cttagttcagtcatcgcattgtttcatctttacttgcaaggcctcaggga

    HSFAU                  
    HSFAU1 gaggtgtgcttctcgg
```

Finding DNA or RNA ligands
--------------------------

**S**ystematic **E**volution of **L**igands by **EX**ponential Enrichment is also a method for selecting nucleic acids with specific binding properties, see [All you wanted to know about SELEX...](http://www.ncbi.nlm.nih.gov/pubmed/7536299)


