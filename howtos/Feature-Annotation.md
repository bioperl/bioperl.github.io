---
title: "HOWTO:Features and Annotations"
layout: default
---

Authors
-------

[Brian Osborne]

  
[briano at bioteam.net](mailto:briano-at-bioteam.net)

Steve Chervitz

  
[sac at bioperl.org](mailto:sac-at-bioperl.org)

### Copyright

This document is copyright Brian Osborne. It can be copied and distributed under
the terms of the [Perl Artistic License](http://www.perl.com/pub/language/misc/Artistic.html).

Abstract
--------

This is a HOWTO that explains how to use the SeqFeature and Annotation objects of Bioperl.

Introduction
------------

There's no more central notion in bioinformatics than the idea that portions of
protein or nucleotide sequence have specific characteristics (or [features]). A
given stretch of DNA may have been found to be essential for the proper
transcriptional regulation of a gene, or a particular amino acid sequence may
bind a particular ion, for example. This simple idea turns out to be a bit more
complicated in the bioinformatics world where there's a need to represent the
actual data in all its varied forms. The promoter region may not be precisely
defined down to the base pair, a transcribed region may be divided into
discontinuous exons, a gene may have different numbered positions on different
maps, a sequence may have a sub-sequence which itself possesses some
characteristic, an experimental observation may be associated with a literature
reference, and so on.

This HOWTO describes aspects of Bioperl's approach. The problem is how to create
software that accepts, analyzes, and displays any and all of this sequence
annotation with the required attention to detail yet remains flexible and easy
to use. The general names for the modules or objects that serve these purposes
in Bioperl are and .

The HOWTO will discuss these objects and the differences between them. There's
also discussion of how to get useful data from these objects and the basics of
how to create your own sequence [annotations] using the objects.

The Basics
----------

Some BioPerl [neophytes] may also be new to [object-oriented programming and
this notion of an object. OOP is not the subject of this HOWTO but there should
be some discussion of how objects are used in BioPerl. In the BioPerl world
parsing a [GenBank file] doesn't give you data, it gives you an object and you
can ask the object, a kind of variable, for data. While annotating you don't
create a file or database entry directly. You might create a "sequence object"
and an "annotation object", then put these two together to create an "annotated
sequence object". You could then tell this object to make a version of itself as
a file, or pass this object to a "database object" in order to enter some data
into the database. This is a very flexible and logical way to design a complex
piece of software like BioPerl, since each part of the system can be created and
evaluated separately.

A central idea in OOP is [inheritance], which means that a child object can
derive some of its capabilities or functionality from a parent object. The OOP
approach also allows new modules to modify or add functionality, distinct from
the parent. Practically speaking this means that there's not one definitive
SeqFeature or Annotation object but many, each a variation on a theme. The
details of the these varieties will be discussed in other sections, but for now
we could use some broad definitions that apply to all the variations.

A [SeqFeature object] : is designed to be associated with a sequence, and can
have a location on that sequence - it's a way of describing the characteristics
of a specific part of a sequence. SeqFeature objects can also have features
themselves, which you could call sub-features but which, in fact, are complete
SeqFeature objects. SeqFeature objects can also have one or more Annotations
associated with them (see [Features_vs._Annotations] for an in-depth discussion
of that). 

<!-- -->

An Annotation object : is also associated with a sequence as you'd expect but it
does not have a location on the sequence, it's associated with an entire
sequence. This is one of the important differences between a SeqFeature and an
Annotation. Annotations also can't have SeqFeatures, which makes sense since
SeqFeature objects typically have locations. The relative simplicity of the
Annotation has made it amenable to the creation of a useful set of Annotation
objects, each devoted to a particular kind of fact or observation.

Locations were discussed, above. Describing locations can be complicated in
certain situations, say when some feature is located on different sequences with
varying degrees of precision. One location could also be shared between
disparate objects, such as two different kinds of SeqFeatures. You may also want
to describe a feature with many locations, like a repeated sequence motif in a
protein. Because of these sorts of complexities and because one may want to
create different types of locations the BioPerl authors elected to keep location
functionality inside dedicated '''Location objects'''.

SeqFeatures and Annotations will make the most sense if you're already somewhat
familiar with [BioPerl] and its central and objects. The reader is referred to
the ,, and the for more information on these topics. Here's a bit of code, to
summarize:

```perl

# BAB55667.gb is a Genbank file, and Bioperl knows that it
# is a Genbank file because of the '.gb' file suffix

use Bio::SeqIO;
my $seqio_object = Bio::SeqIO->new(-file => "BAB55667.gb" );
my $seq_object = $seqio_object->next_seq;

```

''Note'': `$seq_object` is a object. A object, such as would be returned from a
fasta file, does not have associated feature or annotation objects (see [Table 6
below]).

Now that we have a sequence object in hand we can examine its features and annotations.

Getting the Features
--------------------

The focus of this HOWTO is mostly on Genbank format but bear in mind that all of the code shown here will also work on other formats containing features or annotations (EMBL, Swissprot, BSML, Chado XML, GAME, KEGG, Locuslink, Entrez Gene, TIGR XML). When the entry comes from Genbank it's easy to see where most of the features are, they're in the Feature table section, something like this:

```
 FEATURES            Location/Qualifiers
     source          1..1846
                     /organism="Homo sapiens"
                     /db_xref="taxon:9606"
                     /chromosome="X"
                     /map="Xp11.4"
     gene            1..1846
                     /gene="NDP"
                     /note="ND"
                     /db_xref="LocusID:4693"
                     /db_xref="MIM:310600"
     CDS             409..810
                     /gene="NDP"
                     /note="Norrie disease (norrin)"
                     /codon_start=1
                     /product="Norrie disease protein"
                     /protein_id="NP_000257.1"
                     /db_xref="GI:4557789"
                     /db_xref="LocusID:4693"
                     /db_xref="MIM:310600"
                     /translation="MRKHVLAASFSMLSLLVIMGDTDSKTDSSFIMDSDPRRCMRHHY
                     VDSISHPLYKCSSKMVLLARCEGHCSQASRSEPLVSFSTVLKQPFRSSCHCCRPQTSK
                     LKALRLRCSGGMRLTATYRYILSCHCEECNS"
```

Features in Bioperl are accessed using their tags, either a "primary tag" or a plain "tag". Examples of primary tags and tags in this Genbank entry are shown below. You can see that in this case the primary tag is a means to access the tags and it's the tags that are directly associated with the data from the file.

| Tag name    | Tag type    | Tag value        |
|-------------|-------------|------------------|
| source      | primary tag |                  |
| CDS         | primary tag |                  |
| gene        | primary tag |                  |
| organism    | tag         | Homo sapiens     |
| note        | tag         | ND               |
| protein_id | tag         | NP_000257.1     |
| translation | tag         | MRKHVL...HCEECNS |
| db_xref    | tag         | MIM:310600       |
||

When a Genbank file like the one above is parsed the feature data is converted
into objects, specifically objects. How many? In this case 3, one for each of
the primary tags.

In other parts of the Bioperl documentation one finds discussions of the
"SeqFeature object", but there's more than one kind of these, as we'll see
later, so what is this a reference to? More than likely it's referring to this
same object. Think of it as the default SeqFeature object. Now, should you care
what kind of object is being made? For the most part no, you can write lots of
useful and powerful Bioperl code without ever knowing these specific details.

By the way, how does one know what kind of object one has in hand? Try something
like:

```perl

print ref($seq_object);
# results in "Bio::Seq::RichSeq"

```

The SeqFeature::Generic object uses tag/value pairs to store information, and
the values are always returned as arrays. A simple way to access all the data in
the features of a Seq object would look something like this:

```perl

for my $feat_object ($seq_object->get_SeqFeatures) {
    print "primary tag: ", $feat_object->primary_tag, "\n";

    for my $tag ($feat_object->get_all_tags) {
        print "  tag: ", $tag, "\n";
        for my $value ($feat_object->get_tag_values($tag)) {
            print "    value: ", $value, "\n";
        }
    }
}

```

This bit would print out something like:

```
 primary tag: source
   tag: chromosome
     value: X
   tag: db_xref
     value: taxon:9606
   tag: map
     value: Xp11.4
   tag: organism
     value: Homo sapiens
 primary tag: gene
   tag: gene
     value: NDP
   tag: note
     value: ND
 primary tag: CDS
   tag: codon_start
     value: 1
   tag: db_xref
     value: GI:4557789
     value: LocusID:4693
     value: MIM:310600
   tag: product
     value: Norrie disease protein
   tag: protein_id
     value: NP_000257.1
   tag: translation
     value: MRKHVLAASFSMLSLLVIMGDTDSKTDSSFIMDSDPRRCMRHHYVDSI
            SHPLYKCSSKMVLLARCEGHCSQASRSEPLVSFSTVLKQPFRSSCHCC
            RPQTSKLKALRLRCSGGMRLTATYRYILSCHCEECNS
```

So to retrieve specific values, like all the database identifiers, you could do:

```perl

for my $feat_object ($seq_object->get_SeqFeatures) {
    push @ids, $feat_object->get_tag_values("db_xref") if ($feat_object->has_tag("db_xref"));
}

```

Important: Make sure to include that `if ($feat_object->has_tag("..."))` part,
otherwise you'll get errors when the feature does not have the tag you're
requesting.

One last note on Genbank features. The Bioperl parsers for Genbank and EMBL are
built to respect the specification for the feature tables agreed upon by
Genbank, EMBL, and DDBJ (see the [Feature Table
Definition](http://www.ncbi.nlm.nih.gov/projects/collab/FT) for the details).
Check this page if you're interested in a complete listing and description of
all the Genbank, EMBL, and DDBJ feature tags.

Despite this specification some non-standard feature tags have crept into
Genbank, like "bond". When the Bioperl Genbank parser encounters a non-standard
feature like this it's going to throw a fatal exception. The work-around is to
use `eval{}` so your script doesn't die, something like:

```perl

use Bio::SeqIO;

my $seq_object;
my $seqio_object = Bio::SeqIO->new(-file => $gb_file,
                                   -format => "genbank");

eval { $seq_object = $seqio_object->next_seq; };

# if there's an error

print "Problem in $gb_file. Bad feature perhaps?\n" if $@;

```

Getting Sequences
-----------------

One commonly asked question is "How do I get the sequence of a SeqFeature?" The
answer is "It depends on what you're looking for." If you'd like the sequence of
the parent, the sequence object that the SeqFeature is associated with, then use
`entire_seq()`:

```perl

$seq_object = $feat_object->entire_seq;

```

This doesn't return the parent's sequence directly but rather a object
corresponding to the parent sequence. Now that you have this object you can call
its `seq()` method to get the sequence string, or you could do this all in one
step:

```perl

my $sequence_string = $feat_object->entire_seq->seq;

```

There are 2 other useful methods, `seq()` and `spliced_seq()`. Consider the following Genbank example:

```
 FEATURES             Location/Qualifiers
      source          1..177
                      /organism="Mus musculus"
                      /mol_type="genomic DNA"
                      /db_xref="taxon:10090"
      tRNA            join(103..111,121..157)
                      /gene="Phe-tRNA"
```

To get the sequence string from the start to the end of the tRNA feature use
`seq()`. To get the spliced sequence string, accounting for the start and end
locations of each sub-sequence, use `spliced_seq()`. Here are the methods and
the corresponding example coordinates:

| Method         | Coordinates       |
|----------------|-------------------|
| entire_seq()  | 1..177            |
| seq()          | 103..157          |
| spliced_seq() | 103..111,121..157 |
||

It's not unusual for a Genbank file to have multiple CDS or gene features (and
recall that 'CDS' and 'gene' are common primary tags in Genbank format), each
with a number of tags, like 'note', 'protein_id', or 'product'. How can we get,
say, the nucleotide sequences and gene names from all these CDS features? By
putting all of this together we arrive at something like:

```perl

use Bio::SeqIO;

my $seqio_object = Bio::SeqIO->new(-file => $gb_file);
my $seq_object = $seqio_object->next_seq;

for my $feat_object ($seq_object->get_SeqFeatures) {
    if ($feat_object->primary_tag eq "CDS") {
        print $feat_object->spliced_seq->seq,"\n";
        # e.g. 'ATTATTTTCGCTCGCTTCTCGCGCTTTTTGAGATAAGGTCGCGT...'
        
        if ($feat_object->has_tag('gene')) {
            for my $val ($feat_object->get_tag_values('gene')) {
                print "gene: ",$val,"\n";
                # e.g. 'NDP', from a line like '/gene="NDP"'
            }
        }
    }
}

 ``

Compact Code
------------

Many people wouldn't write code in the rather deliberate style used above. The
following is more compact code that gets all the features with a primary tag of
'CDS', starting with a Genbank file:

```perl

my @cds_features = grep { $_->primary_tag eq 'CDS' } Bio::SeqIO->new(-file => $gb_file)->next_seq->get_SeqFeatures;

```

With this array of SeqFeatures you could do all sorts of useful things, such as find all the values for the 'gene' tags and their corresponding spliced nucleotide sequences and store them in a hash:

```perl

my %gene_sequences = map {$_->get_tag_values('gene'), $_->spliced_seq->seq } @cds_features;

```

Because you're asking for a specific primary tag and tag, 'CDS' and 'gene' respectively, this code would only work when there are features that looked something like this:

```
     CDS             735..182
                     /gene="MG001
                     /codon_start=
                     /product="DNA polymerase III, subunit beta (dnaN)
                     /protein_id="AAC71217.1
                     /translation="MNNVIISNNKIKPHHSYFLIEAKEKEINFYANNEYFSVKCNLN
                     NIDILEQGSLIVKGKIFNDLINGIKEEIITIQEKDQTLLVKTKKTSINLNTINVNEF
                     RIRFNEKNDLSEFNQFKINYSLLVKGIKKIFHSVSNNREISSKFNGVNFNGSNGKEI
                     LEASDTYKLSVFEIKQETEPFDFILESNLLSFINSFNPEEDKSIVFYYRKDNKDSFS
                     EMLISMDNFMISYTSVNEKFPEVNYFFEFEPETKIVVQKNELKDALQRIQTLAQNER
                     FLCDMQINSSELKIRAIVNNIGNSLEEISCLKFEGYKLNISFNPSSLLDHIESFESN
                     INFDFQGNSKYFLITSKSEPELKQILVPSR
```

Location Objects
----------------

There's quite a bit to this idea of location, so much that it probably deserves
its own HOWTO. Another way of saying this is that if this topic interests you
should take a closer look at the modules that are concerned with both Location
and Range such as , , and . The Range object is the simpler of the two, it holds
the "start", "end", and "strand" (1, -1) information for a sequence that is
located on some other sequence, typically a larger one. The Range object can
only describe exact locations.

The Location object is a Range object but it has additional capabilities
designed to handle inexact or "fuzzy" locations, where the "start" and "end" of
a particular sub-sequence themselves have start and end positions, or are not
precisely defined.

Both these objects use methods like `overlaps()`, `contains()`, `union()` and
`intersection()` that act on pairs of Ranges or Locations. The table below is
meant to illustrate some of the modules' descriptive capabilities.

| Type      | Example       |
|-----------|---------------|
| EXACT     | (5..100)      |
| BEFORE    | (<5..100)  |
| AFTER     | (>5..100)  |
| WITHIN    | ((5.10)..100) |
| BETWEEN   | (99^100)      |
| UNCERTAIN | (99.?100)     |
||

One type that might not be self-explanatory is 'WITHIN'. The example means
"starting somewhere between positions 5 and 10, inclusive, and ending at 100".
'BETWEEN' is interesting - the example means "between 99 and 100, exclusive". A
biological example of such a location would be a cleavage site, between two
bases or residues, but not including them.

The UNCERTAIN attribute means what it says, not known. This value is found occasionally in SwissProt features.

In their simplest form the Location and Range objects are used to get or set
start and end positions, getting the positions could look like this:

```
       # polyA_signal    1811..1815 
       #                 /gene="NDP"
       my $start = $feat_object->location->start;
       my $end = $feat_object->location->end;
```

By now you've figured out that the `location()` method returns a Location object - this object has `end()` and `start()` methods.

Another way of describing a feature in Genbank involves multiple start and end
positions. These could be called "split" locations, and a very common example is
the join statement in the CDS feature found in Genbank entries (e.g.
`join(45..122,233..267)`). This calls for a specialized object, , which is a
container for Location objects:

```perl

for my $feature ($seqobj->top_SeqFeatures){

    if ( $feature->location->isa('Bio::Location::SplitLocationI')
         && $feature->primary_tag eq 'CDS' )  {
         
        for my $location ( $feature->location->sub_Location ) {
            print $location->start . ".." . $location->end . "\n";
        }

    }
}

```

The Species Object
------------------

'''NOTE''' : Future use of beyond release 1.6 is deprecated. We will be
switching to a new, more reliable system based on and anticipate updating these
notes soon.

Some data in a Genbank file is accessible both as a feature and through a
specialized object. Taxonomic information on a sequence, below, can be accessed
through a Species object as well as a value to the "organism" tag, and you'll
get more information from the object. The taxonomic information for sequence
looks like this in GenBank format:

```
 SOURCE      human.
   ORGANISM  Homo sapiens
             Eukaryota; Metazoa; Chordata; Craniata; Vertebrata; Euteleostomi;
             Mammalia; Eutheria; Primates; Catarrhini; Hominidae; Homo.
```

To access this data you'll need to get a Species object from the Sequence object, and then use its methods:

```perl

# legible and long

my $species_object = $seq_object->species; my $species_string = $species_object->node_name;

# Perlish

my $species_string = $seq_object->species->node_name;

# either way, $species_string is "Homo sapiens"

<!-- -->

# get all taxa from the ORGANISM section in an array

my @classification = $seq_object->species->classification;

# "sapiens Homo Hominidae Catarrhini Primates Eutheria Mammalia
2.  Euteleostomi Vertebrata Craniata Chordata Metazoa Eukaryota"

```

The reason that ORGANISM isn't treated only as a plain tag is that there are a variety of things one would want to do with taxonomic information, so returning just an array wouldn't suffice. See the documentation on for more information on its methods.

Getting the Annotations
-----------------------

There's still quite a bit of data left in our Genbank files that's not in SeqFeature objects, and much of it is parsed into Annotation objects. Annotations, if you recall, are those values that are assigned to a sequence that have no specific location on that sequence. In order to get access to these objects we will get an AnnotationCollection object, which is exactly what it sounds like:

```perl

my $io = Bio::SeqIO->new(-file => $file, -format => "genbank" ); my $seq_obj = $io->next_seq; my $anno_collection = $seq_obj->annotation;

```

Now we can access each Annotation in the AnnotationCollection object. The Annotation objects can be retrieved in arrays:

```perl

for my $key ( $anno_collection->get_all_annotation_keys ) {

`  my @annotations = $anno_collection->get_Annotations($key);`
`  for my $value ( @annotations ) {`
`     print "tagname : ", $value->tagname, "\`

";

`     # $value is an Bio::Annotation, and also has an "as_text" method`
`     print "  annotation value: ", $value->display_text, "\`

";

`  }`

}

```

It turns out the value of `$key`, above, and `$value->tagname` are the same. The code will print something like:

    tagname : comment
      annotation value: Comment: REVIEWED REFSEQ: This record has been curated by NCBI staff. The reference sequence was derived from X65882.1. Summary: NDP is the genetic locus identified as harboring mutations that result in Norrie disease.
    tagname : reference
      annotation value: Reference: The molecular biology of Norrie's disease
    tagname : date_changed
      annotation value: Value: 31-OCT-2000

If you only wanted a specific annotation, like COMMENT, you can use the tagname as an argument:

```perl

`      my @annotations = $anno_collection->get_Annotations('comment');`

```

And if you'd simply like all of the Annotations, regardless of key, you can do this:

```perl

`      my @annotations = $anno_collection->get_Annotations();`

```

The following is a table of some of the common Annotations, their keys in Bioperl, and what they're derived from in Genbank files:

| GenBank Text | Key                  | Object Type | Note                        |
|--------------|----------------------|-------------|-----------------------------|
| COMMENT      | comment              | Comment     |                             |
| SEGMENT      | segment              | SimpleValue | e.g. "1 of 2"               |
| ORIGIN       | origin               | SimpleValue | e.g. "X Chromosome."        |
| REFERENCE    | reference            | Reference   |                             |
| INV          | date_changed        | SimpleValue | e.g. "08-JUL-1994"          |
| KEYWORDS     | keyword              | SimpleValue |                             |
| ACCESSION    | secondary_accession | SimpleValue | 2nd of 2 accessions         |
| DBSOURCE     | dblink               | DBLink      | Link to entry in a database |
||

Some Annotation objects, like Reference, make use of a `hash_tree()` method, which returns a hash reference. This is a more thorough way to look at the actual values than the `display_text()` method used above. For example, `display_text()` for a Reference object is only going to return the title of the reference, whereas the keys of the hash from `hash_tree()` will be "title", "authors", "location", "medline", "start", and "end".

```perl

if ($value->tagname eq "Reference") {

`  my $hash_ref = $value->hash_tree;`
`  for my $key (keys %{$hash_ref}) {`
`     print $key,": ",$hash_ref->{$key},"\`

";

`  }`

}

```

Which yields:

`authors: Meitinger,T., Meindl,A., Bork,P., Rost,B., Sander,C., Haasemann,M. and Murken,J.`
`location: Nat. Genet. 5 (4), 376-380 (1993) `
`medline: 94129616`
`title: Molecular modelling of the Norrie disease protein predicts a cystine knot`
`growth factor tertiary structure`
`end: 1846`
`start: 1`

Other Annotation objects, like SimpleValue, also have a `hash_tree()` method but the hash isn't populated with data and `display_text()` will suffice.

The simplest bits of Genbank text, like KEYWORDS, end up in these objects, the COMMENT ends up in a object, and references are transformed into objects. Some of these specialized objects will have specialized methods. Take the object, for example:

```perl

if ($value->tagname eq "reference") {

`   print "author: ",$value->authors(), "\`

"; }

```

There's also `title()`, `publisher()`, `medline()`, `editors()`, `database()`, `pubmed()` and a number of other methods.

Directly From the Sequence Object
---------------------------------

This is just a reminder that some of the "annotation" data in your sequence files can be accessed directly, without looking at SeqFeatures or Annotations.

For example, if the Sequence object in hand is a object then here are some useful methods:

| Method                     | Returns |
|----------------------------|---------|
| get_secondary_accessions | array   |
| keywords                   | array   |
| get_dates                 | array   |
| seq_version               | string  |
| pid                        | string  |
| division                   | string  |
||

These objects are created automatically when you use to read from EMBL, GenBank, GAME, Chado XML, TIGR XML, Locuslink, BSML, KEGG, Entrez Gene, and SwissProt sequence files. However, it's not guaranteed that each of these formats will supply data for all of the methods above.

Other Sequence File Formats
---------------------------

It is worth mentioning other sequence file formats. The table below shows what sorts of objects, Annotation or SeqFeature, you'll get when you parse other sequence formats using .

| Format      | SeqIO Name | SeqFeature | Annotation |
|-------------|------------|------------|------------|
| Genbank     | genbank    | yes        | yes        |
| EMBL        | embl       | yes        | yes        |
| GAME        | game       | yes        | no         |
| Chado XML   | chadoxml   | yes        | yes        |
| TIGR XML    | tigr       | yes        | yes        |
| Locuslink   | locuslink  | no         | yes        |
| BSML        | bsml       | yes        | yes        |
| KEGG        | kegg       | yes        | yes        |
| SwissProt   | swiss      | yes        | yes        |
| Entrez Gene | entrezgene | no         | yes        |
||

How does one find out what data is in which object in these formats? In general the individual module documentation is not going to provide all the answers, you'll need to do some investigation yourself. Let's use an approach we used earlier to dissect a Locuslink entry in a file, "148.ll". Here's the file:

`LOCUSID: 148`
`LOCUS_CONFIRMED: yes`
`LOCUS_TYPE: gene with protein product, function known or inferred `
`ORGANISM: Homo sapiens`
`STATUS: REVIEWED `
`NM: NM_000680|4501960|na`
`NP: NP_000671|4501961`
`PROT: AAA93114|409029`
`ACCNUM: M11313|177869|na|na|na`
`TYPE: p`
`PROT: P35348|1168246`
`OFFICIAL_SYMBOL: ADRA1A`
`OFFICIAL_GENE_NAME: adrenergic, alpha-1A-, receptor`
`ALIAS_SYMBOL: ADRA1C`
`SUMMARY: Summary: Alpha-1-ARs are members of the GPCR superfamily.`
`CHR: 8`
`STS: SGC35557|8|8124|na|seq_map|epcr `
`COMP: 10090|Adra1a|14|14  cM|11549|8|ADRA1A|ncbi_mgd`
`ALIAS_PROT: adrenergic, alpha-1C-, receptor`
`BUTTON: unigene.gif`
`LINK: http://www.ncbi.nlm.nih.gov/UniGene/clust.cgi?ORG=Hs&CID=52931`
`UNIGENE: Hs.52931`
`OMIM: 104221`
`MAP: 8p21-p11.2|RefSeq|C|`
`MAPLINK: default_human_gene|ADRA1A`
`GO: cellular component|integral to plasma membrane|P|`[`GO:0005887|Proteome|8396931`](GO:0005887%7CProteome%7C8396931)

First collect all the annotations:

```perl

use Bio::SeqIO;

my @annotations = Bio::SeqIO->new(-file => "148.ll", -format => "locuslink")->next_seq->annotation->get_Annotations;

```

And from this array of Annotations let's extract a hash containing the `as_text` strings as keys and the concatenated tagnames and object types as values:

```perl

`      my %tagname_type = map {$_->as_text,($_->tagname . " " . ref($_)) } @annotations;`

```

The contents of the `%tagname_type` hash can be represented in table form, below.

| `as_text`                                                             | `tagname`            | `ref`                         |
|-----------------------------------------------------------------------|----------------------|-------------------------------|
| Direct database link to AAA93114 in database GenBank                  | dblink               | Bio::Annotation::DBLink       |
| Value: http://www.ncbi.nlm.nih.gov/UniGene/clust.cgi?ORG=Hs&CID=52931 | URL                  | Bio::Annotation::SimpleValue  |
| Value: 8                                                              | CHR                  | Bio::Annotation::SimpleValue  |
| Direct database link to NP_000671 in database RefSeq                 | dblink               | Bio::Annotation::DBLink       |
| Direct database link to SGC35558 in database STS                      | dblink               | Bio::Annotation::DBLink       |
| Comment: Summary: Alpha-1-ARs are members of the GPCR superfamily     | comment              | Bio::Annotation::Comment      |
| Value: adrenergic, alpha-1A-, receptor                                | OFFICIAL_GENE_NAME | Bio::Annotation::SimpleValue  |
| Value: ADRA1C                                                         | ALIAS_SYMBOL        | Bio::Annotation::SimpleValue  |
| Value: adrenergic, alpha -1A-, receptor                               | ALIAS_PROT          | Bio::Annotation::SimpleValue  |
| Direct database link to NM_000680 in database RefSeq                 | dblink               | Bio::Annotation::DBLink       |
| Value: ADRA1A                                                         | OFFICIAL_SYMBOL     | Bio::Annotation::SimpleValue  |
| Direct database link to SGC35557 in database STS                      | dblink               | Bio::Annotation::DBLink       |
| Value: 8p21-p11.2                                                     | MAP                  | Bio::Annotation::SimpleValue  |
| Direct database link to 104221 in database MIM                        | dblink               | Bio::Annotation::DBLink       |
| Direct database link to D8S2033 in database STS                       | dblink               | Bio::Annotation::DBLink       |
| Direct database link to none in database GenBank                      | dblink               | Bio::Annotation::DBLink       |
| cellular component|integral to plasma membrane|<GO:0005887>           | cellular component   | Bio::Annotation::OntologyTerm |
| Direct database link to Hs.52931 in database UniGene                  | dblink               | Bio::Annotation::DBLink       |
| Direct database link to M11313 in database GenBank                    | dblink               | Bio::Annotation::DBLink       |
| Direct database link to P35348 in database GenBank                    | dblink               | Bio::Annotation::DBLink       |
||

The output from the script shows that Locuslink Annotations come in a variety of types, including DBLink, OntologyTerm, Comment, and SimpleValue. In order to extract the exact value you want, as opposed to the one returned by the `as_text` method, you'll need to find the desired method in the documentation for the Annotation in question.

If you were only interested in a certain type of Annotation you could retrieve it efficently with something like this:

```perl

`         @ontology_terms = map { $_->isa("Bio::Ontology::TermI"); } $seq_object->get_Annotations();`

```

To completely parse these sequence formats you may also need to use methods that don't have anything to do with Features or Annotations ''per se''. For example, the `display_id` method returns the LOCUS name of a Genbank entry or the ID from a SwissProt file. The `desc()` method will return the DEFINITION line of a Genbank file or the DE field in a SwissProt file. Again, this is a situation where you may have to examine a module, probably a SeqIO::\* module, to find out more of the details.

Building Your Own Sequences
---------------------------

We've taken a look at getting data from SeqFeature and Annotation objects, but what about creating these objects when you already have the data? The object is probably the best SeqFeature object for this purpose, in part because of its flexibility. Let's assume we have a sequence that has an interesting sub-sequence, going from position 10 to 22 on the + or 1 or [sense] strand.

```perl

use Bio::SeqFeature::Generic;

# create the feature with some data, evidence and a note

my $feat = new Bio::SeqFeature::Generic(-start => 10,

`                                       -end         => 22,`
`                                       -strand      => 1,`
`                                       -primary_tag => 'TATA_signal',`
`                                       -tag => {evidence => 'predicted',`
`                                                note     => 'TATA box' } );`

```

The SeqFeature::Generic object offers the user a "tag system" for addition of data that's not explicitly accounted for by its methods, that's what the "-tag" is for, above. Since the value passed to "-tag" could be any kind of scalar, like a reference, it's clear that this approach should be able to handle just about any sort of data.

You can build on the Feature as well. Here we'll add some Annotations to the newly-created Feature:

```perl

$feat->add_tag_value("match1","PF000123 e-7.2"); $feat->add_tag_value("match2","PF002534 e-3.1");

my @tags = $feat->get_all_tags; for my $tag (@tags) {

`  for my $val ( $feat->get_tag_values($tag) ) {`
`     print $tag,":",$val,"\`

";

`  }`

}

```

This prints out:

` evidence:predicted`
` match1:PF000123 e-7.2`
` match2:PF002534 e-3.1`
` note:TATA box`

NOTE: If you need to add a tag that don't have any value when printed (like /pseudo, /trans_splicing, or /environmental_sample), you can use the special value '_no_value' to make BioPerl print the tag without any associated value:

```perl

$feat->add_tag_value("pseudo","_no_value");

```

Once the feature and its annotations are created it can be associated with a sequence:

```perl

use Bio::Seq;

# create a simple Sequence object

my $seq_obj = Bio::Seq->new(-seq => "attcccccttataaaattttttttttgaggggtggg",

`                           -display_id => "BIO52" );`

# then add the feature we've created to the sequence

$seq_obj->add_SeqFeature($feat);

```

The `add_SeqFeature()` method will also accept an array of SeqFeature objects.

What if you wanted to add an Annotation to a sequence? You'll create the Annotation object, add data to it, create an object, add the Annotation to the AnnotationCollection along with a tag, and then add the AnnotationCollection to the sequence object:

```perl

use Bio::Annotation::Collection; use Bio::Annotation::Comment;

my $comment = Bio::Annotation::Comment->new; $comment->text("This looks like a good TATA box"); my $coll = new Bio::Annotation::Collection; $coll->add_Annotation('comment',$comment); $seq_obj->annotation($coll);

```

Now let's examine what we've created by writing the contents of `$seq_obj` to a Genbank file called "test.gb". We should see a sequence, an Annotation associated with the sequence, a sequence Feature, and Annotations associated with the Feature:

```perl

use Bio::SeqIO; my $io = Bio::SeqIO->new(-format => "genbank", -file => ">test.gb" ); $io->write_seq($seq_obj);

```

''Voila!''

`test.gb` now reads:

` LOCUS       BIO52                    36 bp    dna     linear   UNK`
` ACCESSION   unknown`
` COMMENT     This looks like a good TATA box`
` FEATURES             Location/Qualifiers`
`      TATA_signal     10..22`
`                      /match2="PF002534 e-3.1"`
`                      /match1="PF000123 e-7.2"`
`                      /evidence=predicted`
`                      /note="TATA box"`
`                      /pseudo`
` ORIGIN`
`         1 attccccctt ataaaatttt ttttttgagg ggtggg`
` //`

Customizing Sequence Object Construction
----------------------------------------

When you don't need access to the complete set of annotations in a set of potentially rich sequence database entries, it is possible to configure the SeqIO parser to ignore certain sections of the sequence record. For example, let's say you need to crunch through all of Genbank, collecting stats about the number of molecule types per species. In this case, you don't care about the sequence or features contained in each entry. Here's how to tell the parser to ignore these things:

```perl

my $seqin = Bio::SeqIO->new( -fh=> \*STDIN, -format=> 'genbank' ); my $builder = $seqin->sequence_builder(); $builder->want_all(1); $builder->add_unwanted_slot('seq','features','annotation');

# Then go and use the SeqIO object as normal

while(my $seq = $seqin->next_seq()) {

`   # do something`

}

```

This also skips annotations, which includes things like comments, references, and dblinks. This can speed up parsing by a factor of two or better, depending on the complexity of the sequence records.

As of version 1.5.1 this ability to customize your Sequence objects is only available for .

See [Bio::Seq::SeqBuilder] and [HOWTO:SeqIO] for more documentation on this.

Additional Information
----------------------

If you would like to learn about representing sequences and features in graphical form take a look at the [Graphics HOWTO]. The documentation for each of the individual SeqFeature, Range, Location and Annotation modules is also very useful, here's a list of them. If you have questions or comments that aren't addressed herein then write the Bioperl community at [bioperl-l@bioperl.org](http://bioperl.open-bio.org/wiki/Mailing_lists).

'''SeqFeature Modules'''

-   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   -   

'''Annotation Modules'''

-   -   -   -   -   -   -   -   -   -   -   -   

'''Location Modules'''

-   -   -   -   -   -   -   -   -   -   -   -   

'''Range Modules'''

-   -   

Acknowledgements
----------------

Thanks to Steven Lembark for comments and neat code discussions.'


