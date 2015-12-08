---
title: FASTQ sequence format
layout: default
---

Description
-----------

A file format used frequently at the Wellcome Trust Sanger Institute to bundle a FASTA sequence and its quality data.

FASTQ files have sequence and quality data on a single line and the quality values are single-byte encoded. FOr the standard Sanger version of FASTQ, to retrieve the decimal values for qualities you need to subtract 33 (or Octal 41) from each byte and then convert to a '2 digit + 1 space' integer.

The original FASTQ file format can be parsed by the system using the module. The most recent version (BioPerl v. 1.6.1) can parse variations of FASTQ fastq_paper from Solexa and Illumina and interconvert them if the proper variants are designated (either 'sanger', 'illumina', or 'solexa').

Example
-------

```
@NCYC361-11a03.q1k bases 1 to 1576
GCGTGCCCGAAAAAATGCTTTTGGAGCCGCGCGTGAAAT
+NCYC361-11a03.q1k bases 1 to 1576
!)))))****(((***%%((((*(((+,**(((+**+,-
```

References
----------

http://www.ncbi.nlm.nih.gov/pubmed/20015970
https://en.wikipedia.org/wiki/FASTQ_format
