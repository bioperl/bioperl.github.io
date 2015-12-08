---
title: Tinyseq sequence format
layout: default
---

Description
-----------

Tinyseq is an XML based format for plain sequences. Details are available in the NCBI documentation.

Example
-------

```
<?xml version="1.0"?>
<!DOCTYPE TSeqSet PUBLIC "-//NCBI//NCBI TSeq/EN" http://www.ncbi.nlm.nih.gov/dtd/NCBI_TSeq.dtd>
<TSeqSet>
  <TSeq>
    <TSeq_seqtype value="nucleotide"/>
    <TSeq_gi>`11321596`</TSeq_gi>
    <TSeq_sid>`ref|NM_002253.1|`</TSeq_sid>
    <TSeq_taxid>`9606`</TSeq_taxid>
    <TSeq_orgname>`Homo sapiens`</TSeq_orgname>
    <TSeq_defline>`Homo sapiens kinase insert domain receptor, mRNA`</TSeq_defline>
    <TSeq_length>`58`</TSeq_length>
    <TSeq_sequence>`ACTGAGTCCCGGGACCCCGGGAGAGCGGTCAGTGTGTGGTCGCTGCGTTTT`</TSeq_sequence>
  </TSeq>
</TSeqSet>
```

