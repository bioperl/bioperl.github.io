---
title: "HOWTO:EUtilities Cookbook"
layout: default
---

The API described here for refers to the version currently found in as a separate release on CPAN. It is not compatible with the experimental API used in older BioPerl releases.

Also see the [EUtilities Web Service HOWTO].

Authors
=======

[Chris Fields]

[Brian Osborne]

Simple examples
===============

These are examples where you could use a single eUtil to get the data you want.

efetch
------

### Retrieve raw data records from GenBank, save raw data to file, then parse via Bio::SeqIO

This example uses a file intermediate between and . You could feasibly use a temp file in place of a file, or even a pipe as get_Response() also allows -compliant callbacks (method parameter '-cb') and stream size settings (parameter '-read_size_hint'). All passed args to get_Response() are merely passed onto the LWP::UserAgent::request() method. Note that piping data hasn't yet been extensively tested.

'''Note''': This retrieves the basic GenBank record for the IDs you pass in; this record could be a WGS record, a CONTIG record, etc. If you '''always''' want a full sequence record use: `-rettype => 'gbwithparts'  (instead of `-rettype => 'gb'`).

```perl

use Bio::DB::EUtilities; use Bio::SeqIO;

my $factory = Bio::DB::EUtilities->new(-eutil => 'efetch',
                                       -db      => 'protein',
                                       -rettype => 'gb',
                                       -email   => 'mymail@foo.bar',
                                       -id      => @ids);

my $file = 'myseqs.gb';

# dump <HTTP::Response> content to a file (not retained in memory)
$factory->get_Response(-file => $file);

my $seqin = Bio::SeqIO->new(-file => $file,
                            -format => 'genbank');

while (my $seq = $seqin->next_seq) {
   # do whatever....
}

```

### Get accessions (actually accession.versions) for a list of GenBank IDs (GIs)

This is the quick and easy way to grab the related accessions for a list of GIs. Note that there is no one-to-one correspondence here; for that you will need to use esummary (as indicated [here]).

```perl

use Bio::DB::EUtilities;

my @ids = qw(1621261 89318838 68536103 20807972 730439);

my $factory = Bio::DB::EUtilities->new(-eutil => 'efetch',
                                       -db      => 'protein',
                                       -id      => @ids,
                                       -email   => 'mymail@foo.bar',
                                       -rettype => 'acc');

my @accs = split(m{ },$factory->get_Response->content);

print join(',',@accs), " ";

```

### Get GIs for a list of accessions

There are two ways to go about this. The first (shown here) uses `efetch`, which is the only eUtil capable of accepting both UIDs as well as accession numbers. This returns a simple list of the UIDs for the accession indicated; the raw response data from the object is split into an array for further use. As with the GI->accession example above, the list is generally globbed together, so there is no one-to-one correspondence between the UID and accession.

The second uses esummary and is to be added!

```perl

use Bio::DB::EUtilities;

my @ids = qw(CAB02640 EAS10332 YP_250808 NP_623143 P41007);

my $factory = Bio::DB::EUtilities->new(-eutil => 'efetch',

                                       -db      => 'protein',
                                       -id      => @ids,
                                       -email   => 'mymail@foo.bar',
                                       -rettype => 'gi');

my @gis = split(m{ },$factory->get_Response->content);

print join(',',@gis), " ";

```

### Downloading a large contig

Normally using a '-rettype' set to 'gb' when retrieving any file designated as a contig will retrieve the file without a sequence, but with the CONTIG section containing a join() statement describing how the sequence is built from smaller fragments.

You should set '-rettype' to 'gbwithparts' to get the full sequence with all of its features.

```perl

use Bio::DB::EUtilities;

my $id = 27479347;

# No sequence, just CONTIG

my $factory = Bio::DB::EUtilities->new(-eutil => 'efetch',

                                       -db      => 'nucleotide',
                                       -id      => $id,
                                       -email   => 'mymail@foo.bar',
                                       -rettype => 'gb');

$factory->get_Response(-file => 'contigfile.gb');

# Get sequence and all features

$factory->set_parameters(-rettype => 'gbwithparts');

# file with sequence

$factory->get_Response(-file => 'full_contig.gb');

```

### Get the ''scientific name'' for an organism

Start with the NCBI taxon id and all the taxonomic data is available to you.

```perl

