---
title: "HOWTO:PhyloXML"
layout: howto
---

Author
------

[Mira Han](http://www.bioperl.org/wiki/Mira_Han), [Indiana University](http://informatics.indiana.edu). `mirhan-at-indiana.edu`

Abstract
--------

This [HOWTO](http://www.bioperl.org/wiki/HOWTO) intends to show how to use the [BioPerl](http://www.bioperl.org/wiki/BioPerl)  [Bio::TreeIO::phyloxml](https://metacpan.org/pod/Bio::TreeIO::phyloxml) driver to parse and write the [phyloxml tree format].

Introduction
------------

[phyloXML](http://www.phyloxml.org/) is an XML language for the analysis, exchange, and storage of phylogenetic trees (or networks) and associated data. The format is supported in BioPerl through the [Bio::TreeIO::phyloxml](https://metacpan.org/pod/Bio::TreeIO::phyloxml) driver. The phyloxml driver can fully parse all the elements defined in the phyloXML XSD and write a valid phyloXML document.

Reading and Writing Trees
-------------------------

### Example Code

Here is some code which will read in a Tree from a file called "phyloxml_examples.xml" and produce a [Bio::Tree::Tree](https://metacpan.org/pod/Bio::Tree::Tree) object which is stored in the variable `$tree`.

Like most modules which do input/output you can also specify the argument `-fh` in place of `-file` to provide a glob or filehandle in place of the filename.

```perl
use Bio::TreeIO;

# parse in phyloxml format
my $input = new Bio::TreeIO(-file => "t/data/phyloxml_examples.xml",
                           -format => "phyloxml");
my $tree = $input->next_tree;
```

Once you have a Tree object you can do a number of things with it. You can use the methods required in [Bio::Tree::TreeI](https://metacpan.org/pod/Bio::Tree::TreeI) to access the information on the nodes. For example, this script reads in phyloxml data and prints out the the node ids and bootstrap values.

```perl
use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
           -fh => \*DATA);

while( my $tree = $treeio->next_tree ) {
  for my $node ( $tree->get_nodes ) {
    printf "id: %s bootstrap: %s\n", $node->id || '', $node->branch_length || '', "\n";
  }
}

__DATA__
<?xml version="1.0" encoding="UTF-8"?>
<phyloxml>
 <phylogeny rooted="true">
     <name>example from Prof. Joe Felsenstein's book "Inferring Phylogenies"</name>
     <description>phyloXML allows to use either a "branch_length" attribute or element to indicate branch lengths.</description>
     <clade>
        <clade>
           <branch_length>0.06</branch_length>
           <clade>
              <name>A</name>
              <branch_length>0.102</branch_length>
           </clade>
           <clade>
              <name>B</name>
              <branch_length>0.23</branch_length>
           </clade>
        </clade>
        <clade>
           <name>C</name>
           <branch_length>0.4</branch_length>
        </clade>
     </clade>
  </phylogeny>
</phyloxml>
```

Writing phyloXML trees are similar to writing any other tree formats. Create a TreeIO with 'phyloxml' as the format and call `write_tree()`.

```perl
my $newfile = "newfile.txt";
my $newio = Bio::TreeIO->new (-format => 'phyloxml', -file=>">$newfile");
$newio->write_tree($tree);
```


Accessing Annotations on Nodes
------------------------------

phyloXML provides a number of elements for describing the data associated with the nodes. Some examples are taxonomic information with scientific name, common name, and taxonomy code; sequence data with gene name, sequence accession, and annotation; distribution; branch lengths and support values; events such as duplications and speciations; control of tree appearance with colors and branch widths. Users can also define their own data fields through the element <property>. BioPerl has the [Bio::Tree::AnnotatableNode](https://metacpan.org/pod/Bio::Tree::AnnotatableNode) module to support annotations attached to Node object. Most of the phyloXML elements are attached to the AnnotatableNode as nested annotation collections. Only molecular sequence data are attached as a [Bio::SeqI](https://metacpan.org/pod/Bio::SeqI) object.

### Retrieving Annotations

Each phyloXML element has attributes, text values and nested elements. In order to preserve the structure, elements are stored in a nested [Bio::Annotation::Collection](https://metacpan.org/pod/Bio::Annotation::Collection) with tags `_text` and `_attr` for the text value and the attribute, and nested elements are stored as nested AnnotationCollections with corresponding element names as tag names.

Users can use the annotation() method of AnnotatableNode to get the AnnotationCollection, and then traverse the nested AnnotationCollection structure.

```perl
use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
           -fh => \*DATA);
my $tree = $treeio->next_tree;
my ($A) = $tree->find_node('A');
my ($ac) = $A->annotation();
my (@annotations) = $ac->get_Annotations('property');
my (@keys) = $annotations[0]->get_all_annotation_keys();
my (@value) = $annotations[0]->get_Annotations('_text');
print "Annotation NOAA:depth ",$value[0]->value, "\n";

__DATA__
<?xml version="1.0" encoding="UTF-8"?>
<phyloxml>
  <phylogeny rooted="true">
     <name>same example, using property elements to indicate a "depth" value for marine organisms</name>
     <clade>
        <clade>
           <name>AB</name>
           <clade>
              <name>A</name>
              <property datatype="xsd:integer" ref="NOAA:depth" applies_to="clade" unit="METRIC:m"> 1200 </property>
           </clade>
           <clade>
              <name>B</name>
              <property datatype="xsd:integer" ref="NOAA:depth" applies_to="clade" unit="METRIC:m"> 2300 </property>
           </clade>
        </clade>
        <clade>
           <name>C</name>
           <property datatype="xsd:integer" ref="NOAA:depth" applies_to="clade" unit="METRIC:m"> 200 </property>
        </clade>
     </clade>
  </phylogeny>
</phyloxml>
```

Or users can use the read_annotation method provided in [Bio::TreeIO::phyloxml](https://metacpan.org/pod/Bio::TreeIO::phyloxml) to access the information directly through a XPath-like path string.

```perl
use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
   -fh => \*DATA);
my $tree = $treeio->next_tree;
my $node = $tree->get_root_node;
my @leaves;
my @children = ($node);
for (@children) {
 push @children, $_->each_Descendent();
} for (@children) {
 push @leaves, $_ if $_->is_Leaf;
}
my ($D) = $leaves[0];
my ($point) = $treeio->read_annotation('-obj'=>$D, '-path'=>'distribution/point/geodetic_datum', '-attr'=>1);
print ("node distribution geodetic_datum is $point\n");
my ($lat) = $treeio->read_annotation('-obj'=>$D, '-path'=>'distribution/point/lat');
my ($long) = $treeio->read_annotation('-obj'=>$D, '-path'=>'distribution/point/long');
my ($alt) = $treeio->read_annotation('-obj'=>$D, '-path'=>'distribution/point/alt');
print ("node distribution lat: $lat long: $long alt: $alt\n");

__DATA__
<?xml version="1.0" encoding="UTF-8"?>
<phyloxml> <phylogeny rooted="true">
     <name>A tree with phylogeographic information</name>
     <clade>
        <clade>
           <clade>
              <name>A</name>
              <distribution>
                 <desc>Hirschweg, Winterthur, Switzerland</desc>
                 <point geodetic_datum="WGS84">
                    <lat>47.481277</lat>
                    <long>8.769303</long>
                    <alt>472</alt>
                 </point>
              </distribution>
           </clade>
           <clade>
              <name>B</name>
              <distribution>
                 <desc>Nagoya, Aichi, Japan</desc>
                 <point geodetic_datum="WGS84">
                    <lat>35.155904</lat>
                    <long>136.915863</long>
                    <alt>10</alt>
                 </point>
              </distribution>
           </clade>
           <clade>
              <name>C</name>
              <distribution>
                 <desc>ETH Z\xc3\xbcrich</desc>
                 <point geodetic_datum="WGS84">
                    <lat>47.376334</lat>
                    <long>8.548108</long>
                    <alt>452</alt>
                 </point>
              </distribution>
           </clade>
        </clade>
        <clade>
           <name>D</name>
           <distribution>
              <desc>San Diego</desc>
              <point geodetic_datum="WGS84">
                 <lat>32.880933</lat>
                 <long>-117.217543</long>
                 <alt>104</alt>
              </point>
           </distribution>
        </clade>
     </clade>
  </phylogeny>
</phyloxml>
```

### Retrieving Sequence

`<sequence>` elements are attached to the [Bio::Tree::AnnotatableNode](https://metacpan.org/pod/Bio::Tree::AnnotatableNode) as a [Bio::SeqI](https://metacpan.org/pod/Bio::SeqI) object. The sequence object can be retrieved using the sequence() method of [Bio::Tree::AnnotatableNode](https://metacpan.org/pod/Bio::Tree::AnnotatableNode). Even `<sequence>` elements without molecular sequence data are stored as SeqI objects with empty sequence..

```perl

use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
   -fh => \*DATA);
my $tree = $treeio->next_tree;
my @nodes = $tree->get_nodes;
foreach my $n (@nodes) {
 if ($n->sequence) {
   # get sequence object for the node
   my ($seq) = @{$n->sequence};

   # get annotation in steps
   my ($seqac) = $seq->annotation;
   my ($seqnameac) = $seqac->get_nested_Annotations(-keys => ['name']);
   my ($name) = $seqnameac->get_Annotations('_text');
   print $name->value, "\n";

   # or get annotation using path
   my ($name2) = $treeio->read_annotation('-obj'=>$seq, '-path'=>'name');
   print $name2, "\n";
 }
}
```

Adding Annotations to Nodes, Trees or Sequences
-----------------------------------------------

Users can add various annotations defined in phyloXML to the object of interest, which can be the node, the tree or a sequence associated with a node.

### Adding Annotations in phyloXML format

phyloXML annotations can be added to nodes, trees, or sequences. Users can use the `add_phyloXML_annotation()` of TreeIO to add annotations in phyloXML format.

```perl

use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
           -fh => \*DATA);
my $tree = $treeio->next_tree;
my ($A) = $tree->find_node('A');
$treeio->add_phyloXML_annotation(
       -obj => $A,
       -xml => "<name>A</name>
           <date unit=\"mya\">
           <desc>my date</desc>
           <value>600 million years</value>
           </date>
           "
       );
my ($dateunit) = $treeio->read_annotation('-obj'=>$A, '-path'=>'date/unit', '-attr'=>1);
my ($datevalue) = $treeio->read_annotation('-obj'=>$A, '-path'=>'date/value');
```

### Adding phyloXML attributes

We can also simply add attributes to an existing object or an annotation using the `add_attribute()` function of TreeIO.

```perl
use Bio::TreeIO;
my $treeio = Bio::TreeIO->new(-format => 'phyloxml',
           -fh => \*DATA);
my $tree = $treeio->next_tree;
my ($z) = $tree->find_node('Z');
my $z_seq = $z->sequence->[0];

# add attribute id_source
$treeio->add_attribute(
       '-obj' => $z_seq,
       '-attr' => "id_source = \"Zseq\""
       );
```


Node Relations and Sequence Relations
-------------------------------------

`<clade_relations>` and `<sequence_relations>` are elements used in the `<phylogeny>` level to describe network connections between nodes or relationships between sequences (e.g. orthology/paralogy). Bioperl reads the relation elements and finds the appropriate objects ([Bio::Tree::AnnotatableNode](https://metacpan.org/pod/Bio::Tree::AnnotatableNode) or [Bio::SeqI](https://metacpan.org/pod/Bio::SeqI)) corresponding to the description and attaches the [Bio::Annotation::Relation](https://metacpan.org/pod/Bio::Annotation::Relation) to the object. When the tree is written, the [Bio::Annotation::Relation](https://metacpan.org/pod/Bio::Annotation::Relation)s for each objects are translated into phylogeny level documentation.

Relation type annotations should be added to the tree object.

```perl
$treeio->add_phyloXML_annotation(
         '-obj'=>$tree,
         '-xml'=>'<sequence_relation id_ref_0="Zseq" id_ref_1="Yseq" type="orthology" ><confidence type="rio">value</confidence></sequence_relation>'
         );
```


References and More Reading
---------------------------

For more reading and some references for the techniques above see these titles.

1. Han MV and Zmasek CM. phyloXML: XML for evolutionary biology and comparative genomics. BMC Bioinformatics. 2009 Oct 27;10:356. [DOI:10.1186/1471-2105-10-356](http://dx.doi.org/10.1186/1471-2105-10-356) | [PubMed ID:19860910](http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=pubmed&dopt=Abstract&list_uids=19860910) | [HubMed](http://www.hubmed.org/display.cgi?uids=19860910)

Related Modules
---------------

Here's a list of the relevant modules. If you have questions or comments that aren't addressed herein then write the Bioperl community at <bioperl-l@bioperl.org>.

* [Bio::Annotation::Collection](https://metacpan.org/pod/Bio::Annotation::Collection)
* [Bio::Annotation::Relation](https://metacpan.org/pod/Bio::Annotation::Relation)
* [Bio::Tree::Tree](https://metacpan.org/pod/Bio::Tree::Tree)
* [Bio::Tree::Node](https://metacpan.org/pod/Bio::Tree::Node)
* [Bio::Tree::TreeI](https://metacpan.org/pod/Bio::Tree::TreeI)
* [Bio::Tree::NodeI](https://metacpan.org/pod/Bio::Tree::NodeI)
* [Bio::Tree::AnnotatableNode](https://metacpan.org/pod/Bio::Tree::AnnotatableNode)
* [Bio::Tree::TreeFunctionsI](https://metacpan.org/pod/Bio::Tree::TreeFunctionsI)
* [Bio::TreeIO::phyloxml](https://metacpan.org/pod/Bio::TreeIO::phyloxml)
