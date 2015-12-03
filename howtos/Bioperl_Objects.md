### Authors

Peter Schattner(Peter_Schattner "wikilink")

### Abstract

This is a HOWTO that talks about many of the common and uncommon Bioperl objects that represent sequences.

### Brief descriptions

This section describes various Bioperl sequence objects. Many people using Bioperl will never know, or need to know, what kind of sequence object they are using. This is because the module creates exactly the right type of object when given a file or a filehandle or a string.

is the central sequence object in bioperl. When in doubt this is probably the object that you want to use to describe a DNA, RNA or protein sequence in bioperl. Most common sequence manipulations can be performed with Seq.

Seq objects may be created for you automatically when you read in a file containing sequence data using the SeqIO object. In addition to storing its identification labels and the sequence itself, a Seq object can store multiple annotations and associated "sequence features", such as those contained in most Genbank and EMBL sequence files. This capability can be very useful - especially in development of automated genome annotation systems.

On the other hand, if you need a script capable of simultaneously handling hundreds or thousands sequences at a time, then the overhead of adding annotations to each sequence can be significant. For such applications, you will want to use the PrimarySeq object. PrimarySeq is basically a stripped-down version of Seq. It contains just the sequence data itself and a few identifying labels (id, accession number, alphabet = dna, rna, or protein), and no features. For applications with hundreds or thousands or sequences, using PrimarySeq objects can significantly speed up program execution and decrease the amount of RAM the program requires. See for more details.

objects store additional annotations beyond those used by standard Seq objects. RichSeq objects are created automatically when Genbank, EMBL, or Swissprot format files are read by SeqIO.

objects are used to manipulate sequences with quality data, like those produced by phred.

What is called a object for historical reasons might be more appropriately called an "AlignedSeq" object. It is a Seq object which is part of a multiple sequence alignment. It has start and end positions indicating from where in a larger sequence it may have been extracted. It also may have gap symbols corresponding to the alignment to which it belongs. It is used by the alignment object SimpleAlign and other modules that use SimpleAlign objects (e.g. AlignIO.pm, pSW.pm).

In general you don't have to worry about creating LocatableSeq objects because they will be made for you automatically when you create an alignment (using pSW, Clustalw, Tcoffee, Lagan, or bl2seq) or when you input an alignment data file using AlignIO. However if you need to input a sequence alignment by hand (e.g. to build a SimpleAlign object), you will need to input the sequences as LocatableSeqs. Other sources of information include , , , and .

The object is also a type of Bioperl Seq object. RelSegment objects are useful when you want to be able to manipulate the origin of the genomic coordinate system. This situation may occur when looking at a sub-sequence (e.g. an exon) which is located on a longer underlying sequence such as a chromosome or a contig. Such manipulations may be important, for example when designing a graphical genome browser. If your code may need such a capability, look at the documentation which describes this feature in detail.

A object is a special type of Seq object used for handling very long sequences (e.g. &gt; 100 MB).

A object is another specialized object for storing sequence data. LiveSeq addresses the problem of features whose location on a sequence changes over time. This can happen, for example, when sequence feature objects are used to store gene locations on newly sequenced genomes - locations which can change as higher quality sequencing data becomes available. Although a LiveSeq object is not implemented in the same way as a Seq object, LiveSeq does implement the SeqI interface (see below). Consequently, most methods available for Seq objects will work fine with LiveSeq objects. Section "III.7.4" and contain further discussion of LiveSeq objects.

objects are Seq "interface objects". They are used to ensure bioperl's compatibility with other software packages. SeqI and other interface objects are not likely to be relevant to the casual Bioperl user.

### LargeSeq

Very large sequences present special problems to automated sequence-annotation storage and retrieval projects. Bioperl's LargeSeq object addresses this situation.

A object is a SeqI compliant object that stores a sequence as a series of files in a temporary directory. The aim is to enable storing very large sequences (e.g. &gt; 100 MBases) without running out of memory and, at the same time, preserving the familiar Bioperl Seq object interface. As a result, from the user's perspective, using a LargeSeq object is almost identical to using a Seq object. The principal difference is in the format used in the SeqIO calls. Another difference is that the user must remember to only read in small chunks of the sequence at one time. These differences are illustrated in the following code:

