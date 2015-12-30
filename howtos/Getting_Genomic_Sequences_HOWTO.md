---
title: "Getting Genomic Sequences HOWTO"
layout: howto
---

### Authors

Brian Osborne

Chris Fields

### Copyright

This document is copyright Brian Osborne. It can be copied and distributed under the terms of the [Perl Artistic License](http://www.perl.com/pub/language/misc/Artistic.html).

### Abstract

This is a HOWTO that talks about using Bioperl and tools related to Bioperl to get genomic sequence. There are a few different approaches, one uses files that you'll download to your own computer to query locally, others use remote, programmable interfaces or [APIs](http://en.wikipedia.org/wiki/Application_programming_interface). You should also see the [EUtils Cookbook](EUtilities_Cookbook.html).

### Using local Genbank and Entrez Gene files

You can download chromosomal, nucleotide files in [FASTA format](http://www.bioperl.org/wiki/FASTA_sequence_format) from [NCBI genomes](ftp://ftp.ncbi.nih.gov/genomes/) and get gene position data from [Entrez Gene](http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?db=gene) (see [Using Bio::DB::EntrezGene to get genomic coordinates](#Using_Bio::DB::EntrezGene_to_get_genomic_coordinates)), then create indices of the fasta files using . There is an example script, *scripts/extract_genes.pl*, that shows how this could be done. The query terms are limited to Gene id's in this example since the positional data is taken from Entrez Gene's gene2accession file.

Requirement: BioPerl.

### Using the Perl API at ENSEMBL

You can connect remotely to the [ENSEMBL](http://ensembl.org) database and query it using any name or identifier that's understood by [ENSEMBL](http://ensembl.org).

Requirements: BioPerl, the Ensembl Perl API, [DBI](https://metacpan.org/pod/DBI), and [DBD::mysql](https://metacpan.org/pod/DBD::mysql). See [Ensembl API installation](http://www.ensembl.org/info/docs/api/api_installation.html), [Ensembl API docs](http://www.ensembl.org/info/docs/api/index.html) and the [Ensembl Perl API tutorials](http://www.ensembl.org/info/docs/api/core/core_tutorial.html) for information on installation and use.

Example script:

```perl
#!/usr/local/bin/perl
use strict;
use Bio::EnsEMBL::Registry;
use Getopt::Long;
use Bio::SeqIO;

# old style (deprecated) use the Bio::EnsEMBL::Registry
# use Bio::EnsEMBL::DBSQL::DBAdaptor;

# initialize some defaults
my $species = 'homo_sapiens';
my $source = 'core'; # core or vega
# allow identifier being passed as the first argument in the command line 
# or by an option -n or -gene_symbol
my $identifier = shift;
GetOptions(
          "n|gene_symbol=s" => $identifier,
          "species=s"       => $species,
          "source=s"        => $source,
         );

my $out_seq = Bio::SeqIO->new(-fg => *STDOUT,
                              -format => 'fasta',
                            );
# The current way for accessing ENSEMBL uses the registry,
# it matches your API with its corresponding ENSEMBL database version
# Also takes care of the mysql port (now is in a non standard port 5306)
my $reg = 'Bio::EnsEMBL::Registry';

$reg->load_registry_from_db(-host => 'ensembldb.ensembl.org', 
                            -user => 'anonymous');

my $gene_adaptor = $reg->get_adaptor($species, $source, 'Gene' );

for my $gene (@{$gene_adaptor->fetch_all_by_external_name($identifier)}) {
   # the seq method in gene returns the nucleotide sequence
   # [warning] in transcript and exon objects, the seq method returns a 
   # Bioperl Bio::Seq object
   print "gene sequence for " . $identifier . ":\n" . $gene->seq() . "\n";

   for my $trans (@{$gene->get_all_Transcripts}) {
       # print the spliced sequence in fasta (you can print the raw seq 
       # with $trans->seq->seq())
       print "\ttranscript " . $trans->stable_id() . ":\n";
       $out_seq->write_seq($trans->seq);
   }
}
```

You also have the option of using raw [SQL](http://en.wikipedia.org/wiki/Sql) when using the ENSEMBL API, the result is that this is a very powerful API for analyzing genomic data.

#### Notes on this example

- This bit of code has *not been extensively tested*

- The `fetch_all_by_external_name` method does not accept a namespace or database name as an argument, so it lacks some precision. Be careful that your query returns just one sequence. Alternatively use a more precise SQL statement rather than `fetch_all_by_external_name`.

- To get a listing of available databases using `mysql`:

```
       $ mysql -u anonymous -h ensembldb.ensembl.org
       Welcome to the MySQL monitor. ...

       mysql> show databases like "homo_sapiens_core%";
       mysql> show databases;
```

### Using Bio::DB::EUtilities to get genomic coordinates

It's easy to get the coordinates of a given gene using EUtilities. The following code uses EUtilities' esearch to obtain a list of ids, and esummary to obtain the data which is then used to calculate the coordinates. It also allows to selected extra flanking sequences (on both 5' and 3' ends).

```perl
use Bio::DB::EUtilities;

## extra bps to retrieve from the flanking sequences
my $bp5_extra = 0;
my $bp3_extra = 0;
## results limitations (stop if query returns more than this number of results)
my $limit = 100;

## query for the database
my $query = '"gallus gallus"[ORGANISM] AND H4-VII[GENE]';

## make the search
my $factory = Bio::DB::EUtilities->new(-eutil  => 'esearch',
                                       -db     => 'gene',
                                       -term   => $query,
                                       -tool   => 'bioperl',
                                       -retmax => $limit
                                           );
my $n_results = $factory->get_count;
my @ids       = $factory->get_ids;

my $summaries = Bio::DB::EUtilities->new(-eutil => 'esummary',
                                         -db    => 'gene',
                                         -id    => $ids);

while (my $docsum = $summaries->next_DocSum) {
  # some items in DocSum are also named ChrStart so we pick the genomic
  # information item and get the coordinates from it
  my ($genomic_info) = $docsum->get_Items_by_name('GenomicInfoType');

  # some entries may have no data on genomic coordinates. This condition 
  # filters them out

  # found no genomic coordinates data
  next if (!$genomic_info) {

  ## get coordinates of sequence
  ## get_contents_by_name always returns a list
  my ($chr_acc_ver) = $genomic_info->get_contents_by_name("ChrAccVer");
  my ($chr_start)   = $genomic_info->get_contents_by_name("ChrStart");
  my ($chr_stop)    = $genomic_info->get_contents_by_name("ChrStop");
  my $strand;

  if ($chr_start < $chr_stop) {
    $strand     = 1;
    $chr_start  = $chr_start +1 - $bp5_extra;
    $chr_stop   = $chr_stop  +1 + $bp5_extra;
  } elseif ($chr_start > $chr_stop) {
    $strand     = 2;
    $chr_start  = $chr_start +1 - (-$bp5_extra);
    $chr_stop   = $chr_stop  +1 + (-$bp5_extra);
  } else {
    ## error, found equal values for start and stop coordinates?
  }
  ## Do something with coordinates and accession version number
}
```

### Using Bio::DB::EntrezGene to get genomic coordinates<a name="Using_Bio::DB::EntrezGene_to_get_genomic_coordinates"></a>

You can get the coordinates of a given gene from Entrez Gene using the [Bio::DB::EntrezGene](https://metacpan.org/pod/Bio::DB::EntrezGene) module. This involves examining the Annotations associated with the gene (see the [Feature-Annotation HOWTO](Features_and_Annotations_HOWTO.html) for more information on Annotations) and finding the one labelled *Evidence Viewer*, the data is found in a [URL](http://en.wikipedia.org/wiki/Url). The only identifier that the NCBI Entrez Gene API can use is a `Gene id`, formerly known as a LocusLink id.

Requirement: BioPerl.

Example code:

```perl
use strict;
use Bio::DB::EntrezGene;

# use a Gene id
my $id = shift or die "Id?"; 

my $db = Bio::DB::EntrezGene->new;

my $seq = $db->get_Seq_by_id($id);

my $ac = $seq->annotation;

for my $ann ($ac->get_Annotations('dblink')) {
  if ($ann->database eq "Evidence Viewer") {
    # get the sequence identifier, the start, and the stop
    my ($contig,$from,$to) = $ann->url =~ 
      /contig=([^&]+).+from=([0-9]+)&to=([0-9]+)/;
    print "$contig\t$from\t$to\n";
  }
}
```

This data is found in a [Bio::Annotation::DBLink](https://metacpan.org/pod/Bio::Annotation::DBLink) Annotation.

Once you have the coordinates you can use them to retrieve a sub-sequence either by using a local indexed file (e.g. [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta)) or by retrieving the sequence from a remote database (e.g. [Bio::DB::GenBank](https://metacpan.org/pod/Bio::DB::GenBank), [Bio::DB::RefSeq](https://metacpan.org/pod/Bio::DB::RefSeq) and using `subseq` or `trunc` from [Bio::PrimarySeq](https://metacpan.org/pod/Bio::PrimarySeq) or [Bio::PrimarySeqI](https://metacpan.org/pod/Bio::PrimarySeqI) (the first approach will give you the best performance).

### Using Bio::DB::GenBank when you have genomic coordinates to get a Seq object

Once you have the coordinates, sequences can be easily pulled from [Genbank](https://en.wikipedia.org/wiki/GenBank), complete with Genbank's annotation.

This is a simple example that creates a Seq object in the end.

```perl
my $gb = Bio::DB::GenBank->new(-format => 'genbank',
                               -seq_start  => $chr_start,
                               -seq_stop   => $chr_stop,
                               -strand     => $strand
                               );
my $obj = $gb->get_Seq_by_acc($chr_acc_ver);
```

The following is an example of how you can pull sequence chunks from [GenBank](https://en.wikipedia.org/wiki/GenBank), complete with GenBank's annotation, using [Bio::SeqFeature::Generic](https://metacpan.org/pod/Bio::SeqFeature::Generic) objects generated by a [Bio::Tools::RNAMotif](https://metacpan.org/pod/Bio::Tools::RNAMotif) factory.

Note that you could easily replace [Bio::Tools::RNAMotif](https://metacpan.org/pod/Bio::Tools::RNAMotif) with anything that gives start, end, and strand information. While the last `for` loop just dumps sequence annotation information, it could be modified to add the sequence feature, determine the seqfeatures's genomic context to surrounding features, etc. For more information, see [Bio::DB::GenBank](https://metacpan.org/pod/Bio::DB::GenBank).

Requirement: BioPerl.

```perl
use strict;
use Bio::Tools::RNAMotif; # or anything that gives start, end, strand info
use Bio::DB::GenBank;
use Bio::SeqIO;

my $factory = Bio::Tools::RNAMotif->new(-file =>'clean_RNAMotif.txt',
                                        -motiftag => 'protein_binding',
                                        -desctag => 'pyrR_BL'
                                       );
# array of Bio::SeqFeature::Generic objects generated by 
# Bio::Tools::RNAMotif factory
my @motifs = ();
while ( my $motif = $factory->next_prediction ) {
   push @motifs, $motif;
}

# Start Bio::SeqIO factory
my $outfile = Bio::SeqIO->new(-file => '>temp.txt',
                              -format => 'genbank');

my @seqs = ();
for my $motif (@motifs) {
   my $strand = ($sf->strand == 1) ? 1 : 2;
   my $seqstart = $sf->start - 500;
   my $seqend = $sf->end + 500;    
   # Below is from Bio::DB::GenBank POD, with some modifications
   my $factory = Bio::DB::GenBank->new(
                                  -format => 'genbank',
                                  -seq_start => $seqstart, # 500 bp upstream
                                  -seq_stop => $seqend,  # 500 bp downstream
                                  -strand => $strand,  # 1=plus, 2=minus
                                 );
   my $seqin = $factory->get_Seq_by_acc($motif->seq_id);
   # store away files
   $outfile->write_seq($seqin);
   # may take lots of memory if you have many seqfeatures
   push @seqs, $seqin;
   sleep 3;  # don't irritate NCBI
}

# from HOWTO:Feature-Annotation; gives seq annotation
for my $seq (@seqs) {
  print $seq->accession_number,"\t", $seq->length,"\n";
  for my $feat_object ($seq->get_SeqFeatures) {
    next unless $feat_object->primary_tag eq "CDS";
    print "primary tag: ", $feat_object->primary_tag, "\n";
    for my $tag ($feat_object->get_all_tags) {             
      print "  tag: ", $tag, "\n";
      for my $value ($feat_object->get_tag_values($tag)) {                
        print "    value: ", $value, "\n";
      }          
    }       
  }
}
```

### Using Bio::DB::EUtilities to get raw GenBank-formatted sequence

The [EUtilities Cookbook](EUtilities_Cookbook.html) has two examples on how to retrieve the sequence for a gene region using esummary information.
