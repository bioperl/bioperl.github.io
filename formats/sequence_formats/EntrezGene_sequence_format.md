---
title: EntrezGene sequence format
layout: default
---

Description
-----------

This is an example of Entrez Gene ASN1 format. This is part of a [much larger file](ftp://ftp.ncbi.nih.gov/gene/DATA/ASN_OLD/Viruses/dsRNA_viruses).

Example
-------

```
Entrezgene ::= {
 track-info {
   geneid 956430 ,
   status live ,
   create-date
     std {
       year 2003 ,
       month 8 ,
       day 1 ,
       hour 20 ,
       minute 50 ,
       second 0 } ,
   update-date
     std {
       year 2005 ,
       month 5 ,
       day 29 ,
       hour 6 ,
       minute 54 ,
       second 38 } } ,
 type protein-coding ,
 source {
   org {
     taxname "Pseudomonas phage phi-6" ,
     db {
       {
         db "taxon" ,
         tag
           id 10879 } } ,
     orgname {
       name
         virus "Pseudomonas phage phi-6" ,
       mod {
         {
           subtype nat-host ,
           subname "Pseudomonas syringae pv. phaseolicola" } ,
         {
           subtype old-name ,
           subname "bacteriophage phi-6" } } ,
       lineage "Viruses; dsRNA viruses; Cystoviridae; Cystovirus" ,
       gcode 11 ,
       div "PHG" } } ,
   subtype {
     {
       subtype segment ,
       name "segment S" } } } ,
 gene {
   locus-tag "phi-6S_1" } ,
 prot {
   desc "P8 protein" } ,
 gene-source {
   src "Entrez Genomes" ,
   src-int 20330558 ,
   src-str1 "NC_003714" ,
   src-str2 "NC_003714:305-754" ,
   gene-display FALSE ,
   locus-display FALSE ,
   extra-terms FALSE } ,
 locus {
   {
     type genomic ,
     accession "NC_003714" ,
     version 1 ,
     seqs {
       int {
         from 304 ,
         to 753 ,
         strand plus ,
         id
           gi 20330558 } } ,
     products {
       {
         type peptide ,
         accession "NP_620340" ,
         version 1 ,
         refs {
           pmid 3754015 } ,
         genomic-coords {
           int {
             from 304 ,
             to 753 ,
             id
               gi 20330558 } } ,
         seqs {
           whole
             gi 20330559 } } } } } ,
 comments {
   {
     type comment ,
     heading "NCBI Reference Sequences (RefSeq)" ,
     version 0 ,
     products {
       {
         type peptide ,
         heading "Product" ,
         accession "NP_620340" ,
         version 1 ,
         source {
           {
             src {
               db "Protein" ,
               tag
                 id 20330559 } ,
             anchor "NP_620340" ,
             post-text "P8 protein [Pseudomonas phage phi-6]" } } ,
         seqs {
           whole
             gi 20330559 } } } } ,
   {
     type comment ,
     heading "Related Sequences" ,
     version 0 ,
     products {
       {
         type genomic ,
         heading "Genomic" ,
         accession "M12921" ,
         version 1 ,
         source {
           {
             src {
               db "Nucleotide" ,
               tag
                 id 215492 } ,
             anchor "M12921" } } ,
         seqs {
           whole
             gi 215492 } ,
         products {
           {
             type peptide ,
             accession "AAA32358" ,
             version 1 ,
             source {
               {
                 src {
                   db "Protein" ,
                   tag
                     id 215493 } ,
                 anchor "AAA32358" } } ,
             seqs {
               whole
                 gi 215493 } } } } } } ,
   {
     type comment ,
     heading "RefSeq Status" ,
     label "Provisional" ,
     version 0 } } ,
 xtra-iq {
   {
     tag "NUCLEOTIDE" ,
     value "215492" } ,
   {
     tag "PROTEIN" ,
     value "139301" } ,
   {
     tag "PROTEIN" ,
     value "215493" } ,
   {
     tag "PROTEIN" ,
     value "75727" } } ,
 non-unique-keys {
   {
     db "ID" ,
     tag
       id 20330558 } } }
Entrezgene ::= {
 track-info {
   geneid 956431 ,
   status live ,
   create-date
     std {
       year 2003 ,
       month 8 ,
       day 1 ,
       hour 20 ,
       minute 50 ,
       second 0 } ,
   update-date
     std {
       year 2005 ,
       month 5 ,
       day 29 ,
       hour 6 ,
       minute 54 ,
       second 38 } } ,
 type protein-coding ,
 source {
   org {
     taxname "Pseudomonas phage phi-6" ,
     db {
       {
         db "taxon" ,
         tag
           id 10879 } } ,
     orgname {
       name
         virus "Pseudomonas phage phi-6" ,
       mod {
         {
           subtype nat-host ,
           subname "Pseudomonas syringae pv. phaseolicola" } ,
         {
           subtype old-name ,
           subname "bacteriophage phi-6" } } ,
       lineage "Viruses; dsRNA viruses; Cystoviridae; Cystovirus" ,
       gcode 11 ,
       div "PHG" } } ,
   subtype {
     {
       subtype segment ,
       name "segment S" } } } ,
 gene {
   locus-tag "phi-6S_2" } ,
 prot {
   desc "P12 protein" } ,
 gene-source {
   src "Entrez Genomes" ,
   src-int 20330558 ,
   src-str1 "NC_003714" ,
   src-str2 "NC_003714:754-1341" ,
   gene-display FALSE ,
   locus-display FALSE ,
   extra-terms FALSE } ,
 locus {
   {
     type genomic ,
     accession "NC_003714" ,
     version 1 ,
     seqs {
       int {
         from 753 ,
         to 1340 ,
         strand plus ,
         id
           gi 20330558 } } ,
     products {
       {
         type peptide ,
         accession "NP_620341" ,
         version 1 ,
         refs {
           pmid 3754015 } ,
         genomic-coords {
           int {
             from 753 ,
             to 1340 ,
             id
               gi 20330558 } } ,
         seqs {
           whole
             gi 20330560 } } } } } ,
 comments {
   {
     type comment ,
     heading "NCBI Reference Sequences (RefSeq)" ,
     version 0 ,
     products {
       {
         type peptide ,
         heading "Product" ,
         accession "NP_620341" ,
         version 1 ,
         source {
           {
             src {
               db "Protein" ,
               tag
                 id 20330560 } ,
             anchor "NP_620341" ,
             post-text "P12 protein [Pseudomonas phage phi-6]" } } ,
         seqs {
           whole
             gi 20330560 } } } } ,
   {
     type comment ,
     heading "Related Sequences" ,
     version 0 ,
     products {
       {
         type genomic ,
         heading "Genomic" ,
         accession "M12921" ,
         version 1 ,
         source {
           {
             src {
               db "Nucleotide" ,
               tag
                 id 215492 } ,
             anchor "M12921" } } ,
         seqs {
           whole
             gi 215492 } ,
         products {
           {
             type peptide ,
             accession "AAA32359" ,
             version 1 ,
             source {
               {
                 src {
                   db "Protein" ,
                   tag
                     id 215494 } ,
                 anchor "AAA32359" } } ,
             seqs {
               whole
                 gi 215494 } } } } } } ,
   {
     type comment ,
     heading "RefSeq Status" ,
     label "Provisional" ,
     version 0 } } ,
 xtra-iq {
   {
     tag "NUCLEOTIDE" ,
     value "215492" } ,
   {
     tag "PROTEIN" ,
     value "139160" } ,
   {
     tag "PROTEIN" ,
     value "215494" } ,
   {
     tag "PROTEIN" ,
     value "75728" } } ,
 non-unique-keys {
   {
     db "ID" ,
     tag
       id 20330558 } } }
Entrezgene ::= {
 track-info {
   geneid 956432 ,
   status live ,
   create-date
     std {
       year 2003 ,
       month 8 ,
       day 1 ,
       hour 20 ,
       minute 50 ,
       second 0 } ,
   update-date
     std {
       year 2005 ,
       month 5 ,
       day 29 ,
       hour 6 ,
       minute 54 ,
       second 39 } } ,
 type protein-coding ,
 source {
   org {
     taxname "Pseudomonas phage phi-6" ,
     db {
       {
         db "taxon" ,
         tag
           id 10879 } } ,
     orgname {
       name
         virus "Pseudomonas phage phi-6" ,
       mod {
         {
           subtype nat-host ,
           subname "Pseudomonas syringae pv. phaseolicola" } ,
         {
           subtype old-name ,
           subname "bacteriophage phi-6" } } ,
       lineage "Viruses; dsRNA viruses; Cystoviridae; Cystovirus" ,
       gcode 11 ,
       div "PHG" } } ,
   subtype {
     {
       subtype segment ,
       name "segment S" } } } ,
 gene {
   locus-tag "phi-6S_3" } ,
 prot {
   desc "P9 protein" } ,
 gene-source {
   src "Entrez Genomes" ,
   src-int 20330558 ,
   src-str1 "NC_003714" ,
   src-str2 "NC_003714:1341-1613" ,
   gene-display FALSE ,
   locus-display FALSE ,
   extra-terms FALSE } ,
 locus {
   {
     type genomic ,
     accession "NC_003714" ,
     version 1 ,
     seqs {
       int {
         from 1340 ,
         to 1612 ,
         strand plus ,
         id
           gi 20330558 } } ,
     products {
       {
         type peptide ,
         accession "NP_620342" ,
         version 1 ,
         refs {
           pmid 3754015 } ,
         genomic-coords {
           int {
             from 1340 ,
             to 1612 ,
             id
               gi 20330558 } } ,
         seqs {
           whole
             gi 20330561 } } } } } ,
 comments {
   {
     type comment ,
     heading "NCBI Reference Sequences (RefSeq)" ,
     version 0 ,
     products {
       {
         type peptide ,
         heading "Product" ,
         accession "NP_620342" ,
         version 1 ,
         source {
           {
             src {
               db "Protein" ,
               tag
                 id 20330561 } ,
             anchor "NP_620342" ,
             post-text "P9 protein [Pseudomonas phage phi-6]" } } ,
         seqs {
           whole
             gi 20330561 } } } } ,
   {
     type comment ,
     heading "Related Sequences" ,
     version 0 ,
     products {
       {
         type genomic ,
         heading "Genomic" ,
         accession "M12921" ,
         version 1 ,
         source {
           {
             src {
               db "Nucleotide" ,
               tag
                 id 215492 } ,
             anchor "M12921" } } ,
         seqs {
           whole
             gi 215492 } ,
         products {
           {
             type peptide ,
             accession "AAA32360" ,
             version 1 ,
             source {
               {
                 src {
                   db "Protein" ,
                   tag
                     id 215495 } ,
                 anchor "AAA32360" } } ,
             seqs {
               whole
                 gi 215495 } } } } } } ,
   {
     type comment ,
     heading "RefSeq Status" ,
     label "Provisional" ,
     version 0 } } ,
 xtra-iq {
   {
     tag "NUCLEOTIDE" ,
     value "215492" } ,
   {
     tag "PROTEIN" ,
     value "139313" } ,
   {
     tag "PROTEIN" ,
     value "215495" } ,
   {
     tag "PROTEIN" ,
     value "75729" } } ,
 non-unique-keys {
   {
     db "ID" ,
     tag
       id 20330558 } } }
Entrezgene ::= {
 track-info {
   geneid 956433 ,
   status live ,
   create-date
     std {
       year 2003 ,
       month 8 ,
       day 1 ,
       hour 20 ,
       minute 50 ,
       second 0 } ,
   update-date
     std {
       year 2005 ,
       month 5 ,
       day 29 ,
       hour 6 ,
       minute 54 ,
       second 39 } } ,
 type protein-coding ,
 source {
   org {
     taxname "Pseudomonas phage phi-6" ,
     db {
       {
         db "taxon" ,
         tag
           id 10879 } } ,
     orgname {
       name
         virus "Pseudomonas phage phi-6" ,
       mod {
         {
           subtype nat-host ,
           subname "Pseudomonas syringae pv. phaseolicola" } ,
         {
           subtype old-name ,
           subname "bacteriophage phi-6" } } ,
       lineage "Viruses; dsRNA viruses; Cystoviridae; Cystovirus" ,
       gcode 11 ,
       div "PHG" } } ,
   subtype {
     {
       subtype segment ,
       name "segment S" } } } ,
 gene {
   locus-tag "phi-6S_4" } ,
 prot {
   desc "P5a (alt., gtg start codon)" } ,
 gene-source {
   src "Entrez Genomes" ,
   src-int 20330558 ,
   src-str1 "NC_003714" ,
   src-str2 "NC_003714:1620-2282" ,
   gene-display FALSE ,
   locus-display FALSE ,'

```

