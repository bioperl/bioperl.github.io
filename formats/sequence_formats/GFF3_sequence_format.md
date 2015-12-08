---
title: GFF3 sequence format
layout: default
---

Version 3 of the GFF format. The formal definition is available on the [SONG website](http://www.sequenceontology.org/gff3.shtml).

A short version of the field specifications:

*Column 1: "seqid"*

The ID of the landmark used to establish the coordinate system for the current feature. IDs may contain any characters, but must escape any characters not in the set \[a-zA-Z0-9.:^\*$@!+_?-|\]. In particular, IDs may not contain unescaped whitespace and must not begin with an unescaped "&gt;".

*Column 2: "source"*

The source is a free text qualifier intended to describe the algorithm or operating procedure that generated this feature. Typically this is the name of a piece of software, such as "Genescan" or a database name, such as "Genbank." In effect, the source is used to extend the feature ontology by adding a qualifier to the type creating a new composite type that is a subclass of the type in the type column.

*Column 3: "type"*

The type of the feature (previously called the "method"). This is constrained to be either: (a) a term from the "lite" sequence ontology, SOFA; or (b) a SOFA accession number. The latter alternative is distinguished using the syntax SO:000000.

*Columns 4 & 5: "start" and "end"*

The start and end of the feature, in 1-based integer coordinates, relative to the landmark given in column 1. Start is always less than or equal to end.

For zero-length features, such as insertion sites, start equals end and the implied site is to the right of the indicated base in the direction of the landmark.

*Column 6: "score"*

The score of the feature, a floating point number. As in earlier versions of the format, the semantics of the score are ill-defined. It is strongly recommended that E-values be used for sequence similarity features, and that P-values be used for ab initio gene prediction features.

*Column 7: "strand"*

The strand of the feature. + for positive strand (relative to the landmark), - for minus strand, and . for features that are not stranded. In addition, ? can be used for features whose strandedness is relevant, but unknown.

*Column 8: "phase"*

For features of type "CDS", the phase indicates where the feature begins with reference to the reading frame. The phase is one of the integers 0, 1, or 2, indicating the number of bases that should be removed from the beginning of this feature to reach the first base of the next codon. In other words, a phase of "0" indicates that the next codon begins at the first base of the region described by the current line, a phase of "1" indicates that the next codon begins at the second base of this region, and a phase of "2" indicates that the codon begins at the third base of this region. This is NOT to be confused with the frame, which is simply start modulo 3.

For forward strand features, phase is counted from the start field. For reverse strand features, phase is counted from the end field.

*Column 9: "attributes"*

A list of feature attributes in the format tag=value. Multiple tag=value pairs are separated by semicolons ";". Multiple values of the same tag are indicated by separating the values with a comma ",". URL escaping rules are used for tags or values containing the following characters: ",=;". Spaces are allowed in this field, but tabs must be replaced with the %09 URL escape. Attribute tags are case sensitive. All attributes that begin with an uppercase letter are reserved for later use. Attributes that begin with a lowercase letter can be used freely by applications.


 
