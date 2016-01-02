---
title: "AlignIO and SimpleAlign HOWTO"
layout: howto
---

## Authors

Brian Osborne *briano at bioteam.net*

Peter Schattner

## Abstract

This is a [HOWTO](/howtos/index.html) that talks about using [Bio::AlignIO](https://metacpan.org/pod/Bio::AlignIO) and [Bio::SimpleAlign](https://metacpan.org/pod/Bio::SimpleAlign)) to create and analyze alignments. It also discusses how to run various applications that create alignment files. See the [list of alignment formats](/formats/alignment_formats/index.html) for details on each format.

## AlignIO

Data files storing multiple sequence alignments appear in varied formats and is the Bioperl object for conversion of alignment files. AlignIO is patterned on the object and its commands have many of the same names as the commands in . Just as in the object can be created with `-file` and `-format` options:

```perl
use Bio::AlignIO; my $io = Bio::AlignIO->new(
                         -file => "receptors.aln", 
                         -format => "clustalw" );
```

If the `-format` argument isn't used then Bioperl will try and determine the format based on the file's suffix, in a case-insensitive manner. Here is the current set of input formats:

| [Format](/formats/alignment_formats/index.html)    | Suffixes                      | Comment     |
|-----------|-------------------------------|-------------|
| bl2seq    |                               |             |
| [ClustalW](/formats/alignment_formats/ClustalW_multiple_alignment_format.html)  | `aln`                         |             |
| emboss    | `water needle`                |             |
| [fasta](/formats/alignment_formats/FASTA_multiple_alignment_format.html)     | `fasta fast seq fa fsa nt aa` |             |
| [maf](/formats/alignment_formats/MAF_multiple_alignment_format.html)       | `maf`                         |             |
| [mase](/formats/alignment_formats/Mase_multiple_alignment_format.html)      |                               | Seaview     |
| [mega](/formats/alignment_formats/MEGA_multiple_alignment_format.html)      | `meg ega`                     |             |
| [meme](/formats/alignment_formats/MEME_multiple_alignment_format.html)      | `meme`                        |             |
| metafasta |                               |             |
| [msf](/formats/alignment_formats/MSF_multiple_alignment_format.html)       | `msf pileup gcg`              | GCG         |
| [nexus](/formats/alignment_formats/NEXUS_multiple_alignment_format.html)     | `nexus nex`                   |             |
| pfam      | `pfam pfm`                    | Pfam        |
| [phylip](/formats/alignment_formats/PHYLIP_multiple_alignment_format.html)    | `phylip phlp phyl phy ph`     | interleaved |
| [po](/formats/alignment_formats/PO_multiple_alignment_format.html)        |                               | POA         |
| [prodom](/formats/alignment_formats/Prodom_multiple_alignment_format.html)    |                               |             |
| psi       | `psi`                         | PSI-BLAST   |
| [selex](/formats/alignment_formats/SELEX_multiple_alignment_format.html)     | `selex slx selx slex sx`      | HMMER       |
| [stockholm](/formats/alignment_formats/Stockholm_multiple_alignment_format.html) | `stk`                         | Rfam, Pfam  |
| [XMFA](/formats/alignment_formats/XMFA_multiple_alignment_format.html)      | `xmfa`                        |             |
| arp       | `arp`                         | Arlequin    |
Table 1. AlignIO input formats

The emboss format refers to the output of the water, needle, matcher, stretcher, merger, and supermatcher applications. See <http://emboss.sourceforge.net>.

[Bio::AlignIO](https://metacpan.org/pod/Bio::AlignIO) reads many formats but does not write in every format (the same is true for [Bio::SeqIO](https://metacpan.org/pod/Bio::SeqIO)). [AlignIO](https://metacpan.org/pod/Bio::AlignIO) currently supports output in these formats:

-   fasta
-   mase
-   selex
-   clustalw
-   msf
-   phylip
-   po
-   stockholm
-   XMFA
-   metafasta

The basic syntax for AlignIO is similar to that of SeqIO:

```perl
use Bio::AlignIO;

$in = Bio::AlignIO->new(-file => "inputfilename" ,
                        -format => 'fasta');

$out = Bio::AlignIO->new(-file => ">outputfilename",
                         -format => 'pfam');

while ( my $aln = $in->next_aln ) {
    $out->write_aln($aln);
}
```

The returned object, `$aln`, is a [Bio::SimpleAlign](https://metacpan.org/pod/Bio::SimpleAlign) object rather than a [Bio::Seq](https://metacpan.org/pod/Bio::Seq) object.

[Bio::AlignIO](https://metacpan.org/pod/Bio::AlignIO) also supports the tied filehandle syntax described for [Bio::SeqIO](https://metacpan.org/pod/Bio::SeqIO).

## SimpleAlign

Once one has identified a set of similar sequences, one often needs to create an alignment of those sequences.

[Bio::AlignIO](https://metacpan.org/pod/Bio::AlignIO) objects can be produced by bioperl-run alignment creation objects (e.g. Clustalw.pm, BLAST's bl2seq, TCoffee.pm, and Lagan.pm or they can be read in from files of multiple-sequence alignments in various formats using AlignIO.

Some of the manipulations possible with include:

* `slice()` : Obtaining an alignment `slice`, that is, a subalignment inclusive of specified start and end columns. Sequences with no residues in the slice are excluded from the new alignment and a warning is printed.
* `column_from_residue_number()` : Finding column in an alignment where a specified residue of a specified sequence is located.
* `consensus_string()` : Making a consensus string. This method includes an optional threshold parameter, so that positions in the alignment with lower percent-identity than the threshold are marked by *?* in the consensus
* `percentage_identity()` : A fast method for calculating the average percentage identity of the alignment
* `consensus_iupac()` : Making a consensus using IUPAC ambiguity codes from DNA and RNA.  

Skeleton code for using some of these features is shown below. More detailed, working code is in *examples/align_on_codons.pl*, also in the *examples/align* directory. Additional documentation on methods can be found in [Bio::SimpleAlign](https://metacpan.org/pod/Bio::LocatableSeq) and [Bio::LocatableSeq](https://metacpan.org/pod/Bio::LocatableSeq).

```perl

use Bio::SimpleAlign;

$aln = Bio::SimpleAlign->new('t/data/testaln.dna');

$threshold_percent = 60;

$consensus_with_threshold = $aln->consensus_string($threshold_percent);

# dna/rna alignments only
$iupac_consensus = $aln->consensus_iupac();

$percent_ident = $aln->percentage_identity;

$seqname = '1433_LYCES'; 

$pos = $aln->column_from_residue_number($seqname, 14);

```

## Aligning 2 sequences with Blast using bl2seq and AlignIO

As an alternative to Smith-Waterman, two sequences can also be aligned in Bioperl using the `bl2seq` option of Blast within the object. To get an alignment - in the form of a object - using `bl2seq`, you need to parse the bl2seq report with the file format reader as follows:

```perl

$factory = Bio::Tools::Run::StandAloneBlast->new(-outfile => 'bl2seq.out');
$bl2seq_report = $factory->bl2seq($seq1, $seq2);

# Use AlignIO.pm to create a SimpleAlign object from the bl2seq report
$str = Bio::AlignIO->new(-file => 'bl2seq.out',
                        -format => 'bl2seq');
$aln = $str->next_aln();
```

## Aligning multiple sequences with Clustalw.pm and TCoffee.pm

For aligning multiple sequences (i.e. two or more), Bioperl offers a perl interface to the `clustalw`, `muscle`, and `tcoffee` programs. TCoffee is a more recent program - derived from clustalw - which has been shown to produce better results for local MSA.

To use these capabilities, the `clustalw`, `muscle`, or `tcoffee` programs need to be installed on the host system. In addition, the environmental variables `CLUSTALDIR` and `TCOFFEEDIR` need to be set to the directories containg the executables.

From the user's perspective, the Bioperl syntax for calling  [Bio::Tools::Run::Alignment::Clustalw](https://metacpan.org/pod/Bio::Tools::Run::Alignment::Clustalw), [Bio::Tools::Run::Alignment::TCoffee](https://metacpan.org/pod/Bio::Tools::Run::Alignment::TCoffee), or [Bio::Tools::Run::Alignment::Muscle](https://metacpan.org/pod/Bio::Tools::Run::Alignment::Muscle) or is almost identical. The only differences are the names of the modules themselves appearing in the initial `use`, and constructor statements and the names of the some of the individual program options and parameters.

In either case, initially, a factory object must be created. The factory may be passed most of the parameters or switches of the relevant program. In addition, alignment parameters can be changed or examined after the factory has been created. Any parameters not explicitly set will remain as the underlying program's defaults. Application output is returned in the form of a object. It should be noted that some `clustalw` and `TCoffee` parameters and features (such as those corresponding to tree production) have not been implemented yet in the Perl interface.

Once the factory has been created and the appropriate parameters set, one can call the method align() to align a set of unaligned sequences, or profile_align() to add one or more sequences or a second alignment to an initial alignment. Input to align() consists of a set of unaligned sequences in the form of the name of file containing the sequences or a reference to an array of objects. Typical syntax is shown below. We illustrate with `clustalw`, but a similar syntax - except for the module name - would work for `TCoffee`, or `muscle`.

```perl

use Bio::Tools::Run::Alignment::Clustalw;

$factory = Bio::Tools::Run::Alignment::Clustalw->new(-matrix => 'BLOSUM'); 
$ktuple = 3; 
$factory->ktuple($ktuple);

# @seq_array is an array of Bio::Seq objects
$seq_array_ref = @seq_array;

$aln = $factory->align($seq_array_ref);
```

Clustalw.pm and TCoffee.pm can also align two (sub)alignments to each other or add a sequence to a previously created alignment by using the `profile_align()` method. For further details on the required syntax and options for the profile_align() method, the user is referred to [Bio::Tools::Run::Alignment::Clustalw](https://metacpan.org/pod/Bio::Tools::Run::Alignment::Clustalw) and [Bio::Tools::Run::Alignment::TCoffee](https://metacpan.org/pod/Bio::Tools::Run::Alignment::TCoffee). The user is also encouraged to examine the script *clustalw.pl* in the *examples/align* directory.

## Manipulating clusters of sequences Cluster and ClusterIO

Sequence alignments are not the only examples in which one might want to manipulate a group of sequences together. Such groups of related sequences are generally referred to as clusters. Examples include Unigene clusters and gene clusters resulting from clustering algorithms being applied to microarray data.

The Bioperl Cluster and modules are available for handling sequence clusters. Code to read in a Unigene cluster (in the NCBI XML format) and then extract individual sequences for the cluster for manipulation might look like this:

```perl

my $stream = Bio::ClusterIO->new(-file => "Hs.data", -format => "unigene");

while ( my $in = $stream->next_cluster ) {
    print $in->unigene_id() . "\n";
    while ( my $sequence = $in->next_seq ) {
        print $sequence->accession_number . "\n";
    }
}
```

See [Bio::Cluster::UniGene](https://metacpan.org/pod/Bio::Cluster::UniGene) for more details.


