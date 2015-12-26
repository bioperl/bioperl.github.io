---
title: "FAQ"
layout: howto
---

About this FAQ
--------------

### What is this FAQ?

It is the list of Frequently Asked Questions about BioPerl.

### What if my question isn't answered here?

Please contact the [bioperl-l mailing list](http://lists.open-bio.org/mailman/listinfo/bioperl-l).

### How can I tell what version of BioPerl is installed?

We have a universal version number for a release. This is set in the module  [Bio::Root::Version](https://metacpan.org/pod/Bio::Root::Version) which is universally applied to every module which inherits from [Bio::Root::RootI](https://metacpan.org/pod/Bio::Root::RootI). To check the version, just use the following one liner:

```perl
perl -MBio::Root::Version -e 'print $Bio::Root::Version::VERSION' 
```

Now when we use [version tuples](http://www.perl.com/pub/a/2000/04/whatsnew.html#Version_Tuples) that are not just decimal numbers, Perl converts these silently to [Unicode](https://en.wikipedia.org/wiki/Unicode) representations. What that means is, to actually print the version number you have to use formatted printing like this

```perl
perl -MBio::Root::Version -e
    'printf "%vd\n", $Bio::Root::Version::VERSION'
```

Printing the version number can be done on any module in BioPerl (and should be consistent) so for example, printing out the version number of [Bio::SeqIO](https://metacpan.org/pod/Bio::Root::SeqIO), which is different from the overall Bioperl version number.

```perl 
perl -MBio::SeqIO -e 'printf "%vd ", $Bio::SeqIO::VERSION' 
```

BioPerl in General
------------------

### What is BioPerl?

BioPerl is a toolkit of perl modules useful in building [bioinformatics](https://en.wikipedia.org/wiki/bioinformatics) solutions in [Perl](http://perl.org). It is built in an [object-oriented](https://en.wikipedia.org/wiki/Object_oriented) manner so that many modules depend on each other to achieve a task. The collection of modules in the [bioperl-live](https://github.com/bioperl/bioperl-live) repository consist of the core of the functionality of bioperl. Additionally auxiliary modules for creating persistent storage in [RDMBS](https://en.wikipedia.org/wiki/RDMBS) ([bioperl-db](https://github.com/bioperl/bioperl-db)) and running and parsing the results from hundreds of [bioinformatics](https://en.wikipedia.org/wiki/bioinformatics) applications ([bioperl-run](https://github.com/bioperl/bioperl-run)) are all available in our [Git](https://en.wikipedia.org/wiki/Using_Git) repository.

Some early articles about BioPerl:

- [History of BioPerl](/articles/History_of_BioPerl.html)
- [How Perl Saved the Human Genome Project](/articles/How_Perl_saved_human_genome.html)

### Where do I go to get the latest release?

Please see the [INSTALL file](INSTALL.html).

### What do you mean *developer release*?

Developer releases are odd numbered releases (e.g. 1.3, 1.5) not intended to be completely stable (although all tests should pass). Stable releases are even numbered (1.0, 1.2, 1.6) and intended to provide a stable API so that modules will continue to respect the API throughout a stable release series. We cannot guarantee that APIs are stable between releases (i.e. 1.6 may not be completely compatible with scripts written for 1.4), but we endeavor to keep the API stable so that upgrading is easy.
  
The 0.7 series (0.7.0, 0.7.2) were all released in 2001 and were stable releases on 0.7 branch. This means they had a set of functionality that is maintained throughout (no experimental modules) and were guaranteed to have all tests and subsequent bug fix releases with the 0.7 designation would not have any API changes.

The 0.9 series was our first attempt at releasing so called developer releases. These are snapshots of the actively developed code that at a minimum pass all our tests.

### How can I learn how to use a module?

```
% perldoc MODULE
```

Careful - spelling and case count! If you are not sure about case you can use the `-i` switch with perldoc. You may also find useful documentation in the form of a [HOWTOs](/howtos/index.html). There are also many scripts in the `examples/` and `scripts/` directories that may be useful - see [the BioPerl scripts page](https://github.com/bioperl/bioperl-live/tree/master/scripts) for brief descriptions.

Additionally we have written many tests, these are a great source of example code, look in the [test dir](https://github.com/bioperl/bioperl-live/tree/master/t), called `t/`.

### I'm interested in the bleeding edge version of the code, where can I get it?

https://github.com/bioperl

### How should I cite BioPerl?

Stajich JE, Block D, Boulez K, Brenner SE, Chervitz SA, Dagdigian C, Fuellen G, Gilbert JG, Korf I, Lapp H, Lehväslaiho H, Matsalla C, Mungall CJ, Osborne BI, Pocock MR, Schattner P, Senger M, Stein LD, Stupka E, Wilkinson MD, and Birney E. The Bioperl toolkit: Perl modules for the life sciences. Genome Res. 2002 Oct;12(10):1611-8.

- http://dx.doi.org/10.1101/gr.361602
- http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=pubmed&dopt=Abstract&list_uids=12368254

### What are the license terms for BioPerl?

BioPerl is licensed under the same terms as [Perl](http://perl.org) itself which is dually-licensed under the terms of the [Perl Artistic License](http://www.perl.com/pub/a/language/misc/Artistic.html) or http://www.opensource.org/licenses/artistic-license.html or the [GNU GPL](http://www.gnu.org/licenses/gpl.html).

### I want to help, where do I start?

BioPerl is a pretty diverse collection of modules which has grown from the direct needs of the developers participating in the project. So if you don't have a need for a specific module in the toolkit it becomes hard to just describe ways it needs to be expanded or adapted. One area, however is the development of stand-alone scripts which use BioPerl components for common tasks. Some starting points for scripts: find out what people in your institution do routinely that a shortcut can be developed for. Identify modules in BioPerl that need easy interfaces and write that wrapper - you'll learn how to use the module inside and out.

We always need people to help fix bugs - check the [GitHub bug tracking system](https://github.com/bioperl/bioperl-live/issues). Submitting bugs in the documentation and code is very helpful as has been said about open source software ["Given enough eyeballs, all bugs are shallow"](https://en.wikipedia.org/wiki/Cathedral_and_the_Bazaar).

### I've got an idea for a module how do I contribute it?

Post your idea on the [bioperl-l mailing list](http://lists.open-bio.org/mailman/listinfo/bioperl-l). If you have written it already, or if you have been thinking about the API already, post the API, ideally with usage documentation, e.g., the POD that would normally go with each method, and some usage examples, e.g., what would otherwise go into the synopsis section of the module's POD.

Once you completed gathering feedback and incorporating into your module as appropriate, you either post it on [GitHub](https://github.com/bioperl/bioperl-live/issues), or, if you have a developer account already, you should just commit it once you have convinced yourself that the (yours and the pre-existing) tests pass.

### How do I submit a patch or enhancement to BioPerl?

Post your idea to the [bioperl-l mailing list](http://lists.open-bio.org/mailman/listinfo/bioperl-l). If it is a really new idea consider taking us through your thought process. We'll help you tease out the necessary information such as what methods you'll want and how it can interact with other BioPerl modules. If it is a port of something you've already worked on, give us a summary of the current methods. Make sure there is an interface to the module, not just an implementation and make sure there will be a set of tests that will be in the `t/` directory to insure that your module is tested. 

If you have a suggested patch and/or code enhancement you can submit it to the [GitHub tracking system](https://github.com/bioperl/bioperl-live/issues). See also [Advanced BioPerl](/howtos/Advanced_BioPerl.html) for more information.

### Why can't I easily get a list of all the methods a object can call?

This a problem with Perl, not only with Bioperl. To list all the methods, you have to walk the inheritance tree and standard perl is not able to do it. As usual, help can be found in the [CPAN](http://www.cpan.org). Install [Class::Inspector](https://metacpan.org/pod/Class::Inspector) and put the following script into your path and run it.

```perl
#!/usr/bin/perl -w

use Class::Inspector; 
$class = shift || die "Usage: methods perl_class_name"; 
eval "require $class"; 
print join (" ", sort @{Class::Inspector->methods($class,'full','public')});
```

There is also a project called [Deobfuscator](http://bioperl.org/cgi-bin/deob_interface.cgi) developed during the 2005 [Bioinformatics course](http://stein.cshl.org/genome_informatics/Advanced) at [Cold Spring Harbor Labs](http://www.cshl.edu). The Deobfuscator displays available methods for an object type and provide links to the return types of the methods. An older version can also be found [here](http://davemessina.net/cgi-bin/deob_interface.cgi).

### Can you explain the Object Model design and rationale?

There is no simple answer to this question. Simply put, this is a toolkit which has grown organically. The goals and user audience has evolved. Some decisions have been made and we have been forced to live by them rather than destroy backward compatibility. In addition there are different [philosophies of software development](https://en.wikipedia.org/wiki/List_of_software_development_philosophies). The major developers on the project have tried to impose a set of standards on the code so that the project can be coordinated without every commit being cleared by a few key individuals (see [Eric S. Raymond's](https://en.wikipedia.org/wiki/Eric_S_Raymond) essay ["The Cathedral and the Bazaar"](https://en.wikipedia.org/wiki/Cathedral_and_the_Bazaar) for different styles of running an [open source](https://en.wikipedia.org/wiki/open_source) project - we are clearly on the Bazaar end). 

[Advanced BioPerl](/howtos/Advanced_BioPerl.html) talks more about specific design goals. The clear consensus of the project developers is that BioPerl should be consistent. This may cause us to pay the price of some copy-and-paste of code, with the Get/Set accessor methods being a sore spot for some, and the lack of using AUTOLOAD. By being consistent we hope that someone can [grok](https://en.wikipedia.org/wiki/grok) the gist of a module from the basic documentation, see example code, and get a set of methods from the [API](https://en.wikipedia.org/wiki/Application_programming_interface) documentation. We aim to make the core object design easy to understand. This has not been realized by any stretch of the imagination as the toolkit has well over 1000 modules in [bioperl-live](https://github.com/bioperl/bioperl-live) and [bioperl-run](https://github.com/bioperl/bioperl-run) alone. That said we do want to improve things. We want to experiment with newer modules which make Perl more [object-oriented](https://en.wikipedia.org/wiki/Object_oriented). We have high hopes for some of the promises of [Perl6](https://en.wikipedia.org/wiki/Perl6). To try and realize this goal we are encouraging developers to play with new object models in a [bioperl-experimental](https://github.com/bioperl/bioperl-experimental) project. 

Some useful discussion on the mailing list can be found at this node http://bioperl.org/pipermail/bioperl-l/2003-December/014406.html. We encourage you to participate in the discussion and to join in the development process either on existing BioPerl code or the [bioperl-experimental](https://github.com/bioperl/bioperl-experimental) code if you have a particular interest in making the toolkit more [object-oriented](https://en.wikipedia.org/wiki/Object_oriented).

Sequences
---------

### How do I parse a sequence file?

Use the `Bio::SeqIO` system, this will create objects for you from file input. For more information see the [SeqIO HOWTO](/howtos/SeqIO.html), or type `perldoc Bio::SeqIO`.

### I can't get sequences with Bio::DB::GenBank any more, why not?

If you are running an old BioPerl version, [NCBI](https://en.wikipedia.org/wiki/NCBI) changed the web [CGI](https://en.wikipedia.org/wiki/CGI) script that provided this access. You must use a modern version, 1.4 or greater.

### How can I get NT_ or NM_ or NP_ accessions from [NCBI](https://en.wikipedia.org/wiki/NCBI) ([Reference sequences](https://en.wikipedia.org/wiki/Reference_sequences))?

To retrieve [GenBank](https://en.wikipedia.org/wiki/GenBank) reference sequences, or [RefSeqs](https://en.wikipedia.org/wiki/Reference_sequences), use [Bio::DB::RefSeq](https://metacpan.org/pod/Bio::DB::RefSeq). This is still an area of active development because the data providers have not provided the best interface for us to query. [EBI](https://en.wikipedia.org/wiki/EBI) has provided a mirror with their `dbfetch` system which is accessible through the object however, there are cases where `NT_` [accession numbers](https://en.wikipedia.org/wiki/accession_number) will not be retrievable.

### How can I use  to parse sequence data to or from a string?

Use this code to parse sequence records from a string:

```perl
use IO::String; 
use Bio::SeqIO; 
my $stringfh = new IO::String($string); 
my $seqio = new Bio::SeqIO(-fh => $stringfh,
                           -format => 'fasta');

while( my $seq = $seqio->next_seq ) {
    # process each seq
}
```

And here is how to write to a string:

```perl
use IO::String; 
use Bio::SeqIO; 
my $s; 
my $io = IO::String->new($s); 
my $seqOut = new Bio::SeqIO(-format => 'swiss', -fh => $io); 
$seqOut->write_seq($seq1); 
print $s; 
# $s contains the record in swissprot format and is stored in the string
```

### How do I use Bio::Index::Fasta and index on different ids?

I'm using in order to retrieve sequences from my indexed fasta file but I keep seeing `MSG: Did not provide a valid Bio::PrimarySeqI object` when I call `fetch` followed by `write_seq()` on a handle. Why?

It's likely that `fetch` didn't retrieve a object. There are few possible explanations but the most common cause is that the id you're passing to `fetch` is not the key to that sequence in the index. For example, if the [FASTA](https://en.wikipedia.org/wiki/FASTA) header is `>gi|12366` and your id is `12366` then `fetch` won't find the sequence, it expects to see `gi|12366`. You need to use the `get_id` method to specify the key used in indexing, like this:

```perl
$inx = Bio::Index::Fasta->new(-filename =>$indexname); 
$inx->id_parser(&get_id); 
$inx->make_index($fastaname);

sub get_id {
    my $header = shift;
    $header =~ /^>gi|(d+)/;
    $1;
}
```

The same issue arises when you use [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta), and in that case the code might look like this:

```perl
$inx = Bio::DB::Fasta->new($fastaname, -makeid => &get_id);
```

### Accession numbers are not present for FASTA sequence files

If you parse a [FASTA sequence format](https://en.wikipedia.org/wiki/FASTA_sequence_format) file with the sequences won't have the [accession number](https://en.wikipedia.org/wiki/accession_number). What to do?

All the data is in the `$seq->display_id` it just needs to be parsed out. Here is some code to set the [accession number](https://en.wikipedia.org/wiki/accession_number).

```perl
my ($gi,$acc,$locus); 
(undef,$gi,undef,$acc,$locus) = split(/|/, $seq->display_id); 
$seq->accession_number($acc);
```

Why don't we just go ahead and do this? For one, we don't make any assumptions about the format of the ID part of the sequence. Perhaps the parser code could try and detect if it is a [GenBank](https://en.wikipedia.org/wiki/GenBank) formatted ID and go ahead and set the [accession number](https://en.wikipedia.org/wiki/accession_number) field.

Also see http://bioperl.org/pipermail/bioperl-l/2005-August/019579.html

### How do I get genomic sequences when all I have is an gene identifier or name?

This question has a few different answers, see the [Getting Genomic Sequences HOWTO](/howtos/Getting_Genomic_Sequences.html).

### I would like to make my own custom fasta header - how do I do this?

You want to use the method `preferred_id_type()`. Here's some example code:

```perl

use Bio::SeqIO;

my $seqin = Bio::SeqIO->new(-file => $file, -format => 'genbank');

my $seqout = Bio::SeqIO->new(-fh => \*STDOUT, -format => 'fasta');

# From Bio::SeqIO::fasta
$seqout->preferred_id_type('display');
my $count = 1;

while (my $seq = $seqin->next_seq) {
    # override the regular display_id with your own
    $seq->display_id('foo'.$count);
    $seqout->write_seq($seq);
    $count++;
}
```

You can pass one of the following values to `preferred_id_type`: "accession", "accession.version", "display", "primary". The description line is automatically appended to the preferred id type but this can also be set, like so:

```perl
$seq->desc($some_string);
```

Report Parsing
--------------

### I want to parse BLAST output, how do I do this?

Read the [SearchIO HOWTO](/howtos/SearchIO.html) for more information.

### What was wrong with Bio::Tools::Blast?

Bio::Tools::Blast* is no longer supported, as of BioPerl version 1.1. It has just been replaced by a more generic approach to reports. This generic approach allows us to just write pluggable modules for [FASTA](https://en.wikipedia.org/wiki/FASTA) and [BLAST](https://en.wikipedia.org/wiki/BLAST) parsing while using the same framework. This is completely analogous to the system of parsing sequence files. However, the objects produced are of the rather than variety. See the [SearchIO HOWTO](/howtos/SearchIO.html).

### I want to parse FASTA or NCBI -m7 (XML) format, how do I do this?

It is as simple as parsing text [BLAST](https://en.wikipedia.org/wiki/BLAST) results - you simply need to specify the format as `fasta` or `blastxml` and the parser will load the appropriate module for you. You can use the exact logic and code for all of these formats as we have generalized the modules for sequence database searching. The page describing provides a table showing how the formats match up to particular modules. Note that, for parsing BLAST XML output, you will need and that is recommended to speed up parsing.

### How can I generate a pairwise alignment of two sequences?

Look at to see how to use the `water` and `needle` alignment programs that are part of the [EMBOSS](http://emboss.org) suite. is part of the [bioperl-run](https://github.com/bioperl/bioperl-run) package.

Or you can use the pSW module for [DNA](https://en.wikipedia.org/wiki/DNA) alignments or the dpAlign module for protein alignments. These are part of the [bioperl-ext](https://github.com/bioperl/bioperl-ext) package.

You can also use prss34 (part of [FASTA](https://en.wikipedia.org/wiki/FASTA) package) to assess the significance of a pairwise alignment by shuffling the sequences.

### How do I get the frame for a translated search?

I'm using *Bio::Search* and its `frame()` to parse BLAST but I'm seeing 0, 1, or 2 instead of the expected -3, -2, -1, +1, +2, +3. Why am I seeing these different numbers and how do I get the frame according to BLAST?

These are [GFF](https://en.wikipedia.org/wiki/GFF) frames - so +1 is 0 in GFF, -3 will be encoded with a frame of 2 with the strand being set to -1.

Frames are relative to the hit or query sequence so you need to query it based on sequence you are interested in:

```perl
$hsp->hit->strand; $hsp->hit->frame;
```

or

```perl
$hsp->query->strand; $hsp->query->frame;
```

So the value according to a blast report of -3 can be constructed as:

```perl
my $blastframe = ($hsp->query->frame + 1) \* $hsp->query->strand;
```

### Can I get domain number from hmmpfam or hmmsearch output?

For example:

`SH2_5: domain 2 of 2, from 349 to 432: score 104.4, E = 1.9e-26`

Not directly but you can compute it since the domains are numbered by their order on the protein:

```perl
my @domains = $hit->domains; 
my $domainnum = 1; 
my $total = scalar @domains; 
for my $domain ( sort { $a->start <=> $b->start } $hit->domains ) {
    print "domain $domainnum of $total,\n";
    $domainnum++;
}
```

Annotations and Features
------------------------

### How do I retrieve all the features from a sequence?

How about all the features which are exons or have a `/note` field that contains a certain gene name?

To get all the features:

```perl
my @features = $seq->all_SeqFeatures();
```

To get all the features filtering on only those which have the primary tag (ie. feature type) `exon`.

```perl
my @genes = grep { $_->primary_tag eq 'exon'} $seq->all_SeqFeatures();
```

To get all the features filtering on this which have the `/note` tag and within the note field contain the requested string `$noteval`.

```perl
my @f_with_note = grep { my @a = $_->has_tag('note') ? 
    $_->each_tag_value('note') : ();
    grep { $noteval } @a;  }  $seq->all_SeqFeatures();
```

### How do I parse the CDS `join` or `complement` statements in GenBank or EMBL files to get the sub-locations?

For example, how can I get the the coordinates `45` and `122` in `join(45..122,233..267)` ?

You could use `primary_tag` to find the `CDS` features and the object to get the coordinates:

```perl
for my $feature ($seqobj->top_SeqFeatures){
    if ( $feature->location->isa('Bio::Location::SplitLocationI') 
        and $feature->primary_tag eq 'CDS' ) {
        for my $location ( $feature->location->sub_Location ) {
            print $location->start , ".." , $location->end, "\n";
        }
    }
}
```

### How do I retrieve a nucleotide coding sequence when I have a protein gi number?

You could go through the protein's feature table and find the `coded_by` value. The trick is to associate the `coded_by` nucleotide coordinates to the nucleotide entry, which you'll retrieve using the *accession number* from the same feature.

```perl
use Bio::Factory::FTLocationFactory; 
use Bio::DB::GenPept; 
use Bio::DB::GenBank;

my $gp = Bio::DB::GenPept->new; 
my $gb = Bio::DB::GenBank->new;

# factory to turn strings into Bio::Location objects
my $loc_factory = Bio::Factory::FTLocationFactory->new;

my $prot_obj = $gp->get_Seq_by_id($protein_gi); 

for my $feat ( $prot_obj->top_SeqFeatures ) {
    if ( $feat->primary_tag eq 'CDS' ) {
        # example: 'coded_by="U05729.1:1..122"'
        my @coded_by = $feat->each_tag_value('coded_by');
        my ($nuc_acc,$loc_str) = split /:/, $coded_by[0];
        my $nuc_obj = $gb->get_Seq_by_acc($nuc_acc);
        # create Bio::Location object from a string
        my $loc_object = $loc_factory->from_string($loc_str);
        # create a Feature object by using a Location
        my $feat_obj = Bio::SeqFeature::Generic->new(-location =>$loc_object);
        # associate the Feature object with the nucleotide Seq object
        $nuc_obj->add_SeqFeature($feat_obj);
        my $cds_obj = $feat_obj->spliced_seq;     
        print "CDS sequence is ",$cds_obj->seq,"\n";
    }
}

```

### How do I get the complete spliced nucleotide sequence from the CDS section?

You can use the `spliced_seq` method. For example:

```perl
my $seq_obj = $db->get_Seq_by_id($gi); 
for my $feat ( $seq_obj->top_SeqFeatures ) {
  if ( $feat->primary_tag eq 'CDS' ) {
     my $cds_obj = $feat->spliced_seq;
     print "CDS sequence is ",$cds_obj->seq,"\n";
    }
}
```

### How do I get the complete spliced sequence when the coordinates refer to Genbank identifiers?

The problematic features have coordinates like this:

`join(complement(AY421753.1:1..6),complement(3813..5699))`

To retrieve this, you need to pass a Genbank database handle to the `spliced_seq` method. For example:

```perl
my $db = Bio::DB::GenBank->new();
my $io = Bio::SeqIO->new(-file=>'funnyfile.gb', -format=>'genbank'); 
while ( my $seq = $seq_in->next_seq ) {
    for my $feat ( $seq->get_SeqFeatures ) {
        if ( $feat->primary_tag eq 'CDS' ) {
            my $cds = $feat->spliced_seq(-db => $db, -nosort => 0);
            print $cds->translate->seq, "\n";
        }
    }
}
```

### How do I get the reverse-complement of a sequence using the `subseq` method?

One way is to pass the location to `subseq` in the form of a object. This object holds strand information as well as coordinates.

```perl
use Bio::Location::Simple; 
my $location = Bio::Location::Simple->new(-start => $start,
                                          -end   => $end,
                                          -strand => "-1");

# assume we already have a sequence object
my $rev_comp_substr = $seq_obj->subseq($location);
```

### I get the warning *(old style Annotation) on new style Annotation::Collection*. What is wrong?

Wow, you're using an **old** version! You'll see this error because the modules and interface has changed starting with BioPerl 1.0. Before v1.0 there was a Bio::Annotation module with `add_Comment`, `add_Reference`, `each_Comment`, and `each_Reference` methods.

After v1.0 there is a module with `add_Annotation('comment', $ann)` and `get_Annotations('comment')`.

Please update your BioPerl in order to avoid seeing these warning messages.

Utilities
---------

### How do I find all the ORFs in a nucleotide sequence? Antigenic sites in a protein? Calculate nucleotide melting temperature? Find repeats?

In fact, none of these functions are built into BioPerl but they are all available in the [EMBOSS](http://emboss.org) package, as well as many others. The BioPerl developers created a simple interface to [EMBOSS](http://emboss.org) such that any and all [EMBOSS](http://emboss.org) programs can be run from within BioPerl. See for more information, it's in the [bioperl-run](https://github.com/bioperl/bioperl-run) package.

If you can't find the functionality you want in BioPerl then make sure to look for it in [EMBOSS](http://emboss.org), these packages integrate quite gracefully with BioPerl. Of course, you will have to install [EMBOSS](http://emboss.org) to get this functionality.

In addition, BioPerl after version 1.0.1 contains the Pise/Bioperl modules. The [Pise package](http://www-alt.pasteur.fr/~letondal/Pise) was designed to provide a uniform interface to bioinformatics applications, and currently provides wrappers to greater than 250 such applications! Included amongst these wrapped apps are [HMMER](http://hmmer.janelia.org), [PHYLIP](https://en.wikipedia.org/wiki/PHYLIP), [BLAST](https://en.wikipedia.org/wiki/BLAST), [GENSCAN](https://en.wikipedia.org/wiki/GENSCAN), and the [EMBOSS](http://emboss.org) suite. Use of the Pise/BioPerl modules does not require installation of Pise locally as it runs through the [HTTP](https://en.wikipedia.org/wiki/HTTP) protocol of the web. Also, see the [BioMOBY](http://biomoby.org) project for information on running applications remotely.

### How do I do motif searches with BioPerl? Can I do "find all sequences that are 75% identical" to a given motif?

There are a number of approaches. Within BioPerl take a look at . Or, take a look at the [TFBS package](http://forkhead.cgb.ki.se/TFBS). This BioPerl-compliant package specializes in pattern searching of nucleotide sequence using matrices.

It's also conceivable that the combination of BioPerl and Perl's [regular expressions](https://en.wikipedia.org/wiki/Regular_expression) could do the trick. You might also consider the CPAN module (this module addresses the percent match query), but experienced users question whether its distance estimates are correct, the Unix [agrep](https://en.wikipedia.org/wiki/agrep) command is thought to be faster and more accurate. Finally, you could use [EMBOSS](http://emboss.org), as discussed in the previous question (or you could use Pise to run [EMBOSS](http://emboss.org) applications). The relevant programs would be `fuzzpro` or `fuzznuc`. Complex RNA sequence secondary structural 'motifs' can be searched with Tom Macke's RNAMotif available from the [Case group at Scripps](http://www.scripps.edu/mb/case/casegr-sh-3.5.html). See [Bio::Tools::RNAMotif](http://search.cpan.org/dist/BioPerl/Bio/Tools/RNAMotif.pm).

### How do I merge a set of sequences along with their [features and annotations](/howtos/Feature-Annotation.html)?

Try the `cat()` method in [Bio::SeqUtils](https://metacpan.org/pod/Bio::SeqUtils):

```perl
$merged_seq = Bio::SeqUtils->cat(@seqs)
```

This method uses the first sequence in the array as a foundation and adds the subsequent sequences to it, along with their features and annotations.

Running external programs
-------------------------

### How do I run BLAST from within BioPerl?

Use [BlastPlus](/howtos/BlastPlus.html).

### How do I run applications within BioPerl?

[bioperl-run](https://github.com/bioperl/bioperl-run) is the package with *application wrappers*.

### I'm trying to run  and I'm seeing error messages like `Can't locate Bio/Tools/Run/WrapperBase.pm` - how do I fix this?

This file is missing in version 1.2. Install the latest BioPerl.

Other BioPerl packages
----------------------

### What is bioperl-ext?

[bioperl-ext](https://github.com/bioperl/bioperl-ext) is a package of code for C-extensions (hence the 'ext') to BioPerl. These include interfacing to the [staden](http://staden.sourceforge.net/) IO library (the `io_lib` library) for reading in [chromatogram](https://en.wikipedia.org/wiki/chromatogram) files and which is a [Smith-Waterman](https://en.wikipedia.org/wiki/Smith_waterman) implementation.

It is likely that functionality within bioperl-ext will eventually be replaced by the [BioLib](http://biolib.open-bio.org/wiki/Main_Page) initiative.

### bioperl-ext won't compile the staden IO lib part - what do I do?

Make sure you read the README about copying files over. Some more items to check off before asking.

1.  Are you **sure** io_lib is installed where you think it is, and that the install path is seen by Perl (did you answer the questions during `perl Makefile.PL` ?)
2.  Did you copy the various missing *.h files (os.h config.h if I remember right) from your io_lib source directory into the install include directory when installing io_lib?
3.  When you ran make for io_lib did you see any errors or messages about how you should probably run "ranlib" on the library object?
4.  Did you run "ranlib" on the installed libread file(s)?

Note that newer versions of io_lib no longer support ABI sequences unless the Staden Package is also installed.

### What is bioperl-db?

The [BioPerl db](https://github.com/bioperl/bioperl-db) package contains interfaces and adaptors that work with a [BioSQL](https://en.wikipedia.org/wiki/BioSQL) database to serialize and de-serialize Bioperl objects. We *strongly recommend you use the [Git version](https://github.com/bioperl/bioperl-db) with the latest [biosql-schema](http://biosql.org).

### What is bioperl-microarray?

The [Microarray package](https://github.com/bioperl/bioperl-microarray) provides some basic tools for [microarray](https://en.wikipedia.org/wiki/microarray) functionality. It was started by [Allen Day](https://en.wikipedia.org/wiki/Allen_Day) and may need some more work before it is a mature product.

### What is bioperl-gui?

The [GUI package](https://github.com/bioperl/bioperl-gui) provides a **G**raphical **U**ser **I**nterface for interacting with sequence and feature objects. It is used as part of the [Genquire](https://en.wikipedia.org/wiki/Genquire) project.

### What is bioperl-pedigree?

The [Pedigree package](https://github.com/bioperl/bioperl-pedigree) was started by Jason Stajich and provides basic tools for interacting with pedigree data and rendering pedigree plots.

BioPerl-related questions
-------------------------

### I am using [Ensembl](http://ensembl.org). How do I do XYZ?

Though BioPerl is used in [Ensembl](http://ensembl.org), the version used is rather old and most of the sequence parsing infrastructure has evolved beyond using Bioperl directly (see below for an explanation). The best place to look for answers to any Ensembl-related matter is the Ensembl mail list and web site:

-   http://www.ensembl.org/info/about/contact.html
-   http://www.ensembl.org/info/software/core/core_tutorial.html
-   http://www.ensembl.org/info/software/Pdoc/ensembl/index.html

### Why is the version of BioPerl (v.1.2.3) used in [Ensembl](http://ensembl.org) so old? Haven't there been bug fixes?

In short (in this [thread](http://listserver.ebi.ac.uk/mailing-lists-archives/ensembl-dev/msg01745.html)):

*Ensembl doesn't make heavy use of Bioperl anymore - most of the critical things we re-wrote, mainly due to speed/memory issues. I think the short answer is that it _probably_ works with 1.5, but we don't have a strong desire to move up as certainly there are no problems with the 1.2.3 release we are using.*

In [another thread](http://listserver.ebi.ac.uk/mailing-lists-archives/ensembl-dev/msg02751.html):

*Ensembl has slowly migrated away from Bioperl, mainly due to speed issues in the bioperl framework. One project kicked around is to make a thin "facade" across Ensembl objects to make them Bioperl compliant, handling for example what the "get_Annotations()" call will actually do (if you into the look into the Annotation objects in bioperl you will get a sense of why we can't have these objects in the main Ensembl API- way too heavyweight).*