<perl> $seqio = new Bio::SeqIO(-format =&gt; 'largefasta',

`                       -file   => 't/data/genomic-seq.fasta');`

$pseq = $seqio-&gt;next\_seq(); $plength = $pseq-&gt;length(); $last\_4 = $pseq-&gt;subseq($plength-3,$plength); \# this is OK </perl>

On the other hand, the next statement would probably cause the machine to run out of memory:

<perl> $lots\_of\_data = $pseq-&gt;seq(); \# NOT OK for a large LargeSeq object </perl>

See for more.

### LiveSeq

Data files with sequences that are frequently being updated present special problems to automated sequence-annotation storage and retrieval projects. Bioperl's LiveSeq object is designed to address this situation.

The LiveSeq object addresses the need for a sequence object capable of handling sequence data that may be changing over time. In such a sequence, the precise locations of features along the sequence may change. LiveSeq deals with this issue by re-implementing the sequence object internally as a "double linked chain." Each element of the chain is connected to other two elements (the PREVious and the NEXT one). There is no absolute position like in an array, hence if positions are important, they need to be computed (methods are provided). Otherwise it's easy to keep track of the elements with their "LABELs". There is one LABEL (think of it as a pointer) to each ELEMENT. The labels won't change after insertions or deletions of the chain. So it's always possible to retrieve an element even if the chain has been modified by successive insertions or deletions.

Although the implementation of the LiveSeq object is novel, its bioperl user interface is unchanged since LiveSeq implements a PrimarySeqI interface (recall PrimarySeq is the subset of Seq without annotations or SeqFeatures. Consequently syntax for using LiveSeq objects is familiar although a modified version of SeqIO called needs to be used to actually load the data, e.g.:

<perl> $loader = Bio::LiveSeq::IO::BioPerl-&gt;load(-db =&gt; "EMBL",

`                                         -file => "t/data/factor7.embl");`

$gene = $loader-&gt;gene2liveseq(-gene\_name =&gt; "factor7"); $id = $gene-&gt;get\_DNA-&gt;display\_id ; $maxstart = $gene-&gt;maxtranscript-&gt;start; </perl>

See for more details.

### Mutator and Mutation

A Mutation object allows for a basic description of a sequence change in the DNA sequence of a gene. The Mutator object takes in mutations, applies them to a LiveSeq gene and returns a set of Bio::Variation objects describing the net effect of the mutation on the gene at the DNA, RNA and protein level.

The objects in the Bio::Variation and Bio::LiveSeq directories were originally designed for the "Computational Mutation Expression Toolkit" project at European Bioinformatics Institute (EBI). The result of using them to mutate a gene is a holder object, 'SeqDiff', that can be printed out or queried for specific information. For example, to find out if restriction enzyme changes caused by a mutation are exactly the same in DNA and RNA sequences, we can write:

<perl> use Bio::LiveSeq::IO::BioPerl; use Bio::LiveSeq::Mutator; use Bio::LiveSeq::Mutation;

$loader = Bio::LiveSeq::IO::BioPerl-&gt;load(-file =&gt; "$filename"); $gene = $loader-&gt;gene2liveseq(-gene\_name =&gt; $gene\_name); $mutation = new Bio::LiveSeq::Mutation(-seq =&gt; 'G',

`                                       -pos => 100 );`

$mutate = Bio::LiveSeq::Mutator-&gt;new(-gene =&gt; $gene,

`                                    -numbering => "coding"  );`

$mutate-&gt;add\_Mutation($mutation); $seqdiff = $mutate-&gt;change\_gene(); $DNA\_re\_changes = $seqdiff-&gt;DNAMutation-&gt;restriction\_changes; $RNA\_re\_changes = $seqdiff-&gt;RNAChange-&gt;restriction\_changes; $DNA\_re\_changes eq $RNA\_re\_changes or print "Different!\\n"; </perl> For a complete working script, see the change\_gene.pl script in the examples/liveseq directory. For more details on the use of these objects see and as well as the original documentation for the "Computational Mutation Expression Toolkit" project ( <http://www.ebi.ac.uk/mutations/toolkit/> ).

### SeqWithQuality

SeqWithQuality objects are used to describe sequences with very specific annotations - that is, base quality annotations. Base quality information is important for documenting the reliability of base calls, typically made by sequencing machines. The quality data is contained within a object.

A SeqWithQuality object is created automatically when phred output, a \*phd file, is read by SeqIO, e.g.

<perl> $seqio = Bio::SeqIO-&gt;new(-file=&gt;"my.phd",-format=&gt;"phd");

1.  or just 'Bio::SeqIO-&gt;new(-file=&gt;"my.phd")'

$seqWithQualObj = $seqio-&gt;next\_seq; </perl>

Or, you can make a SeqWithQuality object yourself, e.g.

<perl>

1.  first, make a PrimarySeq object

$seqobj = Bio::PrimarySeq-&gt;new( -seq =&gt; 'atcgatcg',

`                               -id  => 'GeneFragment-12',`
`                               -accession_number => 'X78121',`
`                               -alphabet => 'dna');`

1.  now make a PrimaryQual object

$qualobj = Bio::Seq::PrimaryQual-&gt;new(-qual =&gt; '10 20 30 40 50 50 20 10',

`                                     -id   => 'GeneFragment-12',`
`                                     -accession_number => 'X78121',`
`                                     -alphabet => 'dna');`

1.  now make the SeqWithQuality object

$swqobj = Bio::Seq::SeqWithQuality-&gt;new(-seq =&gt; $seqobj,

`                                       -qual => $qualobj);`

1.  Now we access the sequence with quality object

$swqobj-&gt;id(); \# the id of the SeqWithQuality object may not match the

`              # id of the sequence or of the quality`

$swqobj-&gt;seq(); \# the sequence of the SeqWithQuality object $swqobj-&gt;qual(); \# the quality of the SeqWithQuality object </perl>

See for a detailed description of the methods, , and .

### GFF and Bio:DB:GFF

Another format for transmitting machine-readable sequence-feature data is the Genome Feature Format (GFF). This file type is well suited to sequence annotation because it allows the ability to describe entries in terms of parent-child relationships (see <http://www.sanger.ac.uk/software/GFF> for details). Bioperl includes a parser for converting between GFF files and SeqFeature objects. Typical syntax looks like:

<perl> $gffio = Bio::Tools::GFF-&gt;new(-fh =&gt; \\\*STDIN, -gff\_version =&gt; 2);

` # loop over the input stream`

while ($feature = $gffio-&gt;next\_feature()) {

` # do something with feature`

} $gffio-&gt;close(); </perl> Further information can be found at . Also see examples/tools/gff2ps.pl, examples/tools/gb\_to\_gff.pl, and the scripts in scripts/Bio-DB-GFF. Note: this module shouldn't be confused with the module which is for implementing relational databases when using bioperl-db.

### StructureI and Structure::IO

A Structure object can be created from one or more 3D structures represented in Protein Data Bank, or pdb, format (see <http://www.pdb.org> for details).

StructureIO objects allow access to a variety of related Bio:Structure objects. An Entry object consist of one or more Model objects, which in turn consist of one or more Chain objects. A Chain is composed of Residue objects, which in turn consist of Atom objects. There's a wealth of methods, here are just a few: <perl> $structio = Bio::Structure::IO-&gt;new( -file =&gt; "1XYZ.pdb"); $struc = $structio-&gt;next\_structure; \# a Bio::Structure::Entry object $pseq = $struc-&gt;seqres; \# a Bio::PrimarySeq object, thus $pseq-&gt;subseq(1,20); \# returns a sequence string @atoms = $struc-&gt;get\_atoms($res); \# Atom objects, given a Residue @xyz = $atom-&gt;xyz; \# the 3D coordinates of the atom </perl> This code shows how to start with a PDB file and obtain Entry, Chain, Residue, and Atom objects: <perl> use Bio::Structure::IO; use strict;

my $structio = Bio::Structure::IO-&gt;new(-file =&gt; $file); my $struc = $structio-&gt;next\_structure;

for my $chain ($struc-&gt;get\_chains) {

`  my $chainid = $chain->id;`
`  # one-letter chaincode if present, 'default' otherwise`
`  for my $res ($struc->get_residues($chain)) {`
`     my $resid = $res->id;`
`     # format is 3-lettercode - dash - residue number, e.g. PHE-20`
`     my $atoms = $struc->get_atoms($res);`
`     # actually a list of atom objects, used here to get a count`
`     print join "\t", $chainid,$resid,$atoms,"\n";`
`  }`

} </perl>

See , , , , , and the examples/structure directory for more information.

### Map::MapI and MapIO

These are objects for manipulating genetic maps. Bioperl Map objects can be used to describe any type of biological map data including genetic maps, STS maps etc. Map I/O is performed with the MapIO object which works in a similar manner to the SeqIO, SearchIO and similar I/O objects described previously. In principle, Map I/O with various map data formats can be performed. However currently only mapmaker format is supported. Manipulation of genetic map data with Bioperl Map objects might look like this:

<perl> $mapio = new Bio::MapIO(-format =&gt; 'mapmaker', -file =&gt; $mapfile); $map = $mapio-&gt;next\_map; \# get a map $maptype = $map-&gt;type ; for $marker ( $map-&gt;each\_element ) {

` $marker_name = $marker->name ;  # get the name of each map marker`

} </perl>

See and for more information.