use Bio::DB::EUtilities;

my $id = 527031;

my $factory = Bio::DB::EUtilities->new(-eutil => 'esummary',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'taxonomy',
                                       -id    => $id );

my ($name) = $factory->next_DocSum->get_contents_by_name('ScientificName');

print "$name ";

```

esearch
-------

### Simple database query

Here we use the the simple query 'BRCA1 and human' to answer the question: "What protein UIDs correspond to BRCA1 in humans?" To ensure you retrieve the full list of IDs, set the '-retmax' parameter to a high value if you expect long list of returned IDs.

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                       -db     => 'protein',
                                       -term   => 'BRCA1 AND human',
                                       -email  => 'mymail@foo.bar',
                                       -retmax => 5000);

# query terms are mapped; what's the actual query?

print "Query translation: ",$factory->get_query_translation," ";

# query hits

print "Count = ",$factory->get_count," ";

# UIDs

my @ids = $factory->get_ids;

```

einfo
-----

### What databases are available for querying via eUtils?

einfo generally is used to return specific information about a database, but if no other parameters are set, einfo will return a list of databases available for querying. Note that this does not correspond to those where data can be retrieved using efetch.

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'einfo',

                                       -email => 'mymail@foo.bar',);

print "available databases:  t", join(" t",$factory->get_available_databases)," ";

```

or as a one-liner. The following:

  perl -MBio::DB::EUtilities -e "Bio::DB::EUtilities->new(-eutil => 'einfo')->print_all"

gets:

    EUtil               :einfo

    DB                  :pubmed, protein, nucleotide, nuccore, nucgss, nucest, structure, genome,
      \t\t        :biosystems, books, cancerchromosomes, cdd, gap,
    \t\t        :domains, gene, genomeprj, gensat, geo, gds,
    \t\t        :homologene, journals, mesh, ncbisearch, nlmcatalog,
    \t\t        :omia, omim, pepdome, pmc, popset, probe,
    \t\t        :proteinclusters, pcassay, pccompound, pcsubstance,
    \t\t        :seqannot, snp, sra, taxonomy, toolkit, toolkitall,
    \t\t        :unigene, unists

### What information is available for database 'x'?

You can retrieve field codes, link names, updates, terms, and other things. In this example, you can get all relevant field code data and links available. Here's PubMed:

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'einfo',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'pubmed');

# for quick simple output, use:
2.  $factory->print_all;
3.  or use snippets of the following for what you need

<!-- -->

# get database info

print "Database: ",$factory->get_database," "; print " Desc: ",$factory->get_description," "; print " Name: ",$factory->get_menu_name," "; print " Records: ",$factory->get_record_count," "; print " Updated: ",$factory->get_last_update,"  ";

# iterate through FieldInfo and LinkInfo objects to get field and link data

while (my $field = $factory->next_FieldInfo) {

    print "tField code: ",$field->get_field_code,"\

";

    print "t      name: ",$field->get_field_name,"\

";

    print "t      desc: ",$field->get_field_description,"\

";

    print "t     count: ",$field->get_term_count,"\

";

    print "tAttributes: ";
    print join(',', grep {$field->$_} qw(is_date
               is_singletoken is_hierarchy is_hidden is_numerical)),"\

 "; }

while (my $link = $factory->next_LinkInfo) {

    print "tLink name: ",$link->get_link_name,"\

";

    print "t     desc: ",$link->get_link_description,"\

";

    print "t   dbfrom: ",$link->get_dbfrom,"\

"; \# same as get_database()

    print "t     dbto: ",$link->get_dbto,"\

 "; \# database linked to }

```

egquery
-------

### How do I run a global query against all Entrez databases?

You can use egquery to do this. Note this only gives you the count (no IDs), but you could easily follow this by iterating through the specific databases with hits using esearch to retrieve relevant UIDs.

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'egquery',

                                       -email => 'mymail@foo.bar',
                                       -term  => 'BRCA1 and human');

# for quick simple output, use:
2.  $factory->print_all;
3.  or use snippets of the following for what you need

<!-- -->

# iterate through GlobalQuery objects

