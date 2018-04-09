---
title: "Local Databases HOWTO"
layout: howto
---

## Authors

Brian Osborne *briano at bioteam.net*

Peter Schattner

## Abstract

This [HOWTO](/howtos/index.html) talks about using Bioperl to create local sequence databases for fast retrieval.

## Introduction

Bioperl offers many ways to retrieve sequences from online databases like NCBI and Swissprot but there may be times when you want build local databases for fast, secure retrieval. This HOWTO discusses the different Bioperl modules you might use. Also see [OBDA Flat databases HOWTO](OBDA_Flat_databases_HOWTO.html).

## Bio::Index

The following sequence data formats are supported by [Bio::Index](http://search.cpan.org/search?query=Bio::Index):

* [Bio::Index::Swissprot](https://metacpan.org/pod/Bio::Index::Swissprot)
* [Bio::Index::SwissPfam](https://metacpan.org/pod/Bio::Index::SwissPfam)
* [Bio::Index::EMBL](https://metacpan.org/pod/Bio::Index::EMBL)
* [Bio::Index::Blast](https://metacpan.org/pod/Bio::Index::Blast)
* [Bio::Index::BlastTable](https://metacpan.org/pod/Bio::Index::BlastTable)
* [Bio::Index::Fastq](https://metacpan.org/pod/Bio::Index::Fastq)
* [Bio::Index::Qual](https://metacpan.org/pod/Bio::Index::Qual)
* [Bio::Index::Hmmer](https://metacpan.org/pod/Bio::Index::Hmmer)
* [Bio::Index::Stockholm](https://metacpan.org/pod/Bio::Index::Stockholm)
* [Bio::Index::GenBank](https://metacpan.org/pod/Bio::Index::GenBank)
* [Bio::Index::Fasta](https://metacpan.org/pod/Bio::Index::Fasta)

Once the set of sequences have been indexed using [Bio::Index](http://search.cpan.org/search?query=Bio::Index), individual sequences can be accessed using syntax very similar to that used for accessing remote databases.

For example, if one wants to set up an indexed flat-file database of fasta files one could write a script like this using [Bio::Index::Fasta](https://metacpan.org/pod/Bio::Index::Fasta) to create the index:

```perl
# Some users have reported that "use strict" is necessary.
use strict;
use Bio::Index::Fasta;

my $index_file_name = shift;

my $inx = Bio::Index::Fasta->new( -filename => $index_file_name,
                                  -write_flag => 1);

$inx->make_index(@sequence_files);
```

This script then retrieves sequences:

```perl
use strict;
use Bio::Index::Fasta;

my $index_file_name = shift;

my $inx = Bio::Index::Fasta->new($index_file_name);

for my $id (@ARGV) {
    # Returns Bio::Seq object
    my $seq = $inx->fetch($id);
    # do something with the sequence
}
```

To facilitate the creation and use of more complex or flexible indexing systems, the Bioperl distribution includes two sample scripts in the *scripts/index* directory, *bp_index.PLS* and *bp_fetch.PLS*. These scripts can be used as templates to develop customized local data-file indexing systems.

## Bio::DB::Fasta

Bioperl also supplies as a means to index and query Fasta format files. It's similar in spirit to [Bio::Index](http://search.cpan.org/search?query=Bio::Index) but has additional methods and has the ability to retrieve subsequences, great for long sequences:

```perl
use strict;
use Bio::DB::Fasta;

my $id = 'CHROMOSOME_I';
my $file = "arabidopsis.fa"

my $db = Bio::DB::Fasta->new($file);
my @ids = $db->get_all_primary_ids;

# get a sequence as string
my $seqstring = $db->seq($id);

# get a PrimarySeq obj
my $seqobj = $db->get_Seq_by_id($id);

# get the header, or description line
my $desc = $db->header($id);
my $alphabet = $db->alphabet($id);

# Get subsequences and length
my $seqstr   = $db->seq($id, 4_000_000 => 4_100_000);
my $revseq   = $db->seq($id, 4_100_000 => 4_000_000);
my $length   = $db->length($id);
```

See [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta) for more information.

## Indexing using a specific substring

Both modules also offer the user the ability to designate a specific string within the fasta header as the desired id, such as the Swissprot id ("D12567") within the header of this fasta sequence:

```
>gi|523232|emb|AAC12345|sp|D12567 titin fragment
MHRHHRTGYSAAYGPLKJHGYVHFIMCVVVSWWASDVVTYIPLLLNNSSAGWKRWWWIIFGGE
GHGHHRTYSALWWPPLKJHGSKHFILCVKVSWLAKKERTYIPKKILLMMGGWWAAWWWI
```

By default [Bio::Index](http://search.cpan.org/search?query=Bio::Index) and [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta) will use the first "word" they encounter in the fasta header as the retrieval key, in this case:

```
gi|523232|emb|AAC12345|sp|D12567
```

What would be more useful as a key would be a single id. The code below will index the input file (e.g. *test.fa*) file and create an index file called *test.fa.idx* where the keys are the Swissprot identifiers.

```perl
$ENV{BIOPERL_INDEX_TYPE} = "SDBM_File";

#look for the index in the current directory

$ENV{BIOPERL_INDEX} = ".";

my $file_name = "test.fa";
my $inx = Bio::Index::Fasta->new( -filename => $file_name . ".idx",
                                  -write_flag => 1 );

# pass a reference to the critical function to the Bio::Index object
$inx->id_parser(&get_id);

# make the index
$inx->make_index($file_name);

# here is where the retrieval key is specified
sub get_id {
    my $header = shift;
    $header =~ /^>.+sp|([A-Z]\d{5})/;
    $1;
}
```

Here is how you would retrieve the sequence, as an object:

```perl
my $seq = $inx->fetch("D12567");
print $seq->seq;
```

What if you wanted to retrieve a sequence using either a Swissprot id or a gi number and the fasta header was actually a concatenation of headers with multiple gi's and Swissprot ids?

```
>gi|523232|emb|AAC12345|sp|D12567|gi|7744242|sp|V11223 titin fragment
```

Modify the function that's passed to the id_parser() method:

```perl
sub get_id {
    my $header = shift;
    my (@sps) = $header =~ /^>.+sp|([A-Z]\d{5})/g;
    my (@gis) = $header =~ /gi|(\d+)/g;
    (@sps,@gis);
}
```

The [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta) module uses the same principle, but the syntax is slightly different, for example:

```perl
my $db = Bio::DB::Fasta->new('test.fa', -makeid => &make_my_id);
my $seqobj = $db->get_Seq_by_id($id);

sub make_my_id {
    my $header = shift;
    my (@sps) = $header =~ /^>.+sp|([A-Z]\d{5})/g;
    my (@gis) = $header =~ /gi|(\d+)/g;
    (@sps,@gis);
}
```

## Storing sequences in a relational database

The core Bioperl package does not support accessing sequences and data stored in relational databases but this capability is available in the [Bioperl-db](https://github.com/bioperl/bioperl-db) package.
