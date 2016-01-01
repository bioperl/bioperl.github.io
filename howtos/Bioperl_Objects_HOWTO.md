---
title: "BioPerl Objects HOWTO"
layout: howto
---

## Authors

Peter Schattner

## Abstract

This is a HOWTO that talks about many of the common and uncommon Bioperl objects that represent sequences.

## Brief descriptions

This section describes various Bioperl sequence objects. Many people using Bioperl will never know, or need to know, what kind of sequence object they are using. This is because the [Bio::SeqIO](http://metacpan.org/pod/Bio::SeqIO) module creates exactly the right type of object when given a file or a filehandle or a string.

[Bio::Seq](http://metacpan.org/pod/Bio%3A%3ASeq) is the central sequence object in bioperl. When in doubt this is probably the object that you want to use to describe a DNA, RNA or protein sequence in bioperl. Most common sequence manipulations can be performed with Seq.

Seq objects may be created for you automatically when you read in a file containing sequence data using the [Bio::SeqIO](http://metacpan.org/pod/Bio::SeqIO) object. In addition to storing its identification labels and the sequence itself, a Seq object can store multiple annotations and associated "sequence features", such as those contained in most Genbank and EMBL sequence files. This capability can be very useful - especially in development of automated genome annotation systems.

[Bio::Seq::RichSeq](http://metacpan.org/pod/Bio::Seq::RichSeq) objects store additional annotations beyond those used by standard Seq objects. RichSeq objects are created automatically when Genbank, EMBL, or Swissprot format files are read by SeqIO (see the [Feature and Annotations HOWTO](Features_and_Annotations_HOWTO.html) for more information).

On the other hand, if you need a script capable of simultaneously handling hundreds or thousands sequences at a time, then the overhead of adding features and annotations to each sequence can be significant. For such applications, you may want to use the [Bio::PrimarySeq](http://metacpan.org/pod/Bio::PrimarySeq) object. PrimarySeq is basically a stripped-down version of Seq. It contains just the sequence data itself and a few identifying labels (`id, accession number, alphabet = dna, rna, or protein`), and no features. For applications with hundreds or thousands or sequences, using PrimarySeq objects can significantly speed up program execution and decrease the amount of RAM the program requires. See [Bio::PrimarySeq](http://metacpan.org/pod/Bio%3A%3APrimarySeq) for more details.

[Bio::Seq::SeqWithQuality](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3ASeqWithQuality) objects are used to manipulate sequences with quality data, like those produced by phred.

What is called a [Bio::LocatableSeq](http://metacpan.org/pod/Bio%3A%3ALocatableSeq) object for historical reasons might be more appropriately called an "AlignedSeq" object. It is a Seq object which is part of a multiple sequence alignment. It has start and end positions indicating from where in a larger sequence it may have been extracted. It also may have gap symbols corresponding to the alignment to which it belongs. It is used by the alignment object SimpleAlign and other modules that use SimpleAlign objects (e.g. AlignIO.pm, pSW.pm).

In general you don't have to worry about creating LocatableSeq objects because they will be made for you automatically when you create an alignment (using pSW, Clustalw, Tcoffee, Lagan, or bl2seq) or when you input an alignment data file using AlignIO. However if you need to input a sequence alignment by hand (e.g. to build a SimpleAlign object), you will need to input the sequences as LocatableSeqs. Other sources of information include [Bio::LocatableSeq](http://metacpan.org/pod/Bio%3A%3ALocatableSeq), [Bio::SimpleAlign](http://metacpan.org/pod/Bio%3A%3ASimpleAlign), [Bio::AlignIO](http://metacpan.org/pod/Bio%3A%3AAlignIO), and [Bio::Tools::pSW](http://metacpan.org/pod/Bio%3A%3ATools%3A%3ApSW).

The [Bio::DB::GFF::RelSegment](http://metacpan.org/pod/Bio%3A%3ADB%3A%3AGFF%3A%3ARelSegment) object is also a type of Bioperl Seq object. RelSegment objects are useful when you want to be able to manipulate the origin of the genomic coordinate system. This situation may occur when looking at a sub-sequence (e.g. an exon) which is located on a longer underlying sequence such as a chromosome or a contig. Such manipulations may be important, for example when designing a graphical genome browser. If your code may need such a capability, look at the documentation [Bio::DB::GFF::RelSegment](http://metacpan.org/pod/Bio%3A%3ADB%3A%3AGFF%3A%3ARelSegment) which describes this feature in detail.

A [Bio::Seq::LargeSeq](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3ALargeSeq) object is a special type of Seq object used for handling very long sequences (e.g. > 100 MB).

A [Bio::LiveSeq::IO::BioPerl](http://metacpan.org/pod/Bio%3A%3ALiveSeq%3A%3AIO%3A%3ABioPerl) object is another specialized object for storing sequence data. LiveSeq addresses the problem of features whose location on a sequence changes over time. This can happen, for example, when sequence feature objects are used to store gene locations on newly sequenced genomes - locations which can change as higher quality sequencing data becomes available. Although a LiveSeq object is not implemented in the same way as a Seq object, LiveSeq does implement the SeqI interface (see below). Consequently, most methods available for Seq objects will work fine with LiveSeq objects. [Bio::LiveSeq](http://search.cpan.org/search?query=Bio::LiveSeq) contain further discussion of LiveSeq objects.

[Bio::SeqI](http://metacpan.org/pod/Bio%3A%3ASeqI) objects are Seq "interface objects". They are used to ensure bioperl's compatibility with other software packages. SeqI and other interface objects are not likely to be relevant to the casual Bioperl user.

## LargeSeq

Very large sequences present special problems to automated sequence-annotation storage and retrieval projects. Bioperl's LargeSeq object addresses this situation.

A [Bio::Seq::LargeSeqI](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3ALargeSeqI) object is a SeqI compliant object that stores a sequence as a series of files in a temporary directory. The aim is to enable storing very large sequences (e.g. > 100 MBases) without running out of memory and, at the same time, preserving the familiar Bioperl Seq object interface. As a result, from the user's perspective, using a LargeSeq object is almost identical to using a Seq object. The principal difference is in the format used in the SeqIO calls. Another difference is that the user must remember to only read in small chunks of the sequence at one time. These differences are illustrated in the following code:

``` perl
$seqio = new Bio::SeqIO(-format => 'largefasta',
                        -file   => 't/data/genomic-seq.fasta');
$pseq = $seqio->next_seq();
$plength = $pseq->length();
$last_4 = $pseq->subseq($plength-3,$plength);  # this is OK
```

On the other hand, the next statement would probably cause the machine to run out of memory:

``` perl
$lots_of_data = $pseq->seq();  # NOT OK for a large LargeSeq object
```

See [Bio::Seq::LargeSeqI](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3ALargeSeqI) for more.

## LiveSeq

Data files with sequences that are frequently being updated present special problems to automated sequence-annotation storage and retrieval projects. Bioperl's [Bio::LiveSeq](http://search.cpan.org/search?query=Bio::LiveSeq) objects are designed to address this situation.

The LiveSeq object addresses the need for a sequence object capable of handling sequence data that may be changing over time. In such a sequence, the precise locations of features along the sequence may change. LiveSeq deals with this issue by re-implementing the sequence object internally as a "double linked chain." Each element of the chain is connected to other two elements (the PREVious and the NEXT one). There is no absolute position like in an array, hence if positions are important, they need to be computed (methods are provided). Otherwise it's easy to keep track of the elements with their "LABELs". There is one LABEL (think of it as a pointer) to each ELEMENT. The labels won't change after insertions or deletions of the chain. So it's always possible to retrieve an element even if the chain has been modified by successive insertions or deletions.

Although the implementation of the LiveSeq object is novel, its bioperl user interface is unchanged since LiveSeq implements a PrimarySeqI interface (recall PrimarySeq is the subset of Seq without annotations or SeqFeatures. Consequently syntax for using LiveSeq objects is familiar although a modified version of SeqIO called [Bio::LiveSeq::IO::BioPerl](http://metacpan.org/pod/Bio%3A%3ALiveSeq%3A%3AIO%3A%3ABioPerl) needs to be used to actually load the data, e.g.:

``` perl
$loader = Bio::LiveSeq::IO::BioPerl->load(-db   => "EMBL",
                                          -file => "t/data/factor7.embl");
$gene = $loader->gene2liveseq(-gene_name => "factor7");
$id = $gene->get_DNA->display_id ;
$maxstart = $gene->maxtranscript->start;
```

See [Bio::LiveSeq::IO::BioPerl](http://metacpan.org/pod/Bio%3A%3ALiveSeq%3A%3AIO%3A%3ABioPerl) for more details.

## Mutator and Mutation

A Mutation object allows for a basic description of a sequence change in the DNA sequence of a gene. The Mutator object takes in mutations, applies them to a LiveSeq gene and returns a set of Bio::Variation objects describing the net effect of the mutation on the gene at the DNA, RNA and protein level.

The objects in [Bio::Variation](http://search.cpan.org/search?query=Bio::Variation) and [Bio::LiveSeq](http://search.cpan.org/search?query=Bio::LiveSeq) were originally designed for the "Computational Mutation Expression Toolkit" project at European Bioinformatics Institute (EBI). The result of using them to mutate a gene is a holder object, a "SeqDiff", that can be printed out or queried for specific information. For example, to find out if restriction enzyme changes caused by a mutation are exactly the same in DNA and RNA sequences, we can write:

``` perl
use Bio::LiveSeq::IO::BioPerl;
use Bio::LiveSeq::Mutator;
use Bio::LiveSeq::Mutation;
 
$loader = Bio::LiveSeq::IO::BioPerl->load(-file => "$filename");
$gene = $loader->gene2liveseq(-gene_name => $gene_name);
$mutation = new Bio::LiveSeq::Mutation(-seq => 'G',
                                        -pos => 100 );
$mutate = Bio::LiveSeq::Mutator->new(-gene      => $gene,
                                     -numbering => "coding"  );
$mutate->add_Mutation($mutation);
$seqdiff = $mutate->change_gene();
$DNA_re_changes = $seqdiff->DNAMutation->restriction_changes;
$RNA_re_changes = $seqdiff->RNAChange->restriction_changes;
$DNA_re_changes eq $RNA_re_changes or print "Different!\n";
```

For a complete working script, see the change\_gene.pl script in the examples/liveseq directory. For more details on the use of these objects see [Bio::LiveSeq::Mutator](http://metacpan.org/pod/Bio%3A%3ALiveSeq%3A%3AMutator) and [Bio::LiveSeq::Mutation](http://metacpan.org/pod/Bio%3A%3ALiveSeq%3A%3AMutation) as well as the original documentation for the "Computational Mutation Expression Toolkit" project ( <http://www.ebi.ac.uk/mutations/toolkit/> ).

## SeqWithQuality

[Bio::Seq::SeqWithQuality](http://metacpan.org/pod/Bio::Seq::SeqWithQuality) objects are used to describe sequences with very specific annotations - that is, base quality annotations. Base quality information is important for documenting the reliability of base calls, typically made by sequencing machines. The quality data is contained within a [Bio::Seq::PrimaryQual](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3APrimaryQual) object.

A SeqWithQuality object is created automatically when phred output, a \*phd file, is read by SeqIO, e.g.

``` perl
$seqio = Bio::SeqIO->new(-file=>"my.phd",-format=>"phd");
# or just 'Bio::SeqIO->new(-file=>"my.phd")'
$seqWithQualObj = $seqio->next_seq;
```

Or, you can make a SeqWithQuality object yourself, e.g.

``` perl
# first, make a PrimarySeq object
$seqobj = Bio::PrimarySeq->new( -seq => 'atcgatcg',
                                -id  => 'GeneFragment-12',
                                -accession_number => 'X78121',
                                -alphabet => 'dna');
 
# now make a PrimaryQual object
$qualobj = Bio::Seq::PrimaryQual->new(-qual => '10 20 30 40 50 50 20 10',
                                      -id   => 'GeneFragment-12',
                                      -accession_number => 'X78121',
                                      -alphabet => 'dna');
 
# now make the SeqWithQuality object
$swqobj = Bio::Seq::SeqWithQuality->new(-seq  => $seqobj,
                                        -qual => $qualobj);
 
# Now we access the sequence with quality object
$swqobj->id(); # the id of the SeqWithQuality object may not match the
               # id of the sequence or of the quality
$swqobj->seq(); # the sequence of the SeqWithQuality object
$swqobj->qual(); # the quality of the SeqWithQuality object
```

See [Bio::Seq::SeqWithQuality](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3ASeqWithQuality) for a detailed description of the methods, [Bio::Seq::PrimaryQual](http://metacpan.org/pod/Bio%3A%3ASeq%3A%3APrimaryQual), and [Bio::SeqIO::phd](http://metacpan.org/pod/Bio%3A%3ASeqIO%3A%3Aphd).

## GFF and Bio:DB:GFF

Another format for transmitting machine-readable sequence-feature data is the Genome Feature Format (GFF). This file type is well suited to sequence annotation because it allows the ability to describe entries in terms of parent-child relationships (see <http://www.sanger.ac.uk/software/GFF> for details). Bioperl includes a parser for converting between GFF files and SeqFeature objects. Typical syntax looks like:

``` perl
$gffio = Bio::Tools::GFF->new(-file => $file, -gff_version => 2);
  # loop over the input stream
while ($feature = $gffio->next_feature()) {
  # do something with feature
}
$gffio->close();
```

Further information can be found at [Bio::Tools::GFF](http://metacpan.org/pod/Bio%3A%3ATools%3A%3AGFF). Also see *examples/tools/gff2ps.pl*, *examples/tools/gb_to_gff.pl*, and the scripts in *scripts/Bio-DB-GFF*. Note: this module shouldn't be confused with the module [Bio::DB::GFF](http://metacpan.org/pod/Bio%3A%3ADB%3A%3AGFF) which is for implementing relational databases when using bioperl-db.

## [Bio::Structure](http://search.cpan.org/search?query=Bio::Structure) and [Bio::Structure::IO](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AIO)

A Structure object can be created from one or more 3D structures represented in Protein Data Bank, or pdb, format (see <http://www.pdb.org> for details).

StructureIO objects allow access to a variety of related Bio:Structure objects. An Entry object consist of one or more Model objects, which in turn consist of one or more Chain objects. A Chain is composed of Residue objects, which in turn consist of Atom objects. There's a wealth of methods, here are just a few:

``` perl
$structio = Bio::Structure::IO->new( -file => "1XYZ.pdb");
$struc = $structio->next_structure; # a Bio::Structure::Entry object
$pseq = $struc->seqres;             # a Bio::PrimarySeq object, thus
$pseq->subseq(1,20);                # returns a sequence string
@atoms = $struc->get_atoms($res);   # Atom objects, given a Residue
@xyz = $atom->xyz;                  # the 3D coordinates of the atom
```

This code shows how to start with a PDB file and obtain Entry, Chain, Residue, and Atom objects:

``` perl
use Bio::Structure::IO;
use strict;
 
my $structio = Bio::Structure::IO->new(-file => $file);
my $struc = $structio->next_structure;
 
for my $chain ($struc->get_chains) {
   my $chainid = $chain->id;
   # one-letter chaincode if present, 'default' otherwise
   for my $res ($struc->get_residues($chain)) {
      my $resid = $res->id;
      # format is 3-lettercode - dash - residue number, e.g. PHE-20
      my $atoms = $struc->get_atoms($res);
      # actually a list of atom objects, used here to get a count
      print join "\t", $chainid,$resid,$atoms,"\n";
   }
}
```

See [Bio::Structure::IO](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AIO), [Bio::Structure::Entry](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AEntry), [Bio::Structure::Model](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AModel), [Bio::Structure::Chain](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AChain), [Bio::Structure::Residue](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AResidue), [Bio::Structure::Atom](http://metacpan.org/pod/Bio%3A%3AStructure%3A%3AAtom) and the *examples/structure* directory for more information.

## [Bio::Map::MapI](http://metacpan.org/pod/Bio::Map::MapI) and [Bio::MapIO](http://metacpan.org/pod/Bio::MapIO)

These are objects for manipulating genetic maps. Bioperl Map objects can be used to describe any type of biological map data including genetic maps, STS maps etc. Map I/O is performed with the MapIO object which works in a similar manner to the SeqIO, SearchIO and similar I/O objects described previously. In principle, Map I/O with various map data formats can be performed. However currently only mapmaker format is supported. Manipulation of genetic map data with Bioperl Map objects might look like this:

``` perl
$mapio = new Bio::MapIO(-format => 'mapmaker', -file => $mapfile);
$map = $mapio->next_map;  # get a map
$maptype = $map->type ;
for $marker ( $map->each_element ) {
  $marker_name = $marker->name ;  # get the name of each map marker
}
```

See [Bio::MapIO](http://metacpan.org/pod/Bio%3A%3AMapIO) and [Bio::Map::SimpleMap](http://metacpan.org/pod/Bio%3A%3AMap%3A%3ASimpleMap) for more information.
