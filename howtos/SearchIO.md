---
title: "HOWTO:SearchIO"
layout: default
---

Abstract
--------

This is a HOWTO about the [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) system, how to use it, and how one goes about writing new adaptors to different output formats. We will also describe how the modules work for outputting various formats from [Bio::Search](http://search.cpan.org/search?query=Bio::Search) objects.

### Authors

-   Jason Stajich [jason at bioperl.org](mailto:jason-at-bioperl.org)
-   Brian Osborne [briano at bioteam.net](mailto:briano@bioteam.net)

Background and Design
---------------------

One of the most common and necessary tasks in bioinformatics is parsing analysis reports so that one can write programs which can help interpret the sheer volume of data that can be produced by processing many sequences. A popular tool for comparing sequences is the [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) package from NCBI. The parsers for BLAST output are part of BioPerl's [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO).

The system was designed with the following assumptions: That all reports parsed with it could be separated into a hierarchy of components. The Result is the entire analysis for a single query sequence, and multiple Results can be concatenated together into a single file (i.e. running [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) with a fasta database as the input file rather than a single sequence). Each Result is a set of Hits for the query sequence. Hits are sequences in the searched database which could be aligned to the query sequence and met the minimal search parameters, such as e-value threshold. Each Hit has one or more High-scoring segment Pairs ) which are the alignments of the query and hit sequence. Each Result has a set of one or more Hits and each Hit has a set of one or more HSPs, and this relationship can be used to describe results from all pairwise alignment programs including BLAST, FastA, and implementations of the Smith-Waterman and Needleman-Wunsch algorithms.

A design pattern, called Factory, is utilized in object oriented programming to separate the entity which processes data from objects which will hold the information produced. In the same manner that the module is used to parse different file formats and produces objects which are compliant, we have written to produce the Bio::Search objects. Sequences are a little less complicated so there is only one primary object while Search results need three main components to represent the data processed in a file:

