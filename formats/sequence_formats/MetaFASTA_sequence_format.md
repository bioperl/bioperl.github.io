---
title: MetaFASTA sequence format
layout: default
---

Description
-----------

The *MetaFASTA* format "extends" the popular FASTA sequence format to store meta-sequence information on a per-base (or per-amino acid) basis.

It can be also used as a multiple alignment format.

Heikki Lehväslaiho's original specs for this format and the responses can be found [here](http://thread.gmane.org/gmane.comp.lang.perl.bio.general/1370/focus=1370):

Example
-------

```
>test
ABCDEFHIJKLMNOPQRSTUVWXYZ
&charge
NBNAANCNJCNNNONNCNNUNNXNZ
&chemical
LBSAARCLJCLSMOIMCHHULRXRZ
&functional
HBPAAHCHJCHHPOHPCPPUHHXPZ
&hydrophobic
I & OIOIJOIIOOIOOOOUIIXOZ
&structural
ABAEEIEIJEIIEOAEEAAUIAXAZ'
```

