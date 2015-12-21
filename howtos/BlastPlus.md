---
title: "BlastPlus HOWTO"
layout: howto
---

Abstract
--------

Using BioPerl to create, manage, and query [BLAST](http://en.wikipedia.org/wiki/BLAST) databases using the [NCBI's](http://www.ncbi.nlm.nih.gov/) `blast+` suite.

Quicklink to [Synopsis](#Synopsis).

Author
------

Mark A. Jensen

[Fortinbras Research](http://fortinbras.us)

`maj -at- fortinbras -dot- us`

Introduction
------------

[blast+](http://ftp.ncbi.nlm.nih.gov/blast/executables/blast+/LATEST/) is a suite of programs from [NCBI](http://www.ncbi.nlm.nih.gov/) that creates, manipulates, manages and queries [BLAST](http://en.wikipedia.org/wiki/BLAST) sequence databases. The package attempts to integrate the many `blast+` programs into a wrapper providing a unified programmatic interface to these programs, with BioPerl objects as input and output if desired.

Dependencies and Installation
-----------------------------

The module and dependencies are available in the [bioperl/bioperl-run git](https://github.com/bioperl/bioperl-run) repository. To download and install BioPerl core and packages from github see [INSTALL](/INSTALL.html).

Like all run wrappers, these modules need the underlying programs to work. Get `blast+ 2.2.22` at the [NCBI FTP site](http://ftp.ncbi.nlm.nih.gov/blast/executables/blast+/LATEST/). The user [manual](http://www.ncbi.nlm.nih.gov/books/NBK279690/) is very helpful.

Overview
--------

The [Bio::Tools::Run::StandAloneBlastPlus](https://metacpan.org/pod/Bio::Tools::Run::StandAloneBlastPlus) object is a "factory" or harness that directs the execution of the various `blast+` programs. The basic mantra is to (1) create a `StandAloneBlastPlus` factory using the `new()` constructor, and (2) perform BLAST analyses by calling the desired BLAST program by name off the factory object. The database can be pre-existing, or can be created directly using a [FASTA](http://en.wikipedia.org/wiki/FASTA) file or a BioPerl sequence collection object. Low-complexity or other masking can also be applied as the database is constructed.

The BLAST database itself and any masking data are attached to the factory object ([step 1](#Database_construction)). Query sequences and any parameters associated with particular programs are provided to the blast method call ([step 2](#Blast_method_execution)), and are run against the attached database. (We present step 2 first, since it's what people will do many times after creating their database once.)

`blast+` also provides facilities for blasting sequences against NCBI databases over the network. See [Remote BLAST](#Remote_BLAST) for details.

BLAST method execution<a name="Blast_method_execution"></a>
----------------------

Given a `StandAloneBlastPlus` factory, such as

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
  -db_name => 'testdb'
);
```

(see [factory construction](#Factory_construction_and_initialization) below) you can run the desired BLAST method directly from the factory object, against the database currently attached to the factory (in the above example, `testdb`). `-query` is a required argument:

```perl
$result = $fac->blastn( -query => 'query_seqs.fas' );
```

Here, `$result` is a [Bio::Search::Result::BlastResult](https://metacpan.org/pod/Bio::Search::Result::BlastResult) object. To obtain further results, use `next_result`:

```perl
while ($result = $fac->next_result) {
  ...
}
```

Rewind to the beginning of the results:

```perl
$fac->rewind_results;
while ($result = $fac->next_result) {
  ...
}
```

BLAST methods include `blastn, blastp, blastx, tblastn, tblastx, rpsblast, psiblast, rpstblastn`, as well as others I probably forget. For more details on parsing and processing the results, see the [SearchIO HOWTO](SearchIO.html).

Note the following details:

-   The blast output file can be named explicitly:

```perl
$result = $fac->blastn( -query => 'query_seqs.fas',
                        -outfile => 'query.bls');
```

-   The output format can be specified:

```perl
$result = $fac->blastn( -query => 'query_seqs.fas',
                        -outfile => 'query.bls',
                        -outformat => 5); # 5=XML output
```

-   Additional arguments to the method can be specified:

```perl
$result = $fac->blastn( -query => 'query_seqs.fas',
                        -outfile => 'query.bls',
                        -method_args => [ -num_alignments => 10,
                                          -evalue => 100 ]);
```

-   To get the name of the blast output file, do

```perl
$file = $fac->blast_out;
```

-   To clean up the temp files (you must do this explicitly):

```perl
$fac->cleanup;
```

### Running bl2seq

Running `bl2seq` is similar to running the methods as outlined above, but both `-query` and `-subject` parameters are required required, and the attached database is ignored. The BLAST method must be specified explicitly with the `-method` parameter:

```perl
$fac->bl2seq( -method => 'blastp',
              -query => $seq_object_1,
              -subject => $seq_object_2);
```

Other parameters ( `-method_args`, `-outfile`, and `-outformat` ) are valid.

### Return values

The return value is always a [Bio::Search::Result::BlastResult](https://metacpan.org/pod/Bio::Search::Result::BlastResult) object on success, `undef` on failure.

Database construction<a name="Database_construction"></a>
---------------------
### Factory construction and initialization<a name="Factory_construction_and_initialization"></a>

First of all, the factory needs to be told where the `blast+` programs live. The `BLASTPLUSDIR` environment variable will be checked for the default executable directory. Alternatively, the program directory can be set for individual factory instances using the `-prog_dir` constructor parameter. All the `blast+` programs must be accessible from that directory (i.e., as executable files or symlinks).

Either the database or BLAST subject data must be specified at factory construction. Databases can be pre-existing formatted BLAST dbs, or can be built directly from fasta sequence files or BioPerl sequence object collections of several kinds. The key constructor parameters are `-db_name`, `-db_data`, and `-db_dir`.

To specify a pre-existing BLAST database, use `-db_name` alone:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
     -db_name => 'mydb'
);
```

The directory can be specified along with the basename, or separately with `-db_dir`:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
    -db_name => '~/home/blast/mydb'
);

# same as

$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
   -db_name => 'mydb', -db_dir => '~/home/blast'
);
```

To create a BLAST database de novo, see [Creating a BLAST database](#Creating_a_BLAST_database).

If you wish to apply pre-existing mask data (i.e., the final ASN1 output from one of the `blast+` masker programs), to the database before querying, specify it with `-mask_file`:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
   -db_name => 'mydb', -mask_file => 'mymaskdata.asn'
);
```

### Creating a BLAST database<a name="Creating_a_BLAST_database"></a>

There are several options for creating the database de novo using attached data, both before and after factory construction. If a temporary database (one that can be deleted by the `cleanup()` method) is desired, leave out the `-db_name` parameter. If `-db_name` is specified, the database will be preserved with the basename specified.

Use `-create => 1` to create a new database (otherwise the factory will look for an existing database). Use `-overwrite => 1` to create and overwrite an existing database.

Note that the database is not created immediately on factory construction. It will be created if necessary on the first use of a factory BLAST method, or you can force database creation by executing

```perl
$fac->make_db();
```

### Specify data during construction

With a FASTA file:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => 'myseqs.fas',
 -create => 1
);

```

With another BioPerl object collection:

```perl
$alnio = Bio::AlignIO->new( -file => 'alignment.msf' );
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => $alnio,
 -create => 1
);
@seqs = $alnio->next_aln->each_seq;
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => @seqs,
 -create => 1
);
```

Other collections (e.g., [Bio::SeqIO](https://metacpan.org/pod/Bio::SeqIO)) are valid. If a certain type does not work, please open a new [issue](https://github.com/bioperl/bioperl-live/issues).

To create temporary databases, leave out the `-db_name`, e.g.

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_data => 'myseqs.fas',
 -create => 1
);
```

To get the tempfile basename, do:

```perl
$dbname = $fac->db;
```

### Specify data post-construction

After the factory has been created, you can use the explict attribute setters:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -create => 1
);

$fac->set_db_data('myseqs.fas');
$fac->make_db;
```

and so on.

### Creating and using mask data

The blast+ mask utilities `windowmasker`, `segmasker`, and `dustmasker` are available. Masking can be rolled into database creation, or can be executed later. If your mask data is already created and in ASN1 format, set the `-mask_file` attribute on construction (see [Factory construction/initialization](#Factory_construction_and_initialization)).

To create a mask from raw data or an existing database and apply the mask upon database creation, construct the factory like so:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'my_masked_db',
 -db_data => 'myseqs.fas',
 -masker => 'dustmasker',
 -mask_data => 'maskseqs.fas',
 -create => 1
);
```

The masked database will be created during `make_db()`.

The `-mask_data` parameter can be a FASTA filename or any BioPerl sequence object collection. If the datatype (`nucl` or `prot`) of the mask data is not compatible with the selected masker, an exception will be thrown with a message to that effect.

To create a mask ASN1 file that can be used in the `-mask_file` parameter separately from the attached database, use the `make_mask()` method directly:

```perl
$mask_file = $fac->make_mask(-data => 'maskseqs.fas',
                             -masker => 'dustmasker');
# segmasker can use a blastdb as input
$mask_file = $fac->make_mask(-mask_db => 'mydb',
                             -masker => 'segmasker')
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'my_masked_db',
 -db_data => 'myseqs.fas',
 -mask_file => $mask_file
 -create => 1
);
```

### Getting database information

To get a hash containing useful metadata on an existing database (obtained by running `blastdbcmd -info`), use `db_info()`:

```perl
# get info on the attached database..
$info = $fac->db_info;
# get info on another database
$info = $fac->db_info('~/home/blastdbs/another');
```

To get a particular info element for the attached database, just call the element name off the factory:

```perl
$num_seqs = $fac->db_num_sequences;
# info on all the masks applied to the db, if any:
@masking_info = @{ $fac->db_filter_algorithms };
```

### Accessing the Bio::Tools::Run::BlastPlus factory

The `blast+` programs are actually executed by a [Bio::Tools::Run::BlastPlus](https://metacpan.org/pod/Bio::Tools::Run::BlastPlus) wrapper instance. This instance is available for peeking and poking in the `StandAloneBlastPlus` `factory()` attribute. For convenience, `BlastPlus` methods can be run directly from the `StandAloneBlastPlus` object, and are delegated to the `factory()` attribute.

For example, to get the blast+ program to be executed, examine either

`$fac->factory->command`

or

`$fac->command`

Similarly, the currently set parameters for the `BlastPlus` factory are

`@parameters = $fac->get_parameters`

To get a list of what parameters you can set for the current `blast+` operation, do:

`@available = $fac->available_parameters('all');`

### Cleaning up temp files

Temporary analysis files produced under a single factory instances can be unlinked by running

`$fac->cleanup;`

Tempfiles are generally not removed unless this method is explicitly called. Note that `cleanup()` only unlinks "registered" files and databases. All temporary files are automatically registered; in particular, "anonymous" databases (such as

```perl
$fac->Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_data => 'myseqs.fas',
 -create => 1
);
```

without a `-db_name` specification) are registered for cleanup. Any file or database can be registered with an internal method:

`$fac->_register_temp_for_cleanup('testdb');`

### Remote BLAST<a name="Remote_BLAST"></a>

`StandAloneBlastPlus` can access NCBI databases remotely. Just create a factory with the desired database and `-remote => 1`:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'nr',
 -remote => 1
);
```

and call BLAST methods from the factory as usual. Note that the database info methods are unavailable for remote databases. Available remote databases are listed [here](http://www.ncbi.nlm.nih.gov/BLAST/blastcgihelp.shtml#Databases).

### Other database goodies

-   You can check whether a given basename points to a properly formatted BLAST database by doing

`$is_good = $fac->check_db('putative_db');`

-   User parameters can be passed to the underlying `blast+` programs (if you know what you're doing) with `db_make_args` and `mask_make_args`:

```perl
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'customdb',
 -db_data => 'myseqs.fas',
 -db_make_args => [ -taxid_map => 'seq_to_taxa.txt' ],
 -masker => 'windowmasker',
 -mask_data => 'myseqs.fas',
 -mask_make_args => [ -dust => 'T' ],
 -create => 1
);
```

-   You can prevent exceptions from being thrown by failed `blast+` program executions by setting `no_throw_on_crash()`. Examine the error with `stderr()`:

```perl
$fac->no_throw_on_crash(1);
$fac->make_db;
if ($fac->stderr =~ /Error:/) {
  # handle error
  ...
}
```

Synopsis<a name="Synopsis"></a>
--------

```perl
# existing blastdb:
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb'
);

# create blastdb from fasta file and attach
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => 'myseqs.fas',
 -create => 1
);

