---
title: "HOWTO:Restriction Enzyme Analysis"
layout: default
---

### Authors

[Peter Schattner], [Jason Stajich], [Heikki Lehvaslaiho], [Brian Osborne], [Hilmar Lapp], [Chris Dagdigian], [Elia Stupka], [Ewan Birney].

### Abstract

This is a HOWTO that talks about using the Bioperl Bio::Restriction modules to do ''in silico'' restriction enzyme analysis on nucleotide sequences.

### Introduction

A common sequence analysis task for nucleic acid sequences is locating restriction enzyme cutting sites. Bioperl provides the , , and {{ CPAN|Bio::Restriction::Analysis}} objects for this purpose. These modules replace the older module .

### Making a collection of restriction enzymes

A new collection of enzyme objects would be defined like this:

```perl

use Bio::Restriction::EnzymeCollection;

my $all_collection = Bio::Restriction::EnzymeCollection->new();

```

Bioperl's default object comes with data for more than 500 different Type II restriction enzymes. A list of the available enzyme names can be accessed using the `available_list()` method, but these are just the names, not the functional objects. You also have access to enzyme subsets. For example to select all available Enzyme objects with recognition sites that are six bases long one could write:

```perl

my $six_cutter_collection = $all_collection->cutters(6);

for my $enz ($six_cutter_collection->each_enzyme() ) {

  print $enz->name,"t",$enz->site,"t",$enz->overhang_seq,"\`

";

  # prints name, recognition site, overhang`

}

```

There are other methods that can be used to select sets of enzyme objects, such as `unique_cutters()` and `blunt_enzymes()`. You can also select a Enzyme object by name, like so:

```perl

my $ecori_enzyme = $all_collection->get_enzyme('EcoRI');

```

### Cutting with restriction enzymes

```perl

# get a DNA sequence from somewhere

my $seq = Bio::PrimarySeq->new

     (-seq =>\'AGCTTAATTCATTAGCTCTGACTGCAACGGGCAATATGTCTC\',`
      -primary_id => \'synopsis\',`
      -molecule => \'dna\');`

my $ra = Bio::Restriction::Analysis->new(-seq=>$seq);

```

Once an appropriate enzyme or enzymes have been selected, the sites for that enzyme on a given nucleic acid sequence can be obtained using the `fragments()` method. The code would look something like this:

```perl

my $all_cutters = $ra->cutters;

foreach my $enz ( $all_cutters->each_enzyme ) {

     @fragments = $ra->fragments($enz);`

}

```

`$seq` is the Bio::Seq object for the DNA to be cut and `@fragments` will be an array of strings.

To get information on isoschizomers, methylation sites, microbe source, vendor or availability you will need to create your EnzymeCollection directly from a REBASE file, like this:

```perl

use Bio::Restriction::IO;

my $re_io = Bio::Restriction::IO->new(-file => $file,

                                     -format=> \'withrefm\');`

my $rebase_collection = $re_io->read;

```

A REBASE file in the correct format can be found at <ftp://ftp.neb.com/pub/rebase> - it will have a name like "withrefm.308".

### Custom restriction enzymes

You can also create your own enzymes, like this:

```perl

my $re = new Bio::Restriction::Enzyme(-enzyme => 'BioRI',

                                     -seq => \'GG^AATTCC\');`

```

For more informatation see , , , and .'


