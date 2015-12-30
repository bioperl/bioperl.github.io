---
title: "Short Read assemblies using maq HOWTO"
layout: howto
---

Abstract
--------

A detailed description of [Bio::Tools::Run::Maq](https://metacpan.org/pod/Bio::Tools::Run::Maq), a wrapper for creating short-read assemblies with the [Maq](http://maq.sourceforge.net/maq-man.shtml) assembler.

Author
------

Mark A. Jensen

[Fortinbras Research](http://fortinbras.us)

`maj -at- fortinbras -dot- us`

Synopsis
--------

```perl
 # create an assembly
  $maq_fac = Bio::Tools::Run::Maq->new();
  $maq_assy = $maq_fac->run( 'reads.fastq', 'refseq.fas' );
  # paired-end 
  $maq_assy = $maq_fac->run( 'reads.fastq', 'refseq.fas', 
                             'paired-reads.fastq');
  # be more strict
  $maq_fac->set_parameters( -c2q_min_map_quality => 60 );
  $maq_assy = $maq_fac->run( 'reads.fastq', 'refseq.fas', 
                             'paired-reads.fastq');
 
  # run maq commands separately
  $maq_fac = Bio::Tools::Run::Maq->new(
     -command => 'pileup',
     -single_end_quality => 1 );
  $maq_fac->run_maq( -bfa => 'refseq.bfa',
                     -map => 'maq_assy.map',
                     -txt => 'maq_assy.pup.txt' );
```

Dependencies and installation
-----------------------------

[Bio::Tools::Run::Maq](https://metacpan.org/pod/Bio::Tools::Run::Maq) and other Bioperl dependencies are found in the [bioperl/bioperl-run](https://github.com/bioperl/bioperl-run) repository. Other dependencies are found in [bioperl/bioperl-live](https://github.com/bioperl/bioperl-live).

Module description
------------------

This module provides a wrapper interface for [Heng Li](https://github.com/lh3)'s reference-directed short read assembly suite `maq`. The module won't work unless you download and install the `maq` software; see [the Sourceforge site](http://maq.sourceforge.net).

There are two modes of action.

## Assembly

The first is a simple pipeline through the `maq` commands, taking your read data in and squirting out an assembly object of type [Bio::Assembly::IO::maq](https://metacpan.org/pod/Bio::Assembly::IO::maq). The pipeline is based on the one performed by `maq.pl easyrun`:

| Action                                | `maq` Commands         |
|---------------------------------------|------------------------|
| data conversion to maq binary formats | `fasta2bfa, fastq2bfq` |
| map sequence reads to reference seq   | `map`                  |
| assemble, creating consensus          | `assemble`             |
| convert map & cns files to plaintext  | `mapview, cns2fq`      |
Table 1. `maq` commands

Command-line options can be directed to the `map`, `assemble`, and `cns2fq` steps. See Specifying Options.

## Running `maq` components

The second mode is direct access to `maq` commands. To run a command, construct a run factory, specifying the desired command using the `-command` argument in the factory constructor, along with options specific to that command (see [Specifying Options](#specifying-options)):

```perl
$maqfac->Bio::Tools::Run::Maq->new( -command => 'fasta2bfa' );
```

To execute, use the `run_maq` methods. Input and output files are specified in the arguments of `run_maq` (see [Specifying Files](#specifying-files)):

```perl
$maqfac->run_maq( -fas => "myref.fas", -bfa => "myref.bfa" );
```

### Specifying options

`maq` is complex, with many subprograms (commands) and command-line options and file specs for each. This module attempts to provide commands and options comprehensively. You can browse the choices like so:

```perl
$maqfac = Bio::Tools::Run::Maq->new( -command => 'assemble' );
# all maq commands
@all_commands = $maqfac->available_parameters('commands');
@all_commands = $maqfac->available_commands; # alias
# just for assemble
@assemble_params = $maqfac->available_parameters('params');
@assemble_switches = $maqfac->available_parameters('switches');
@assemble_all_options = $maqfac->available_parameters();
```

Reasonably mnemonic names have been assigned to the single-letter command line options. These are the names returned by `available_parameters`, and can be used in the factory constructor like typical BioPerl named parameters.

Options can be directed to the `map`, `assemble` and `cns2fq` components of the assembly pipeline implemented by the `run()` method. Identify the desired options, for example

```perl
 @map_params = Bio::Tools::Run::Maq->new(-command => 'map' )->
        available_parameters();
 
 # returns:
 # adaptor_file
 # first_read_length
 # max_hits
 # max_mismatches
 # max_outer_distance
 # max_outer_distance_rf
 # mismatch_dump
 # mismatch_posn_dump
 # mismatch_thr
 # mutation_rate
 # second_read_length
 # unmapped_dump
```
then in the factory construction, specify the desired parameters prefixed by `map_`, `asm_`, or `c2q_`, as appropriate (note, *no* `-command` parameter):

```perl
$maqfac = Bio::Tools::Run::Maq->new( -map_max_mismatches => 1 );
$assy = $maqfac->run( "read1.fastq", "refseq.fas" );
```

See the [`maq` manpage](http://maq.sourceforge.net/maq-manpage.shtml) for many gory details.

### Specifying files

When a command requires filenames, these are provided to the `run_maq` method, not the constructor (`new()`). To see the set of files required by a command, use `available_parameters('filespec')` or the alias `filespec()`.

```perl
$maqfac = Bio::Tools::Run::Maq->new( -command => 'map' );
@filespec = $maqfac->filespec;
```

This example returns the following array:

```
map
bfa 
bfq1 
#bfq2 
2>#log
```

This indicates that map (`maq` binary mapfile), bfa (`maq` binary fasta), and bfq (`maq` binary fastq) files **must** be specified, another bfq file *may* be specified, and a log file receiving STDERR also *may* be specified. Use these in the `run_maq` call like so:

```perl
$maqfac->run_maq( -map => 'my.map', -bfa => 'myrefseq.bfa',
                  -bfq1 => 'reads1.bfq', -bfq2 => 'reads2.bfq' );
```

Here, the `-log` parameter was unspecified. Therefore, the object will store the programs STDERR output for you in the `stderr()` attribute:

```perl
handle_map_warning($maqfac) if ($maqfac->stderr =~ /warning/);
```

`STDOUT` for a run is also saved, in `stdout()`, unless a file is specified to slurp it according to the filespec. `maq` STDOUT usually contains useful information on the run.

The maq assembly object
-----------------------

`Bio::Tools::Run::Maq`, like the other assembler run modules, stores the assembly in a [Bio::Assembly::Scaffold](https://metacpan.org/pod/Bio::Assembly::Scaffold)
object by default. This object is built using [Bio::Assembly::IO::maq](https://metacpan.org/pod/Bio::Assembly::IO::maq)
, a read-only IO module that parses the `maq` consensus file (`.cns.fastq`) and map file (`.maq`) produced by the execution of `Bio::Tools::Run::Maq::run()`. In the code:

```perl
$fac = Bio::Tools::Run::Maq->new();
$assy = $fac->run('reads.faq', 'refseq.fas');
```

`$assy` is the `Scaffold` object. This code, if successful, will have produced two temporary files in the execution directory, with `.maq` and `.cns.fastq` extensions.

To specify a name for the assembly output files, do

```perl
$fac = Bio::Tools::Run::Maq->new();
$fac->out_type('myassy.maq');
$assy = $fac->run('reads.faq', 'refseq.fas');
```

The `maq` map file (converted by `maq mapview`) will be called `myassy.maq` in this example; the consensus file will be `myassy.cns.fastq`. Both files are required to read in an assembly using [Bio::Assembly::IO](https://metacpan.org/pod/Bio::Assembly::IO), but only the `.maq` is specified in the parameters:

```perl
$assy = Bio::Assembly::IO->new( -file => 'myassy.maq' );
```

### maq assembly contigs

Since the reference sequence is integrated into the `maq` assembly, `maq` always creates a single "contig" that includes all reads that match the reference sequence. [Bio::Assembly::IO::maq](https://metacpan.org/pod/Bio::Assembly::IO::maq)
divides the assembly into separate contigs by examining the consensus quality, and returning contigs that represent contiguous regions of non-zero PHRED quality values. Singlets are contigs containing only one read; as required by `Bio::Assembly::Scaffold`, these are treated specially and can be accessed with the `singlet`-associated iterators.

### maq contig features

Standard contig features and attributes are accessible with standard accessors:

```perl
$contig = $assy->next_contig;
$consensus = $contig->get_consensus_sequence;
$cons_quality = $contig->get_consensus_quality;
@cons_ids = $contig->get_seq_ids;
```

`maq`-specific features, present in the `.maq` file, are accessible as follows.

The sequence IDs within the contigs are obtained as :

```perl
@contig_seqids = $contig->get_seq_ids;
```

To get the individual `maq` features, you need to drill down:

```perl
$seq = $contig->get_seq_by_name( ($contig->get_seq_ids)[0] );
$feat = $contig->get_seq_feat_by_tag($seq, "_aligned_coord:".$seq->id);
$maq_feat = ($feat->sub_SeqFeature)[0];
print join(" ", $maq_feat->get_all_tags);
```

The tags are:

```
alt_map_qual
chr
insert_size
map_qual
num_mm_best_hit
one_mm_hits
paired_flag
posn
qualstr
read_len
read_name
se_map_qual
sum_qual_mm_best_hit
zero_mm_hits
```

which are mnemonics for the contents of each `maq mapview` line; see [the `maq` manpage](http://maq.sourceforge.net/maq-manpage.shtml) under 'mapview' for details.

SEE ALSO
--------

[Short-read assemblies with `bwa`](Short-read_assemblies_with_BWA_HOWTO.html)
