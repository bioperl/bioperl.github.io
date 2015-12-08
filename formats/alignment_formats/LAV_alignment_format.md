---
title: LAV_alignment_format.md
layout: default
---

Description
-----------

LAV is an alignment format developed by Webb Miller.

Example
-------

```
#:lav
d {
  "blastz.v7 H99/cn-h99_chromosome1.fasta JEC21/cn-jec21_chr1.fasta T=3 C=3
     A    C    G    T
    91 -114  -31 -123
  -114  100 -125  -31
   -31 -125  100 -114
  -123  -31 -114   91
  O = 400, E = 30, K = 3000, L = 3000, M = 0"
}
#:lav
s {
  "H99/cn-h99_chromosome1.fasta" 1 2291499 0 1
  "JEC21/cn-jec21_chr1.fasta" 1 2300533 0 1
}
h {
   ">cn-h99_chromosome1"
   ">cn-jec21_chr1"
}
a {
  s 3202
  b 11013 4
  e 11064 55
  l 11013 4 11064 55 86
}
a {
  s 3506
  b 11157 118
  e 11201 162
  l 11157 118 11201 162 93
}
```

Converting this to AXT alignment format you see the two alignment blocks.

```
0 cn-h99_chromosome1 11013 11064 cn-jec21_chr1 4 55 + 3202
CACCCTCCCACCGTGTACTCTGCCACCTGTCCTGCATATCCTCCCTTTCATC
CGCTCTCCCACCGTGTATTCTCCCACCTATCCTGCATATCCTCTCTTGCATC
```

```
1 cn-h99_chromosome1 11157 11201 cn-jec21_chr1 118 162 + 3506
GGAGTAGCCGAGCAAATGCGTCGGGCGTTGAGGGATAGCCGAGTG
GGAGTAGCCGAGCAAATGGGTCGGGCGTCGAGGGATAGCCGAATG
```

In ClustalW multiple alignment format this would be

```
cn-h99_chromosome_1 CACCCTCCCACCGTGTACTCTGCCACCTGTCCTGCATATCCTCCCTTTCATC
cn-jec21_chr1       CGCTCTCCCACCGTGTATTCTCCCACCTATCCTGCATATCCTCTCTTGCATC                    
                    * * ************* *** ****** ************** *** ****
```

```
cn-h99_chromosome_1 GGAGTAGCCGAGCAAATGCGTCGGGCGTTGAGGGATAGCCGAGTG
cn-jec21_chr1       GGAGTAGCCGAGCAAATGGGTCGGGCGTCGAGGGATAGCCGAATG
                    ****************** ********* ************* **'

```