- Top level results: [Bio::Search::Result::ResultI](https://metacpan.org/pod/Bio::Search::Result::ResultI)
- Hits: [Bio::Search::Hit::HitI](https://metacpan.org/pod/Bio::Search::Hit::HitI)
- HSPs: [Bio::Search::HSP::HSPI](https://metacpan.org/pod/Bio::Search::HSP::HSPI)

The [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) object is then a factory which produces [Bio::Search::Result::ResultI](https://metacpan.org/pod/Bio::Search::Result::ResultI) objects that contain information about the query, the database searched, and the full collection of Hits found for the query.

The generality of the [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) approach is demonstrated by the large number of report formats that have appeared since its introduction. These formats are listed below.

| Name | Description |
|------|----------------------------------------------------------------|
|blast| BLAST (BLAST, PSIBLAST, PSITBLASTN, RPSBLAST, WUBLAST, bl2seq, WU-BLAST, BLASTZ, BLAT, Paracel BTK ) |
|fasta| FASTA -m9 and -m0 |
|blasttable| BLAST tabular -m9 or -m8 (NCBI) and -mformat 2 or -mformat 3 (WU-BLAST) |
|blastxml| NCBI BLAST XML and WU-BLAST XML |
|erpin| ERPIN versions 4.2.5 and above  |
|infernal| Infernal versions 0.7 and above |
|megablast| MEGABLAST |
|psl| UCSC formats PSL |
|waba| WABA |
|axt| [AXT](http://genome.ucsc.edu/goldenPath/help/axt.html) |
|sim4| Sim4 |
|hmmer| [HMMER](http://hmmer.janelia.org) (hmmpfam, hmmsearch, and nhmmer) |
|exonerate| Exonerate CIGAR |
|wise| Genewise -genesf |
|rnamotif| raw rnamotif output for RNAMotif versions 3.0 and above

Table 1. Bio::SearchIO input formats.

Parsing with Bio::SearchIO
--------------------------

This section is going to describe how to use the system to process reports. We'll describe [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) reports but the idea is that once you understand the methods associated with the objects you won't need to know anything special about other parsers.

See [the BLAST+ HOWTO](BlastPlus.html) for more on running BLAST.

### Using SearchIO

In order to display all these methods and what they return let's use a report as input, a simple BLASTX result:

```
BLASTX 2.2.4 [Aug-26-2002]
Reference: Altschul, Stephen F., Thomas L. Madden, Alejandro A. Schaffer, 
Jinghui Zhang, Zheng Zhang, Webb Miller, and David J. Lipman (1997), 
"Gapped BLAST and PSI-BLAST: a new generation of protein database search
programs",  Nucleic Acids Res. 25:3389-3402.
Query= gi|20521485|dbj|AP004641.2 Oryza sativa (japonica
cultivar-group) genomic DNA, chromosome 1, BAC clone:B1147B04, 3785
bases, 977CE9AF checksum.
         (3059 letters)
Database: test.fa 
           5 sequences; 1291 total letters
                                                                Score    E
Sequences producing significant alignments:                     (bits) Value
gb|443893|124775 LaForas sequence                                 92   2e-022
>gb|443893|124775 LaForas sequence
          Length = 331
 Score = 92.0 bits (227), Expect = 2e-022
 Identities = 46/52 (88%), Positives = 48/52 (91%)
 Frame = +1
Query: 2896 DMGRCSSGCNRYPEPMTPDTMIKLYREKEGLGAYIWMPTPDMSTEGRVQMLP 3051
            D+ + SSGCNRYPEPMTPDTMIKLYRE EGL AYIWMPTPDMSTEGRVQMLP
Sbjct: 197  DIVQNSSGCNRYPEPMTPDTMIKLYRE-EGL-AYIWMPTPDMSTEGRVQMLP 246 
  Database: test.fa
    Posted date:  Feb 12, 2003  9:51 AM
  Number of letters in database: 1291
  Number of sequences in database:  5
  
Lambda     K      H
   0.318    0.135    0.401 
Gapped
Lambda     K      H
   0.267   0.0410    0.140 
Matrix: BLOSUM62
Gap Penalties: Existence: 11, Extension: 1
Number of Hits to DB: 7140
Number of Sequences: 5
Number of extensions: 180
Number of successful extensions: 2
Number of sequences better than 10.0: 2
Number of HSP's better than 10.0 without gapping: 1
Number of HSP's successfully gapped in prelim test: 0
Number of HSP's that attempted gapping in prelim test: 0 
Number of HSP's gapped (non-prelim): 1
length of database: 1291
effective HSP length: 46
effective length of database: 1061
effective search space used:  1032353
frameshift window, decay const: 50,  0.1
T: 12 
A: 40
X1: 16 ( 7.3 bits)
X2: 38 (14.6 bits)
X3: 64 (24.7 bits)
S1: 32 (17.6 bits)
```

### NCBI-BLAST parsing problems

A plaintext NCBI-BLAST report like that used in the example above probably remains the most common [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) output format in use. However, since the NCBI has stated that this format can change without warning, the SearchIO BLAST parser will break from time to time. To avoid this instability, you can have NCBI-BLAST produce its report in XML format with the `-m7` command-line option. To parse blastxml reports with SearchIO, use `-format=>'blastxml'` instead of `-format=>'blast'`. Parsing BLAST XML output [may also be faster](http://article.gmane.org/gmane.comp.lang.perl.bio.general/14366). WU-BLAST offers XML output as well through the command-line option `mformat=7`, although its standard reports may work when NCBI-BLAST's do not.

### Table of Methods

| Object | Method                | Example                             | Description                                                                                                                |
|--------|-----------------------|-------------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| Result | algorithm             | BLASTX                              | algorithm string                                                                                                           |
| Result | algorithm_version    | 2.2.4 \[Aug-26-2002\]               | algorithm version                                                                                                          |
| Result | query_name           | gi|20521485|dbj|AP004641.2          | query name                                                                                                                 |
| Result | query_accession      | AP004641.2                          | query accession                                                                                                            |
| Result | query_length         | 3059                                | query length                                                                                                               |
| Result | query_description    | Oryza sativa ... 977CE9AF checksum. | query description                                                                                                          |
| Result | database_name        | test.fa                             | database name                                                                                                              |
| Result | database_letters     | 1291                                | number of residues in database                                                                                             |
| Result | database_entries     | 5                                   | number of database entries                                                                                                 |
| Result | available_statistics | effectivespaceused ... dbletters    | statistics used                                                                                                            |
| Result | available_parameters | gapext matrix allowgaps gapopen     | parameters used                                                                                                            |
| Result | num_hits             | 1                                   | number of hits                                                                                                             |
| Result | hits               |                                     | List of all [Bio::Search::Hit::GenericHit](https://metacpan.org/pod/Bio::Search::Hit::GenericHit) objects for this Result       |
| Result | rewind             |                                     | Reset the internal iterator that dictates where next_hit() is pointing, useful for re-iterating through the list of hits |


Table 2.1: All the data returned by methods used by the Result objects when the report shown above is used as input. 

Note that many of the methods shown can be used to either get or set values, but we're just showing what they get.

| Object | Method            | Example          | Description  |
|--------|-------------------|------------------|--------------------------|
| Hit    | name              | gb|443893|124775 | hit name |
| Hit    | length            | 331              | Length of the Hit sequence  |
| Hit    | accession         | 443893           | accession (usually when this is a Genbank formatted id this will be an accession number - the part after the `gb` or `emb` ) |
| Hit    | description       | LaForas sequence | hit description     |
| Hit    | algorithm         | BLASTX           | algorithm           |
| Hit    | raw_score        | 92               | hit raw score       |
| Hit    | significance      | 2e-022           | hit significance   |
| Hit    | bits              | 92.0             | hit bits           |
| Hit    | hsps              |                  | List of all [Bio::Search::HSP::GenericHSP](https://metacpan.org/pod/Bio::Search::HSP::GenericHSP) objects for this Hit |
| Hit    | num_hsps         | 1                | number of HSPs in hit |
| Hit    | locus             | 124775           | locus name  |
| Hit    | accession_number | 443893           | accession number |
| Hit    | rewind            |                  | Resets the internal counter for next_hsp() so that the iterator will begin at the beginning of the list |


Table 2.2. All the data returned by methods used by the Hit objects when the report shown above is used as input.

Many of the methods shown can be used to either get or set values, but we're just showing what they get.


| Object | Method                                               | Example                                  | Description  |
|--------|----------------------------------------------|-------|-------|
| HSP    | algorithm                                            | BLASTX                                   | algorithm     |
| HSP    | evalue                                               | 2e-022                                   | e-value                                                                                |
| HSP    | expect                                               | 2e-022                                   | alias for evalue()                                                                     |
| HSP    | frac_identical                                      | 0.884615384615385                        | fraction identical   |
| HSP    | frac_conserved                                      | 0.923076923076923                        | fraction conserved (conservative and identical replacements aka "fraction similar")             |
| HSP    | gaps                                                 | 2                                        | number of gaps                                                                         |
| HSP    | query_string                                        | DMGRCSSG ..                              | query string from alignment                                                            |
| HSP    | hit_string                                          | DIVQNSS ...                              | hit string from alignment                                                              |
| HSPt | homology_string                                     | D+ + SSGCN ...                           | string from alignment  |
| HSP    | length('total')                                  | 52                                       | length of HSP (including gaps)                                                         |
| HSP    | length('hit')                                    | 50                                       | length of hit participating in alignment minus gaps                                    |
| HSP    | length('query')t                               | 156                                      | length of query participating in alignment minus gaps                                  |
| HSPt | hsp_length                                          | 52                                       | Length of the HSP (including gaps) alias for length('total')                       |
| HSPt | frame                                                | 0                                        | $hsp->query->frame,$hsp->hit->frame                                        |
| HSP    | num_conserved                                       | 48                                       | number of conserved (conservative replacements, aka "similar") residues                |
| HSP    | num_identical                                       | 46                                       | number of identical residues                                                           |
| HSPt | rank                                                 | 1                                        | rank of HSP                                                                            |
| HSP    | seq_inds('query','identical')               | (966,971,972,973,974,975 ...)            | identical positions as array                                                           |
| HSP    | seq_inds('query','conserved-not-identical') | (967,969)                                | conserved, but not identical positions as array                                        |
| HSP    | seq_inds('query','conserved')               | (966,967,969,971,973,974,975, ...)       | conserved or identical positions as array                                              |
| HSP    | seq_inds('hit','identical')                 | (197,202,203,204,205, ...)               | identical positions as array                                                           |
| HSP    | seq_inds('hit','conserved-not-identical')   | (198,200)                                | conserved not identical positions as array                                             |
| HSP    | seq_inds('hit','conserved',1)               | (197,202-246)                            | conserved or identical positions as array, with runs of consecutive numbers compressed |
| HSPt | score                                                | 227                                      | score   |
| HSP    | bits                                                 | 92.0                                     | score in bits                                                                          |
| HSP    | range('query')                                   | (2896,3051)                              | start and end as array                                                                 |
| HSP    | range('hit')                                     | (197,246)                                | start and end as array                                                                 |
| HSP    | percent_identity                                    | 88.4615384615385                         | % identical  |
| HSP    | strand('hit')                                    | 1                                        | strand of the hit                                                                      |
| HSP    | strand('query')                                  | 1                                        | strand of the query                                                                    |
| HSP    | start('query')                                   | 2896                                     | start position from alignment                                                          |
| HSP    | end('query')                                     | 3051                                     | end position from alignment                                                            |
| HSP    | start('hit')                                     | 197                                      | start position from alignment                                                          |
| HSP    | end('hit')                                       | 246                                      | end position from alignment                                                            |
| HSP    | matches('hit')                                   | (46,48)                                  | number of identical and conserved as array                                             |
| HSP    | matches('query')                                 | (46,48)                                  | number of identical and conserved as array                                             |
| HSP    | get_aln | sequence alignment           | [Bio::SimpleAlign](https://metacpan.org/pod/Bio::SimpleAlign) object  |
| HSPt | hsp_group   | *Not available in this report* | Group field from WU-BLAST reports run with -topcomboN or -topcomboE specified          |
| HSP    | links                                                | *Not available in this report* | Links field from WU-BLAST reports run with -links showing consistent HSP linking       |

Table 2.3. All the data returned by methods used by the HSP objects when the report shown above is used as input. 

Many of the methods shown can be used to either get or set values, but we're just showing what they get. Also note that `frac_conserved` is only useful for protein alignments, if used with nucleotide alignments it will be same as `frac_identical`.

### Using the methods

The tables above show that a method can return a string, an array, or an object. When an object is returned some additional code will probably be needed to get the data of interest.

#### get_aln()

For example, if you wanted a printable alignment after you'd parsed BLAST output you could use the `get_aln()` method, retrieve a [Bio::SimpleAlign](https://metacpan.org/pod/Bio::SimpleAlign) object and use it like this:

```perl
use Bio::AlignIO;

# $aln will be a Bio::SimpleAlign object
my $aln = $hsp->get_aln; 
my $alnIO = Bio::AlignIO->new(-format => "msf", 
                              -file => ">hsp.msf"); 
$alnIO->write_aln($aln);
```

On one hand it appears to be a complication, but by entering the worlds of the and objects you now have access to their functionality and flexibility. This is the beauty of BioPerl.

#### ambiguous_aln()

Some of these methods deserve a bit more explanation since they do more than simply extract data directly from the output. For example, the `ambiguous_aln()` method is designed to tell us whether two or more HSPs from a given hit overlap, and whether the overlap refers to the queries or the hits, or both. One situation is where overlaps would be found in one but not the other arises where there are repeats in the query or hit. The `ambiguous_aln()` method will return one of these 4 values:

| Value | Description |
|------|----------------------------------------------------------------|
| q   | query sequence contains overlapping sub-sequences while hit sequence does not |
| s   | hit sequence contains overlapping sub-sequences while query does not |
| qw  | query and hit sequences contain overlapping sub-sequences relative to each other |
| -   | query and hit sequence do not contain multiple domains relative to each other OR both contain the same distribution of similar domains |

Table 2.4. Values used by the `ambiguous_aln` method.

#### seq_inds()

Another method that's useful in dissecting an HIT is the `seq_inds()` method of the HSP object. What this method does is tell us what the positions are of all the identical, conserved, mismatched, or gap ("identical", "conserved", "nomatch", "gap") residues in the query or hit sequence as deduced from the HSP alignment. The returned positions refer to the query or hit sequence ("sbjct" is synonymous with "hit"). It could be used like this:

```perl
# put all the conserved matches in query strand into an array
my @str_array = split "",$hsp->query_string; 
for ( $hsp->seq_inds('query','conserved') ){
    push @conserved,$str_array[$_ - 1];
}
```

For 'gaps', the returned positions are the sequence indices prior to a gap insertion; if the gap insert is greater than 1 then the position is repeated based on the number of insertions. You can regain the gap length using a simple temporary hash:

```perl
# grab hsp
my %gap_pos; 
for my $pos ($hsp->seq_inds('query'=>'gaps')) {
    $gap_pos{$pos}++;
}
print "Ind: $_ Gaps: $gap_pos{$_} " for sort {$a <=> $b} keys %gap_pos;
```

`seq_inds()` can be very useful for extracting the mismatch bases in an alignment. If you wanted to figure out which bases are not matching in an alignment you could use `seq_inds` to get these positions and then extract out these specific bases from the alignment. *Note* translated sequences in a HSP (such as those used for TBLASTN, BLASTX, etc.) are by nature ambiguous as the sequence coordinates reported in the HSP map back to the original (nucleotide) sequence; under these circumstances all redundant nucleotide positions are returned for that match.

One final note when using `seq_inds()`: if you want a list of ranges or only care about the positions of the gaps (not the gap length) and don't want repeated positions, you can use `my @inds = $hsp->seq_inds('query'=>'gaps',1)`; the last argument is a boolean flag with collapses consecutive positions into ranges and single sequence positions.

#### frame()

In most cases the [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) methods extract data directly from output but there's one important exception, the `frame()` method of the HSP object. Instead of using the values in the [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) report it converts them to values according to the GFF specification, which is a format used by many BioPerl modules involved in gene annotation.

Specifically, the `frame()` method returns 0, 1, or 2 instead of the expected -3, -2, -1, +1, +2, or +3 in BLAST. GFF frame values are meaningful relative to the strand of the hit or query sequence so in order to reconstruct the BLAST frame you need both the strand, 1 or -1, and the GFF frame value:

```perl
my $blast_frame = ($hsp->query->frame + 1) * $hsp->query->strand;
```

### Analyzing a single report in different ways

Another common example is analyzing data from a single [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) report several different ways, such as sorting hits based on particular criteria then printing all hits and HSPs. One could iterate through the list twice, once for sorting the objects and then a second time for printing. Note that in this script:

-   A object is passed to the subroutines, not the SearchIO object in `$blast_report`
-   You must use `rewind` to reset the iterator for the object since there are two iterations through this object (one in each subroutine)

```perl
# ... 
my $result = $blast_report->next_result; 
sort_results($result);
$result->rewind;
print_blast_results($result);

sub sort_results{
    my $result = shift;
    my @hits;
    while( my $hit = $result->next_hit() ) {
        push @hits, $hit;
    }
    # sort by accessions
    my @acc = sort { $a->accession cmp $b->accession } @hits;
    print join("\t", $_->accession, $_->description ) . "\n" for @acc;
}

sub print_blast_results {
    my $result = shift;
    while ( my $hit = $result->next_hit() ) {
        while( my $hsp = $hit->next_hsp() ) {
            print join(", ", $hit->name, $hsp->bits) . "\n";
        }
    }
}
```

If you parsed a report already and want to reset the parser (i.e. if you sent the SearchIO object to the subs above instead of the object, then iterated through everything twice), you would need to reset the SearchIO object itself by using `seek($blast_report->_fh, 0);`. We don't recommend doing this for two reasons. First, each round of parsing takes the same length of time since you start parsing the report from scratch, so you'll take a time hit. Second, if you saved objects from the first round of parsing you will take a memory hit, because a new set of objects is generated for each subsequent round of parsing the report. Hence, we use the already-generated object in the subroutines instead.

To simplify things here a bit more, you could simply grab all the hits at once using `$result->hits` before you sort them, as demonstrated in a previous example. The `hits` method does not use the iterator; it simply returns a list of [Bio::Search::Hit::BlastHit](https://metacpan.org/pod/Bio::Search::Hit::BlastHit) objects. Hence, there is no need to `rewind` the object:

```perl

# ... 
&sort_results($result); 
&print_blast_results($result);

sub sort_results {
    my $result = shift;
    my @acc = sort {$a->accession cmp $b->accession} $result->hits;
    print join("t", $_->accession,$_->description),"\n" for @acc; 
}

sub print_blast_results {
    my $result = shift;
    while ( my $hit = $result->next_hit() ) {
        while( my $hsp = $hit->next_hsp() ) {
            print join(", ", $hit->name, $hsp->bits) . "\n";
        }
    }
}
```

Similar methods exist for objects to rewind the iterator for HSP's (`$hit->rewind`) and to grab all HSP's (`$hit->hsps`).

Sorting
-------

One frequently-asked question has to do with getting sorted output from a report, or sorting hits or HSPs just as they're sorted in the input file. There's little in the way of sorting in Bio::SearchIO's methods, generally speaking you'll just use a standard Perl approach. Here is an example that sorts hits according to their bit scores:

```perl
my @hits = $result->hits;
for my $hit ( sort { $a->bits `<=>` $b->bits } @hits ) {
  # Do something...
}
```

Creating Reports for SearchIO
-----------------------------

One note on creating reports that can be parsed by [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO): the developers haven't attempted to parse all the possible reports that could be created by programs with many command-line options, like [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual). Certainly you should be able to parse reports created using the default settings, but if you're running [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual), say, using some special set of options and you've encountered a parsing problem this may be the explanation.

For example, one can currently parse [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) output created with the default settings as well as the reports created when using the "-m 8" or "-m 9" options (use `-format=>'blasttable'`) or the "-m 7" XML-formatted reports (use `-format=>'blastxml'`) but it's still possible to find sets of options that can't parse.

You might also find it useful not to have to create reports as files. [Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) is aware of `STDIN` so you can pipe output from the search application directly to it (on operating systems that allow such things). It could look something like this:

```perl
use strict; 
use Bio::SearchIO;

my $fh; 
my $fasta = "/usr/local/bin/fasta34"; 
my $library = "hs.seq"; 
my $query = "deserts.seq"; 
my $options = "-E 0.01 -m 0 -d 10 -Q"; 
my $command = "$fasta $options $query $library";

open $fh,"$command |" || die("cannot run fasta cmd of $command: $! ");

my $searchio = Bio::SearchIO->new(-format => 'fasta', -fh => $fh);
```

Implementation
--------------

This section is going to describe how the SearchIO system was implemented, it is probably not necessary to understand all of this unless you are curious or want to implement your own parser. We have utilized an event-based system to process these reports. This is analogous to the SAX system used to process XML documents. Event based parsing can be simply thought of as simple start and end events. When you hit the beginning of a report a start event is thrown, when you hit the end of the report an end event is thrown. So the report events are paired, and everything else that is thrown in between the paired start and end events is related to that report.

Another way to think of it is as if you pick a number and color for a card in a standard deck. Let's say you pick red and 2. Then you start dealing cards from our deck and pile them one on top of each other. When you see your first red 2 you start a new pile, and start dealing cards onto that pile until you see the next red 2. Everything in your pile that happened between when you saw the beginning red 2 and ending red 2 is data you'll want to keep and process. In the same way all the events you see between a pair of start and end events (like 'report' or 'hsp') are data associated with object or child object in its hierarchy. A listener object processes all of these events, in our example the listener is the table where the stack of cards is sitting, and later it is the hand which moves the pile of cards when a new stack is started. The listener will take the events and process them. We've neglected to tell you of a third event that is thrown and caught. This is the characters event in [SAX] terminology, which is simply data. So one sends a start event, then some data, then an end event. This process is analogous to a finite state machine in computer science where what we do with data received is dependent on the state we're in. The state that the listener is in is affected by the events that are processed.

A small caveat: in an ideal situation a processor would throw events and not need to maintain any state information, it would just be processing data and the listener would manage the information and state. However, a lot of the parsing of these human-readable reports requires contextual information to apply the correct regular expressions. So in fact the event thrower has to know what state it is in and apply different methods based on this. In contrast the [XML parsers] simply keep track of what state they are in, but can process all the data with the same system of reading the tag and sending the data that is in between the [XML] start and end tags.

All of this framework has been built up, so to implement a new parser one only needs to write a module that produces the appropriate start and end events and the existing framework will do the work of creating the objects for you. Here's how we've implemented event-based parsing for . The is just the front-end to this process, in fact the processing of these reports is done by different modules in the `Bio/SearchIO/` directory. So if you look at your BioPerl distribution and the modules in `Bio/SearchIO` you'll see modules in there like `blast.pm`, `fasta.pm`, `blastxml.pm`, `SearchResultEventBuilder.pm`, `EventHandlerI.pm` (depending on what version of the toolkit there may be more modules in there). There is also a `SearchWriterI.pm` and `Writer` directory in there but we'll save that for later.

Let's use the `blast.pm` module as an example to describe the relationship of the modules in this directory (could have substituted any of the other format parsers like `fasta.pm` or `blastxml.pm` - these are always lowercase for historical reasons). The module has some features you should look for - the first is the hash in the `BEGIN` block called `%MAPPING`. These key/value pairs here are the shorthand for how we map events from this module to general event names. This is only necessary because if we have an XML processor (see the `blastxml.pm module`) the event names will be the same as the XML tag names (like `Hsp_bit-score` in the NCBI BLAST XML DTD). So to make this general we'll make sure all of the events inside our parser map to the values in the `%MAPPING` hash - we can call them whatever we want inside this module. Some of the events map to hash references (like `Statistics_db-len`) and this is so we can map multiple values to the same top-level attribute field but we know they will be stored as a hash value in the subsequent object (in this example, keyed by the name `dbentries`). The capital "RESULT", "HSP", or "HIT" in the value name allow us to encode the event state in the event so we don't have to pass in two values. It is also easy for someone to quickly read the list of events and know which ones are related to Hits and which ones are related to HSPs. The listener in our architecture is the . This object is attached as a listener through the method `add_EventListener`. In fact you could have multiple event listeners and they could do different things. In our case we want to create [Bio::Search](http://search.cpan.org/search?query=Bio::Search) objects, but an event listener could just as easily be writing data directly into a database or writing to a file, based on the events. The `SearchResultEventBuilder` takes the events thrown by the SearchIO classes and builds the appropriate object from it.

Sometimes special objects are needed that are extensions beyond what the or objects are meant to represent. For this case we have implemented so that it can use factories for creating its resulting Bio::Search objects - see the `::_initialize` method for an example of how this can be set.

Writing and formatting output
-----------------------------

Often people want to write back out a [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) report for users who are most comfortable with that output or if you want to visualize the context of a weakly aligned region and use human intuition to score the confidence of a putative homologue. The modules are for creating output using the information.

[Bio::SearchIO](https://metacpan.org/pod/Bio::SearchIO) currently creates output in a few different formats: text (recreating something like the BLAST report itself, in part or entirely), HTML, BSML, "ResultTable" (tab-delimited format), "HSPTable" (tab-delimited, for HSPs), and Gbrowse GFF.

The simplest way to output data in HTML format is as follows.

```perl

my $writerhtml = new Bio::SearchIO::Writer::HTMLResultWriter(); 
my $outhtml = new Bio::SearchIO(-writer => $writerhtml,
                                -file   => ">searchio.html");

# get a result from Bio::SearchIO parsing or build it up in memory
$outhtml->write_result($result);
```

If you want to output multiple results into a single HTML file, do the following:

```perl

my $writerhtml = new Bio::SearchIO::Writer::HTMLResultWriter(); 
my $outhtml = new Bio::SearchIO(-writer => $writerhtml,
                                -file   => ">searchio.html");

# Loop through all the results, successively adding each one to the bottom of # the HTML report
while ( $result = $searchio->next_result() ) {  
    $outhtml->write_report($result);
}
```

If you wanted to get the output as a string rather than write it out to a file, simply use the following.

```perl
$writerhtml->to_string($result);
```

The supports setting your own remote database url for the sequence links in the event you'd like to point to your own SRS or local HTTP-based connection to the sequence data. Simply use the `remote_database_url` method which accepts a sequence type as input (protein or nucleotide).

You can also override the `id_parser()` method to define what the unique IDs are from these sequence ids in the event you would like to use something other than the accession number that is gleaned from the sequence string.

If your data is instead stored in a database you could build the Bio::Search objects up in memory directly from your database and then use the Writer object to output the data.

Extending 
----------

The framework for is just a starting point for parsing these reports and creating objects which represent the information. If you would like to create your own set of objects which extend the current functionality we have built the system so that it will support this. For example, you may have built your own HSP object which supports a special operation like `realign_with_sw()`, which might realign the HSP via a Smith-Waterman algorithm, pulling extra bases from the flanking sequence. You might call your module `Bio::Search::HSP::RealignHSP` and put it in a file called `Bio/Search/HSP/RealignHSP.pm`. Note that you don't have to put this file directly in the BioPerl source directory - you can create your own local directory structure that is in parallel to the BioPerl release source code as long as you have updated your `PERL5LIB` to contain your local directory or you are using the `use lib` directive in your script. Also, you don't have to use the namespace as namespaces don't mean anything to Perl with respect to object inheritance, but do we recommend you name things in a logical manner so that others might read and understand your code (and if you feel encouraged to donate your code to the project it might easily integrated with existing modules).

So, you're going to write your new special module, you do need to make sure it inherits from the base object. Additionally unless you want to reimplement all the initialization state in the current you should just plan to extend that object. You need to follow the chained constructor system that we have set up so that the arguments are properly processed. Here is a sample of what your code might look like (don't forget to write your own [POD] so that it will be documented, we've left it off here to keep things simple).

```perl
package Bio::Search::HSP::RealignHSP; 
use strict; 
use Bio::Search::HSP::GenericHSP; 
use vars qw(@ISA); 
# for inheritance 
@ISA = qw(Bio::Search::HSP::GenericHSP);
# RealignHSP inherits from GenericHSP

sub new {
    my ($class,@args) = @_;
    my $self = $class->SUPER::new(@args); # chained contructor
    # process the 1 additional argument this object supports
    my ($ownarg1) = $self->_rearrange([OWNARG1],@args); 
    return $self; # remember to pass the object reference back out   
}

sub realign_hsp {
    my ($self) = @_;
    # implement my special realign method here
}
```

The above code gives you a skeleton of how to start to implement your object. To register it so that it is used when the system makes HSPs you just need to call a couple of functions. The code below outlines them.

```perl

use Bio::SearchIO; 
use Bio::Search::HSP::HSPFactory; 
use Bio::Search::Hit::HitFactory;

# setup the blast parser, you can do this with and SearchIO parser however
my $searchio = Bio::SearchIO->new(-file => $blastfile,
                                  -format =>'blast'); 

# build HSP factory with a certain type of HSPs to make
# the default is Bio::Search::HSP::GenericHSP
my $hspfact = Bio::Search::HSP::HSPFactory->new(-type =>
                  'Bio::Search::HSP::RealignHSP');

# if you wanted to replace the Hit factory you can do this as well
# additionally there is an analagous
# Bio::Search::Result::ResultFactory for setting custom Result objects
my $hitfact = Bio::Search::Hit::HitFactory->new(-type =>
                  'Bio::Search::Hit::SUPERDUPER_Hit');

$searchio->_eventHandler->register_factory('hsp', $hspfact);
$searchio->_eventHandler->register_factory('hit', $hitfact);
```

We have to register the HSPFactory, which is the object which will create HSPI objects, by allowing this to be built by a factory rather than a hard-coded `Bio::Search::HSP::GenericHSP->new(...)` call. We are allowing the user to take advantage of the whole parsing structure and the ability to slot their own object into the process rather than re-implementing very much. We think this is very powerful and worth the system overhead, but it may not permit this to be as efficient in parsing as we would like. Future work will hopefully address speed and memory issues with this parser. Volunteers and improvement code are always welcome.

Speed improvements with lightweight objects
-------------------------------------------

The approaches described above will create a lot of objects, one for each of the components of a report. When you have 2000 hits in a BLASTX result there will be quite a few objects built, and a lot of memory consumed. It's possible that you'll want to use an approach that's less memory-intensive if your result sets are large. One option is to use the tabular output from BLAST when dealing with large datasets (''-m 8'' or ''-m 9'').

There are other workarounds depending on what kind of data you want. We designed to be a modular system which separates parsing the data from instantiating objects by throwing events (like SAX) and having a listener build objects from these events. So one can instantiate a different listener which builds simpler objects and throws away the data you don't want.

Here is an example of such a lightweight listener - `Bio::SearchIO::FastHitEventBuilder` - it just throws away the HSPs and only builds Result and Hit objects.

```perl

use Bio::SearchIO; 
use Bio::SearchIO::FastHitEventBuilder; 
my $searchio = new Bio::SearchIO(-format => $format, -file => $file);

$searchio->attach_EventHandler(Bio::SearchIO::FastHitEventBuilder->new); 
while ( my $r = $searchio->next_result ) {
    while( my $h = $r->next_hit ) {
        # Hits will NOT have HSPs
        print $h->significance,"\n";
    }
}
```

You could also build your own listener object - SearchResultEventBuilder and FastHitEventBuilder are two example implementations that specify the type of Result/Hit/HSP objects that are created by the listeners. You could create some lightweight Hit and HSP objects and have SearchResultEventBuilder create these instead of the default full-fledged ones.

The whole parser/listener design assumes that you want to process all the data for a result before moving on to the next one. From the listener's standpoint this means you have to store all the data you just got from the parser. Whether this is in memory, or potentially stored in a temporary file or database, would be up to the implementation.

SearchIO History
----------------

The BioPerl project has produced a number of parsers for the ubiquitous BLAST report. Steve Chervitz wrote one of the first BioPerl modules for BLAST called . Ian Korf allowed us to import and modify his BPlite (Blast Parser) module into BioPerl. This is of course in a sea of BLAST parsers that have been written by numerous people, but we will only cover the ones associated directly with the BioPerl project in this document.

One of the reasons for writing yet another [BLAST](http://www.ncbi.nlm.nih.gov/bookshelf/br.fcgi?book=helpblast&part=CmdLineAppsManual) parser in the form of is that even though both and did their job correctly, and could parse WU-BLAST and NCBI-BLAST output, they did not adequately genericize what they were doing. By this we mean everything was written around the BLAST format and was not easily applicable to parsing, say, FASTA alignments or a new alignment format. One of the powerful features of the object-oriented framework in BioPerl is the ability to read in, say, a sequence file in different formats or from different data sources like a database or XML-flatfile, and have the program code process the sequences objects in the same manner. We wanted to have this capability in place for analysis reports as well and thus the generic design of the module.

### Avoiding possible confusion

There had been some confusion about the names and functions of the objects for historical reasons.

Both Steve Chervitz and Jason Stajich had implemented parsers in this system. The basic objects Jason has created are called `Bio::Search::XXX::GenericXXX` where, again, `XXX` is HSP, Hit, and Result. Most of the implementations use these simple objects for sorting the data. Steve created the psiblast parser (which was later merged into the module) and a host of objects named `Bio::Search::XXX::BlastXXX` where `XXX` is HSP, Hit, and Result. These objects have additional functions related to output from BLAST.

The important take home message is that you cannot assume that methods in the `BlastXXX` objects are in fact implemented by the `GenericHSP` objects. More likely than not the `BlastXXX` objects will be deprecated and dismantled as their functionality is ported to the `GenericHSP` objects. For this reason we only discuss the Generic* objects, though we used the terms 'hit', 'HSP', and 'result'.