while (my $gq = $factory->next_GlobalQuery) {

    print "Database: ",$gq->get_database,"\

";

    print "   Count: ",$gq->get_count,"\

";

    print "  Status: ",$gq->get_status,"\

 "; }

# counts from specific databases

print "PubMed Count: ", $factory->get_count('pubmed')," "; print "Protein Count: ", $factory->get_count('protein')," ";

```

esummary
--------

### I want the document summaries for a list of IDs from database 'x'.

Document summary information can be layered; allows access to summary information via a 'nested' approach (where layering remains intact) or a 'flattened' approach (where items in a summary are flattened in a depth-first order). For most purposes a 'flattened' approach works:

```perl

use Bio::DB::EUtilities;

my @ids = qw(828392 790 470338);

my $factory = Bio::DB::EUtilities->new(-eutil => 'esummary',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'gene',
                                       -id    => @ids);

# iterate through the individual DocSum objects (one per ID)

while (my $ds = $factory->next_DocSum) {

    print "ID: ",$ds->get_id,"\

";

    # flattened mode, iterates through all Item objects
    while (my $item = $ds->next_Item('flattened'))  {
        # not all Items have content, so need to check...
        printf("%-20s:%s\

",$item->get_name,$item->get_content) if $item->get_content;

    }
    print "\

"; }

```

### Get accessions (as well as other information) for a list of GIs

As with the efetch example above, this fetches the accession (the item named 'Caption' in the output). The main advantage of this approach is it also gives one the current status of the relevant sequence record; for instance, if the record has been superseded by a more recent UID, it will be indicated here.

```perl

use Bio::DB::EUtilities;

my @ids = qw(403164 45447012 27806117);

my $factory = Bio::DB::EUtilities->new(-eutil => 'esummary',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'protein',
                                       -id    => @ids);

while (my $ds = $factory->next_DocSum) {

    print "ID: ",$ds->get_id,"\

";

    # flattened mode
    while (my $item = $ds->next_Item('flattened'))  {
        # not all Items have content, so need to check...
        printf("%-20s:%s\

",$item->get_name,$item->get_content) if $item->get_content;

    }
    print "\

"; }

```

elink
-----

### I want a list of database 'x' UIDs that are linked from a list of database 'y' UIDs.

The originating database ('-dbfrom') is 'y', and the destination database ('-db') is 'x.' There are two ways to go about this; you can retrieve all the destination database IDs lumped together (where there is no one-to-one correspondence, the default), or you can set the 'correspondence' flag to retrieve the destination IDs in a way where they correspond with the query IDs. I'll demonstrate both. By the way, don't be surprised if you don't have a linked UID for every initial query UID; UIDs (in particular sequence records) are notoriously volatile, so you may be using an older UID which no longer links to a corresponding UID in the database of interest.

'''No correspondence:''' Each print statement below will print all submitted IDs and linked-to IDs lumped together, as they were retrieved.

```perl

use Bio::DB::EUtilities;

my @ids = qw(1621261 89318838 68536103 20807972 730439);

my $factory = Bio::DB::EUtilities->new(-eutil => 'elink',

                                       -email  => 'mymail@foo.bar',
                                       -db     => 'nucleotide',
                                       -dbfrom => 'protein',
                                       -id     => @ids);

# iterate through the LinkSet objects

while (my $ds = $factory->next_LinkSet) {

    print "   Link name: ",$ds->get_link_name,"\

";

    print "Protein IDs: ",join(',',$ds->get_submitted_ids),"\

";

    print "    Nuc IDs: ",join(',',$ds->get_ids),"\

"; }

```

'''ID correspondence:''' To switch on ID correspondence, just use the 'correspondence' flag; each print statement below will only print one ID per linkset (one protein GI, one nucleotide GI).

```perl

use Bio::DB::EUtilities;

my @ids = qw(1621261 89318838 68536103 20807972 730439);

my $factory = Bio::DB::EUtilities->new(-eutil => 'elink',

                                       -email          => 'mymail@foo.bar',
                                       -db             => 'nucleotide',
                                       -dbfrom         => 'protein',
                                       -correspondence => 1,
                                       -id             => @ids);

# iterate through the LinkSet objects

while (my $ds = $factory->next_LinkSet) {

    print "   Link name: ",$ds->get_link_name,"\

";

    print "Protein IDs: ",join(',',$ds->get_submitted_ids),"\

";

    print "    Nuc IDs: ",join(',',$ds->get_ids),"\

"; }

```

### I want a list of the closest neighbor UIDs for a single UID.

Set '-dbfrom' = '-db'. This retrieves a list of neighbors based on a score (also accessible, see the example code). The 'score' depends on the database queried. The curious can check [this link](http://eutils.ncbi.nlm.nih.gov/entrez/query/static/entrezlinks.html) for an updated list of link names; the link descriptions specify how the scores are calculated. For the example, IDs returned using the link name 'protein_protein' (where 'dbfrom' = 'db' = 'protein') are based on a score calculated from precomputed BLASTP results.

'''Note :''' You can also do this for a list of IDs (as each neighbor list per ID is preserved in a LinkSet); in cases where multiple IDs are submitted you should use 'correspondence' to ensure that the returned IDs relate to each query ID. Also, each ID query may get thousands of neighboring IDs, something to remember when it comes to memory (and something I'm also actively working on).

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'elink',

                                       -email  => 'mymail@foo.bar',
                                       -db     => 'protein',
                                       -dbfrom => 'protein',
                                       -id     => 1621261);

while (my $ls = $factory->next_LinkSet) {

    print "    Link name: ",$ls->get_link_name,"\

";

    print " Protein IDs: ",join(',',$ls->get_submitted_ids),"\

";

    my @ids = $ls->get_ids;
    print "Neighbor IDs: \

";

    for my $id (@ids) {
        printf("tID:%-15d Score: %d\

",$id, $ls->get_score_by_id($id));

    }

}

```

### What LinkOut URLs are available for my list of IDs?

LinkOut URLs are provided as a service by NCBI and link to information present outside of Entrez. These links are available via elink using '-cmd' parameter settings of 'llinks', 'llinklibs', and 'prlinks'. Among the links provided in the following example are the USCS Genome Browser.

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'elink',

                                       -email  => 'mymail@foo.bar',
                                       -dbfrom => 'nucleotide',
                                       -cmd    => 'llinks',
                                       -id     => [qw(28864546 53828898 14523048
                                                      14336674 1817575)]);

while (my $ls = $factory->next_LinkSet) {

    my ($id) = $ls->get_ids; # these are evaluated per id by default
    print "ID:$id\

";

    while (my $linkout = $ls->next_UrlLink) {
        print "tProvider: ",$linkout->get_provider_name,"\

";

        print "tLink    : ",$linkout->get_url,"\

";

    }

}

```

espell
------

Note: This was added for completeness. Not sure how much use it'll get but you never know...

### Why isn't my Entrez query working?

```perl

use Bio::DB::EUtilities;

my $factory = Bio::DB::EUtilities->new(-eutil => 'espell',

                                       -email => 'mymail@foo.bar',
                                       -term  => 'brest cnacer');

print "Did you mean "",$factory->get_corrected_query, ""? ";

```

epost
-----

### How do I post a specific list of UIDs to NCBI's history server?

Use epost. This comes in handy if you have a large list of UIDs and want to retrieve the data in batches. NCBI recommends that UIDs be posted in batches of 500.

```perl

use Bio::DB::EUtilities;

my @ids = qw(1621261 89318838 68536103 20807972 730439);

my $factory = Bio::DB::EUtilities->new(-eutil => 'epost',

                                       -email          => 'mymail@foo.bar',
                                       -db             => 'protein',
                                       -id             => @ids,
                                       -keep_histories => 1);

if (my $history = $factory->next_History) {

    print "Posted successfully\

";

    print "WebEnv    : ",$history->get_webenv,"\

";

    print "Query_key : ",$history->get_query_key,"\

"; }

```

More complex examples
=====================

esearch->efetch
------------------

### How do I retrieve a long list of sequences using a query?

This example uses the esearch parameter `'-usehistory'  to store the relevant IDs on the remote server, and then fetches the sequences (in chunks of 500) and saves to a file.

A few notes:

The `get_Response()  method can take either a file name or a callback as arguments, along with an optional third argument that determines the size of the data chunk returned from the remote server (if allowed). These are then passed into `{{CPAN|LWP::UserAgent}}::response()`. Due to the API, one can only use a destructive write with the file passed in; therefore, each call to `get_Response  with a file name would effectively overwrite any prior response content (e.g. from previous loop iterations), even with a filename with a redirect such as `'>>foo.txt'  (it will just create a file with the name `'>>foo.txt'  and overwrite it in each iteration).

To get around this, one can use a callback as shown below. The callback is called with three arguments: the chunk of data returned, a reference to the object, and a reference to the object. In this case, all we really care about is the data, which is then printed to the already-open file handle.

Also, note that the server retrieval is wrapped in an `eval  block; this is to catch possible server errors and repost if necessary. As NCBI has recently allowed posting up to 3 requests per second, each request could feasibly be forked into separate processes/threads for faster data retrieval (though separate files should be used under such circumstances).

```perl

use Bio::DB::EUtilities;

# set optional history queue

my $factory = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                       -email      => 'mymail@foo.bar',
                                       -db         => 'protein',
                                       -term       => 'BRCA1 AND human',
                                       -usehistory => 'y');

my $count = $factory->get_count;

# get history from queue

my $hist = $factory->next_History || die 'No history data returned'; print "History returned ";

# note db carries over from above

$factory->set_parameters(-eutil => 'efetch',

                         -rettype => 'fasta',
                         -history => $hist);

my $retry = 0; my ($retmax, $retstart) = (500,0);

open (my $out, '>', 'seqs.fa') || die "Can't open <file:$>!";

RETRIEVE_SEQS: while ($retstart < $count) {
    $factory->set_parameters(-retmax => $retmax,

                             -retstart => $retstart);
    eval{
        $factory->get_Response(-cb => sub {my ($data) = @_; print $out $data} );
    };
    if ($@) {
        die "Server error: $@.  Try again later" if $retry == 5;
        print STDERR "Server error, redo #$retry\

";

        $retry++ && redo RETRIEVE_SEQS;
    }
    say "Retrieved $retstart";
    $retstart += $retmax;

}

close $out;

```

### How do I retrieve records for the last X days for a particular query?

Set the `-reldate  parameter to the number of days prior to today's date. As a note, for some reason NCBI has dropped the `EDAT  field for the nucleotide and protein databases (which is the default date type when using `-reldate`); in this case use the `-datetype  flag to `MDAT  (modification date) or `PDAT  (publication date, or the date added to the database). Note in the following example we don't loop through the sequence IDs; I have managed to download ~ 2000 sequences this way. However it's probably advisable to use a loop as in the above example just in case.

```perl

use Bio::DB::EUtilities;

my $eutil = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                     -email      => 'mymail@foo.bar',
                                     -db         => 'nucest',
                                     -term       => 'Drosophila[ORGN]',
                                     -reldate    => 10, # records 10 days old or newer
                                     -datetype   => 'pdat', #publication date
                                     -usehistory => 'y'
                                     );

my $ct = $eutil->get_count;

my $hist = $eutil->next_History || die "No history data returned";

$eutil->set_parameters(-eutil => 'efetch',

                       -rettype => 'fasta',
                       -history => $hist);

$eutil->get_Response(-file => 'ests.fna');

```

esummary -> efetch
---------------------

### How do I retrieve the DNA sequence using EntrezGene IDs?

EntrezGene has almost everything about a specific gene with the exception of the actual DNA sequence; this information is linked to in NCBI. One way to get the actual sequence is as follows, and uses a separate instance of Bio::DB::EUtilities to act as a 'fetcher' so the previous instance retains document summary information.

```perl

use Bio::DB::EUtilities;

# this needs to be a list of EntrezGene unique IDs

my @ids = @ARGV;

my $eutil = Bio::DB::EUtilities->new(-eutil => 'esummary',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'gene',
                                       -id    => @ids);

my $fetcher = Bio::DB::EUtilities->new(-eutil => 'efetch',

                                       -email => 'mymail@foo.bar',
                                       -db      => 'nucleotide',
                                       -rettype => 'gb');

while (my $docsum = $eutil->next_DocSum) {

    # to ensure we grab the right ChrStart information, we grab the Item above
    # it in the Item hierarchy (visible via print_all from the eutil instance)
    my ($item) = $docsum->get_Items_by_name('GenomicInfoType');
    
    my %item_data = map {$_ => 0} qw(ChrAccVer ChrStart ChrStop);
    
    while (my $sub_item = $item->next_subItem) {
        if (exists $item_data{$sub_item->get_name}) {
            $item_data{$sub_item->get_name} = $sub_item->get_content;
        }
    }
    # check to make sure everything is set
    for my $check (qw(ChrAccVer ChrStart ChrStop)) {
        die "$check not set" unless $item_data{$check};
    }
    
    my $strand = $item_data{ChrStart} > $item_data{ChrStop} ? 2 : 1;
    printf("Retrieving %s, from %d-%d, strand %d\

", $item_data{ChrAccVer},

                                                    $item_data{ChrStart},
                                                    $item_data{ChrStop},
                                                    $strand
                                                    );
    
    $fetcher->set_parameters(-id => $item_data{ChrAccVer},
                             -seq_start => $item_data{ChrStart} + 1,
                             -seq_stop  => $item_data{ChrStop} + 1,
                             -strand    => $strand);
    print $fetcher->get_Response->content;

}

```

If you are using bioperl-live (the latest core code from our [Git] repository), the above has been simplified somewhat. Since both DocSums and Items can contain Items, they both now can use the same methods to retrieve Items they contain. In the above example, we know the `GenomicInfoType  Item contains other Items of interest. Using the ItemContainerI interface, we can look up a (contained) Item's contents and name, instead of delving into the innards of the tree:

```perl

use Bio::DB::EUtilities;

# this needs to be a list of EntrezGene unique IDs

my @ids = @ARGV;

my $eutil = Bio::DB::EUtilities->new(-eutil => 'esummary',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'gene',
                                       -id    => @ids);

my $fetcher = Bio::DB::EUtilities->new(-eutil => 'efetch',

                                       -email   => 'mymail@foo.bar',
                                       -db      => 'nucleotide',
                                       -rettype => 'gb');

while (my $docsum = $eutil->next_DocSum) {

    # This version uses the updated interface in bioperl-live that will be in
    # BioPerl 1.6.1 (consider it a minor bug fix for the obfuscated version
    # above)
    my ($item) = $docsum->get_Items_by_name('GenomicInfoType');
    next unless $item;
    
    my ($acc, $start, $end) = ($item->get_contents_by_name('ChrAccVer'),
                               $item->get_contents_by_name('ChrStart'),
                               $item->get_contents_by_name('ChrStop'));

    my $strand = $start > $end ? 2 : 1;
    printf("Retrieving %s, from %d-%d, strand %d\

", $acc, $start, $end,$strand );

    $fetcher->set_parameters(-id        => $acc,
                             -seq_start => $start + 1,
                             -seq_stop  => $end + 1,
                             -strand    => $strand);
    print $fetcher->get_Response->content;

}

```

esearch->esummary
--------------------

### Get GIs (as well as other information) for a list of accessions

Retrieving GIs using accessions and maintaining one-to-one correspondence is a little tricky since efetch is the only eUtil which accepts accessions using the '-id' parameter. However, you can search for the accession using a term with esearch; the trick is you must join the accessions together in a comma-separated list (performed below using the perl built-in `join`).

The UIDs returned are (again) not in correspondence with the accessions, so you will then pass them on to esummary, which has both accessions and GIs available. Note that in this example one of the accessions is out-of-date, an advantage of this approach.

```perl

use Bio::DB::EUtilities;

my @accs = qw(CAB02640 EAS10332 YP_250808 NP_623143 P41007);

my $factory = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                       -email => 'mymail@foo.bar',
                                       -db    => 'protein',
                                       -term  => join(',',@accs) );

my @uids = $factory->get_ids;

$factory->reset_parameters(-eutil => 'esummary',

                           -db    => 'protein',
                           -id    => @uids);

while (my $ds = $factory->next_DocSum) {

    print "ID: ",$ds->get_id,"\

";

    # flattened mode
    while (my $item = $ds->next_Item('flattened'))  {
        # not all Items have content, so need to check...
        printf("%-20s:%s\

",$item->get_name,$item->get_content) if $item->get_content;

    }
    print "\

"; }

```

esearch->elink->esummary
------------------------------

### How do I find all related structures to accession x?

'''NOTE:''' This code requires a bug fix in bioperl-live that will appear in 1.6.1.

First, get the protein GI, then find the related sequences to that GI using elink. From that list find those present in the structure database using elink again, finally using esummary to print out all information. The following example does that using the NCBI history server to our advantage.

```perl

use Bio::DB::EUtilities;

# get the GI

my $factory = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                       -email      => 'mymail@foo.bar',
                                       -term       => 'BAA20519',
                                       -db         => 'protein',
                                       -usehistory => 'y');

my $hist1 = $factory->next_History || die 'esearch failed';

# get neighbor proteins (note db=dbfrom, using neighbor_history)

$factory->reset_parameters(-eutil => 'elink',

                           -history => $hist1,
                           -db      => 'protein',
                           -dbfrom  => 'protein',
                           -cmd     => 'neighbor_history');

my $hist2 = $factory->next_History || die 'elink1 failed';

# get structural neighbors for the protein GIs on the server

$factory->reset_parameters(-eutil => 'elink',

                           -history => $hist2,
                           -db      => 'structure',
                           -dbfrom  => 'protein',
                           -cmd     => 'neighbor_history');

my $hist3 = $factory->next_History || die 'elink2 failed';

# get docsums for the structure IDs on the server

$factory->reset_parameters(-eutil => 'esummary',

                           -history => $hist3,
                           -db      => 'structure');

for my $ds ( $factory->get_DocSums) {

    print "ID: ",$ds->get_id,"\

";

    while (my $item = $ds->next_Item('flattened'))  {
        printf("%-20s:%s\

",$item->get_name,$item->get_content) if $item->get_content;

    }
    print "\

"; }

```

### How do I find all active compounds/substances for a particular bioassay?

'''NOTE:''' This code requires a bug fix in bioperl-live that will appear in 1.6.1.

This originally appeared as a [post](http://bioperl.org/pipermail/bioperl-l/2009-July/030558.html) from the bioperl [mail list].

There are several approaches to answering the above question, all based on whether or not you know the UID for the specific bioassay. The below script is a basic skeleton of what one can do if the UID is unknown but you know the name, thus using it as a search term. This approach uses esearch to find the BioAssay UIDs, elink to find all active compounds using the specific linkname `pcassay_pccompound_ active`, then dumps out esummary information via `print_all  (to get active substances, use the linkname `pcassay_pcsubstance_ active  instead). The various summary information can be munged using the key names via the DocSum interface methods (see the code examples above for ways to do this).

```perl

use Bio::DB::EUtilities;

my $term = '"Luciferase Profiling Assay"';

my $factory = Bio::DB::EUtilities->new(-eutil => 'esearch',

                                       -email   => 'mymail@foo.bar',
                                       -db      => 'pcassay',
                                       -term    => $term,
                                       -retmax  => 100);

my @ids = $factory->get_ids;

$factory->reset_parameters(-eutil => 'elink',

                           -db          => 'pccompound',
                           -dbfrom      => 'pcassay',
                           -linkname    => 'pcassay_pccompound_active',
                           -cmd         => 'neighbor_history',
                           -id          => @ids);

my $hist = $factory->next_History || die "Arghh!";

# you may want to iterate through chunks of summary info using retstart/retmax

$factory->reset_parameters(-eutil => 'esummary',

                           -db          => 'pccompound',
                           -history     => $hist); 

$factory->print_all;

```

elink->efetch and elink->esummary
---------------------------------------

### How do I find all the SNPs in a particular gene?

This originally appeared as a [post](http://bioperl.org/pipermail/bioperl-l/2010-June/033434.html) from the bioperl [mail list].

```perl

use Bio::DB::EUtilities;

my $id = '224809339';

my $eutil = Bio::DB::EUtilities->new(-eutil => 'elink',

                                    -id      => $id,
                                    -email   => 'setyourown@foo.bar',
                                    -verbose => 1,
                                    -dbfrom  => 'nuccore',
                                    -db      => 'snp',
                                    -cmd     => 'neighbor_history');

my $hist = $eutil->next_History || die "No history data returned";

$eutil->set_parameters(-eutil => 'efetch',

                      -history => $hist,
                      -retmode => 'text',
                      # 'chr', 'flt', 'brief', 'rsr', 'docset'
                      -rettype => 'chr');

$eutil->get_Response(-file => 'snps.txt');

# or ...

$eutil->set_parameters(-eutil => 'esummary',

                       -history => $hist);

$eutil->print_all;

```

References / See Also
=====================

-   [HOWTO:EUtilities Web Service] - more info about the SOAP interface
-   The official [NCBI EUtilities Help manual](http://www.ncbi.nlm.nih.gov/books/NBK25501/)'

 <Category:Fetching/Scrapbook>
