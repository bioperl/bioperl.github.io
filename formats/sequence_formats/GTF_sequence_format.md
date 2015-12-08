---
title: GTF sequence format
layout: default
---

Description
-----------

Another name for the GFF2 spec, sometimes called GFF2.5, however there is a specific versioning of GTF and the current is GTF2.2 (that's really clear now isn't...). This format primarily for encoding location of protein coding genes. Essentially there are four types of features when annotating genes, *CDS* (coding sequence exons), *exon* (not necessarily coding from which UTRs can be inferred), *start_codon* and *stop_codon* to indicate beginning and end of reading frame. Most critically in the GTF format are the two key-value pairs in the last column. These are *gene_id * and *transcript_id *. Some programs also encode the *exontype* field as any one of *initial*, *internal*, *terminal*, or *single* although these can be inferred via the placement of the start and stop codons wrt the exon and CDS features. The *transcript_id* field permits alternative splicing isoforms to be encoded. The order of the key/value pairs is not specified although some programs may expect it to be in gene/transcript/exontype order.

Example
-------

```
Chrom1  SNAP    start_codon     505     507     .       +       .       transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    CDS     505     673     21.624  +       0       exontype "initial"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    exon    505     673     21.624  +       .       exontype "initial"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    CDS     730     1446    46.298  +       2       exontype "internal"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    exon    730     1446    46.298  +       .       exontype "internal"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    CDS     1472    3447    147.456 +       2       exontype "terminal"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    exon    1472    3447    147.456 +       .       exontype "terminal"; transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";
Chrom1  SNAP    stop_codon      3445    3447    .       +       .       transcript_id "Chrom1.0-snapCCIN.1.1"; gene_id "Chrom1.0-snap.1";

```
See GTF2 page.

'