# create blastdb from BioPerl sequence collection objects
$alnio = Bio::AlignIO->new( -file => 'alignment.msf' );
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => $alnio,
 -create  => 1
);

# blast against the remote NCBI db
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'nr',
 -remote  => 1
);

@seqs = $alnio->next_aln->each_seq;
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
 -db_name => 'mydb',
 -db_data => @seqs,
 -create  => 1
);

# create database with masks
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
-db_name => 'my_masked_db',
-db_data => 'myseqs.fas',
-masker => 'dustmasker',
-mask_data => 'maskseqs.fas',
-create => 1
);

# create a mask datafile separately
$mask_file = $fac->make_mask(
 -data => 'maskseqs.fas',
 -masker => 'dustmasker'
);

# query database for metadata
$info_hash = $fac->db_info;
$num_seq = $fac->db_num_sequences;
@mask_metadata = @{ $fac->db_filter_algorithms };

# perform blast methods
$result = $fac->tblastn( -query => $seqio );

# create a factory:
$fac = Bio::Tools::Run::StandAloneBlastPlus->new(
  -db_name => 'testdb'
);

# get your results
$result = $fac->blastn(-query => 'query_seqs.fas',
                       -outfile => 'query.bls',
                       -method_args => [ -num_alignments => 10 ] );

$result = $fac->tblastx(-query => $an_alignment_object,
                        -outfile => 'query.bls',
                        -outformat => 7 );

# do a bl2seq
$fac->bl2seq(-method => 'blastp',
             -query => $seq_object_1,
             -subject => $seq_object_2 );
```
