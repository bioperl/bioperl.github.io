---
title: HOWTO:NeXML
layout: howto
---

Abstract
--------

This is a HOWTO about the module, and how to use it to read and write complete Nexml documents. We will also describe how the , , and modules work for outputting individual data types (e.g. just trees) to nexml.

Author
------

Chase Miller

Introduction
------------

The nexml modules integrate the [NeXML](http://www.nexml.org/) exchange standard into [BioPerl], facilitating the adoption of this standard and easing the transition from the overworked NEXUS standard. A wrapper was used to allow [BioPerl] native access to the preferred NeXML parser ([Bio::Phylo](http://search.cpan.org/~rvosa/Bio-Phylo/)).

NeXML functionality in bioperl consists of four modules that allow the user to interact with NeXML data in two different ways. allows users to read/write an entire NeXML document, whereas , , and allow the user to only read/write one data type (seqs, alns, or trees, respectively).

Getting `Bio::Phylo`
--------------------

To use these modules, the `Bio::Phylo` package (*not* part of BioPerl) must be installed. To obtain it via CPAN, do

`$ cpan`
`cpan[1]> install Bio::Phylo`

or to get the bleeding edge Subversion:

`$ cd $YOUR_LOCAL_SRC`
`$ svn co `[`https://nexml.svn.sourceforge.net/svnroot/nexml/trunk/nexml/perl`](https://nexml.svn.sourceforge.net/svnroot/nexml/trunk/nexml/perl)` biophylo`
`$ cd biophylo`
`$ perl Makefile.PL`
`$ make`
`$ make test`
`$ make install`

Design
------

Nexml support in BioPerl is accomplished by creating four nexml modules (described above) that make use of [Bio::Phylo](http://search.cpan.org/dist/Bio-Phylo/) the prefered Nexml parser/unparser. The basic flow goes: BioPerl object to [Bio::Phylo](http://search.cpan.org/dist/Bio-Phylo/) object to Nexml format and vice versa. The module handles the creation/conversion of BioPerl and [Bio::Phylo](http://search.cpan.org/dist/Bio-Phylo/) objects providing a single `Bio::Phylo` access point for all four nexml modules.

The , , and modules are normal extensions of BioPerl and are used in the same ways as other formats. (For more on the \*IO modules read [SeqIO HOWTO].)

The module allows the writing/reading of multiple data object types (i.e. trees/alns/seqs), as opposed to the other \*IO modules which only allow a single data object type.

Example NeXML Documents
-----------------------

Nexml documents to use with the example code can be found at <http://www.nexml.org/nexml/examples/>

Reading/Writing Entire NeXML Documents
--------------------------------------

Reading and writing a whole NeXML document is accomplished with the module. The module can read a NeXML document and maintain many of the data associations allowable by Bio::Phylo (however at this point not all [data associations] are maintained). Once read the data is automatically converted into BioPerl objects (i.e , , and ) and can be manipulated before writing back to a NeXML document.

### Example Code

`#Instantiate a Bio::NexmlIO object and link it to a file`
`my $in_nexml = Bio::NexmlIO->new(-file => \'nexml_doc.xml\', -format => \'Nexml\');`
`#Read in some data`
`my $bptree1 = $in_nexml->next_tree();`
`my $bptree2 = $in_nexml->next_tree();`
`my $bpaln1  = $in_nexml->next_aln();`
`my $bpseq1  = $in_nexml->next_seq();`
`#Use/manipulate data`
`...`
`#push into arrays`
`my @bptrees;`
`push (@bptrees, $bptree1);`
`push (@bptrees, $bptree2);`
`#Write data to nexml file`
`my $out_nexml = Bio::NexmlIO->new(-file => \'>new_nexml_doc.xml\', -format => \'Nexml\');`
`$out_nexml->write(-trees => @bptrees, -alns => @alns, -seqs => @seqs);`

Reading/Writing Individual Datatypes (e.g. trees)
-------------------------------------------------

Sometimes it may be preferable to only work with a single data type. In these cases the use of the `Bio::*IO::nexml` modules (, , or ) are available.

### Example Code

Read/Write a tree

`#Create stream object`
`my $TreeStream = Bio::TreeIO->new(-file => \'trees.xml\', -format => \'Nexml\');`
`#Read and convert first tree to BioPerl Bio::Tree::Tree object`
`my $tree_obj = $TreeStream->next_tree();`
`#Use/manipulate tree data (e.g.)`
`my @nodes = $tree_obj->get_nodes();`
`...`
`#Convert and output BioPerl tree object to nexml`
`my $outTree = Bio::TreeIO->new(-file => \'>trees_out.xml\', -format => \'nexml\');`
`$outTree->write_tree($tree_obj);`

Read/Write an alignment

`#Create stream object`
`my $AlnStream = Bio::AlignIO->new(-file => \'characters.xml\', -format => \'Nexml\');`
`#Read and convert first tree to BioPerl Bio::SimpleAlign object`
`my $aln_obj = $AlnStream->next_aln();`
`#Use/manipulate tree data (e.g.)`
`...`
`#Convert and output BioPerl alignment object to nexml`
`my $outAln = Bio::AlignIO->new(-file => \'>aln_out.xml\', -format => \'nexml\');`
`$outAln->write_aln($aln_obj);`

Use Cases
---------

### Merge two NeXML documents

For this example you will need [characters.xml](http://www.nexml.org/nexml/examples/characters.xml) and [trees.xml](http://www.nexml.org/nexml/examples/trees.xml)

`use strict;`
`use Bio::NexmlIO;`
`#intialize input streams`
`my $alns_in  = Bio::NexmlIO->new(-file => "characters.xml");`
`my $trees_in = Bio::NexmlIO->new(-file => "trees.xml");`
`#read in alignments and convert to bioperl objects`
`my $aln1 = $alns_in->next_aln();`
`my $aln2 = $alns_in->next_aln(); `
`#read in trees and convert to bioperl objects`
`my $tree1 = $trees_in->next_tree();`
`my $tree2 = $trees_in->next_tree();`
`#Manipulate the objects (e.g. change the id)`
`$aln1->id("alignment 1");`
`#push objects into array`
`my (@alns, @trees);`
`push (@alns, $aln1, $aln2);`
`push (@trees, $tree1, $tree2);`
`#intialize output stream`
`my $out = Bio::NexmlIO->new(-file => ">characters+trees.xml");`
`#call write, which generates a valid nexml document and writes it to the stream`
`$out->write(-trees => @trees, -alns => @alns);`

### Write NeXML from other formats

This example converts a Nexus file ([trees.nex](http://www.bioperl.org/wiki/HOWTO:Nexml/trees.nex)) to a NeXML document

`use strict;`
`use Bio::TreeIO;`
`use Bio::NexmlIO;`
`#intialize input streams`
`my $trees_in  = Bio::TreeIO->new(-file => "trees.nex", -format => "nexus");`
`#read in trees and convert to bioperl objects`
`my $tree1 = $trees_in->next_tree();`
`#push objects into array`
`my @trees;`
`push (@trees, $tree1);`
`#intialize output stream`
`my $out = Bio::NexmlIO->new(-file => ">trees_converted.xml");`
`#call write, which converts the data to a nexml document and writes to the stream`
`$out->write(-trees => @trees);`

### Convert specific data types from NeXML to other formats

For convenience NexmlIO provides methods for the quick extraction and conversion of specific data types (i.e. seqs, alns, or trees). For this example you can use the NeXML document that was created in the [Merge Two NeXML Documents] use case above.

`use strict;`
`use Bio::NexmlIO;`
`#intialize stream`
`my $in = Bio::NexmlIO->new(-file => "characters+trees.xml");`
`#extract, convert, and write data types`
`$in->extract_seqs(-file => ">seqs.fas", -format => "fasta");`
`$in->extract_alns(-file => ">alns.nex", -format => "nexus");`
`$in->extract_trees(-file => ">trees.nwk", -format => "newick");`

Data Degradation from Bio::Phylo
--------------------------------

Some associations available in [Bio::Phylo](http://search.cpan.org/~rvosa/Bio-Phylo/) are not currently implemented in Bioperl.

### Associations Maintained

-   Taxa and [Bio::Phylo::Forest::Tree](http://search.cpan.org/~rvosa/Bio-Phylo/lib/Bio/Phylo/Forest/Tree.pm) objects
-   Taxa "blocks" and [Bio::Phylo::Matrices::Datum](http://search.cpan.org/~rvosa/Bio-Phylo/lib/Bio/Phylo/Matrices/Datum.pm) objects (sequences)
-   Taxa and [Bio::Phylo::Matrices::Matrix](http://search.cpan.org/~rvosa/Bio-Phylo/lib/Bio/Phylo/Matrices/Matrix.pm) objects (alignments)

### Associations Not Maintained Yet..

-   Sequences and Nodes
-   Alignments and Trees

### Alignments and Sequences of arbitrary genotype

NeXML is a robust standard and can represent wide-ranging types of data. NeXML allows `Bio::Phylo::Matrices::Matrix` objects (i.e. alignments) and `Bio::Phylo::Matrices::Datum` objects (i.e. sequences) to represent data that is not DNA, RNA, or Protein. We are working on an implementation that interconverts `Bio::Phylo` objects and BioPerl objects using [Jason's] model.

Related Modules
---------------

-   -   -   -   -   -   

'


