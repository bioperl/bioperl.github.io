---
title: GFF sequence format
layout: default
---

Description
-----------

*GFF* - The acronym originally stood for *G*ene *F*inding *F*ormat, but current specifications are using *G*eneric *F*eature *F*ormat. GFF is a line based, tab separated format for storing features and annotations. This makes it simple to read and write.

See for an example of writing a GFF file from a Bio::Seq object.

GFF2
----

GFF2 specifications are available at the Sanger web site page for more information. This is sometimes called GFF2.5 and was primarily developed for gene features.

GFF3
----

Version 3 page has more info.

<http://public.ecolihub.net/cgi-bin/validate_gff3_online/validate_gff3_online>

The [original WormBase GFF3](http://dev.wormbase.org/db/validate_gff3/validate_gff3_online) validator is currently offline.

### Example

    mmscl\tsupported_mRNA\tCDS\t40759\t41225\t.\t+\t.\tParent=mmscl
    mmscl\tsupported_mRNA\texon\t61468\t61729\t.\t+\t.\tParent=mmMAP_17
    mmscl\tsupported_mRNA\texon\t63653\t63768\t.\t+\t.\tParent=mmMAP_17
    mmscl\tsupported_mRNA\texon\t65434\t65537\t.\t+\t.\tParent=mmMAP_17
    mmscl\tsupported_mRNA\texon\t65983\t66383\t.\t+\t.\tParent=mmMAP_17
    mmscl\tRepeatMasker\tRepeat\t55\t115\t378\t-\t.\tTarget=B4;Note="(230) 61";Name="SINE/B4"
    mmscl\tRepeatMasker\tRepeat\t160\t304\t1153\t+\t.\tTarget=B1_MM;Note="1 147";Name="SINE/Alu"
