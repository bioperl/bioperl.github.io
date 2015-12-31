---
title: "EUtilities Web Service HOWTO"
layout: howto
---

Abstract
--------

Using BioPerl to make and manage [NCBI](http://www.ncbi.nlm.nih.gov/) Entrez web service queries via the [EUtilities](http://www.ncbi.nlm.nih.gov/books/NBK25501/) [SOAP](https://en.wikipedia.org/wiki/SOAP) service.

Also see the [EUtilities Cookbook](EUtilities_Cookbook_HOWTO.html).
Author
------

Mark A. Jensen

[Fortinbras Research](http://fortinbras.us)

`maj -at- fortinbras -dot- us`

Introduction
------------

The Entrez SOAP web service provided by NCBI promises to be more stable and efficient than CGI for programmatic access to NCBI databases. The publically accessible descriptions of request and response formats in the form of computable [WSDL](https://en.wikipedia.org/wiki/Web_Services_Description_Language) documents solidify and systematize the interface to the databases, making programs that depend on database access more reliable and maintainable. More complex data can be managed and computed because of the "contract" the WSDL provides with the user with respect to data organization and access protocols.

Because data access and management can be better systematized using a web service protocol like SOAP, the service can support more complex features and data organization, making it difficult for the developer initially to take advantage of the benefits of web services. [Bio::DB::SoapEUtilities](https://metacpan.org/pod/Bio::DB::SoapEUtilities)
(SoapEU) is a system of modules designed to make Entrez SOAP access easy to incorporate into scripts and Perl one-liners, yet also provide enough hooks into the low-level interfaces to make more sophisticated code possible with a minimum of extra logic.

Dependencies and Installation
-----------------------------

SoapEU is currently available in the [bioperl-run](https://github.com/bioperl/bioperl-run) repository, in the Bio::DB namespace. 

SoapEU requires CPAN modules [XML::Twig](https://metacpan.org/pod/XML::Twig) and [SOAP::Lite](https://metacpan.org/pod/SOAP::Lite), both of which are standard BioPerl core externals.

Overview
--------

SoapEU is intended to provide all the functionality currently available in (see [the EUtilities Cookbook](http://bioperl.github.io/howtos/EUtilities_Cookbook.html) for many examples and use cases). It provides an extensive and extensible backend for adapting the SOAP responses into appropriate and familiar BioPerl objects and iterators.

The mantra is

1. create a `Bio::DB::SoapEUtilities` factory object;
2. use the factory to select a utility (`esearch, efetch, einfo, elink, esummary, egquery, epost, espell`) and set its parameters;
3. run the utility;
4. iterate the result object, or access it for data.

A typical use case might be the following:

-   Search the nucleotide database for sequences relevant to HIV1, the chemokine receptor CCR5, and Brazil. Retreive those sequences as  objects.

This is accomplished like so:

```perl
use Bio::DB::SoapEUtilities;
my $fac = Bio::DB::SoapEUtilities->new(); # step 1
my $seqio = $fac->esearch(
      -db => 'nucleotide', 
      -term => 'HIV1 and CCR5 and Brazil'
   )->run(-auto_adapt => 1, -rettype => 'fasta'); # step 2, 3
 
while ( my $seq = $seqio->next_seq ) { # step 4
 # do something with $seq, a Bio::Seq object...
}
```

Usage
-----

Browse below for useful code snippets.

## Factory

To begin, make a factory:

```perl
my $fac = Bio::DB::SoapEUtilities->new();
```

From the factory, utilities are called, parameters are set, and results or adaptors are retrieved.

If you have your own copy of the wsdl, use

```perl
my $fac = Bio::DB::SoapEUtilities->new( -wsdl_file => $my_wsdl );
```

otherwise, the correct one will be obtained over the network (by [Bio::DB::ESoap](https://metacpan.org/pod/Bio::DB::ESoap) and friends).

## Utilities and parameters

To run any of the standard NCBI EUtilities (`einfo, esearch, esummary, elink, egquery, epost, espell`), call the desired utility from the factory. To use a utility, you must set its parameters and run it to get a result. [TMTOWTDI](https://en.wikipedia.org/wiki/There's_more_than_one_way_to_do_it):

```perl
# verbose
my $fetch = $fac->efetch();
$fetch->set_parameters( -db => 'gene', -id => [828392, 790]);
my $result = $fetch->run;
 
# compact
my $result = $fac->efetch(-db =>'gene',-id => [828392,790])->run;
 
# change ids
$fac->efetch->set_parameters( -id => 470338 );
$result = $fac->run;
 
# another util
$result = $fac->esearch(-db => 'protein', -term => 'BRCA and human')->run;
 
# the utilities are kept separate
%search_params = $fac->esearch->get_parameters;
%fetch_params = $fac->efetch->get_parameters;
$search_param{db}; # is 'protein'
$fetch_params{db}; # is 'gene'
```

The factory is [Bio::ParameterBaseI](https://metacpan.org/pod/Bio::ParameterBaseI)-compliant: that means you can find out what you can set with

```perl
@available_search = $fac->esearch->available_parameters;
@available_egquery = $fac->egquery->available_parameters;
```

For more information on parameters, see [1](http://www.ncbi.nlm.nih.gov/entrez/query/static/eutils_help.html).

## Results

The "intermediate" object for `SoapEU` query results is the [Bio::DB::SoapEUtilities::Result](https://metacpan.org/pod/Bio::DB::SoapEUtilities::Result). This is a BioPerly parsing of the SOAP message sent by NCBI when a query is `run()`. This can be very useful on its own, but most users will likely want to proceed directly to [Adaptors](#adaptors), which take a `Result` and turn it into more intuitive/familiar BioPerl objects. Go there if the following details are too gory.

Results can be highly- or lowly-parsed, depending on the parameters passed to the factory `run()` method. To get the raw XML message with no parsing, do

```perl
my $xml = $fac->$util->run(-raw_xml => 1); # $xml is a scalar string
```

To retrieve a [Bio::DB::EUtilities::Result](https://metacpan.org/pod/Bio::DB::EUtilities::Result) object with limited parsing, but with accessors to the [SOAP::SOM](https://metacpan.org/pod/SOAP::SOM) message (provided by [SOAP::Lite](https://metacpan.org/pod/SOAP::Lite), do

```perl
my $result = $fac->$util->run(-no_parse => 1);
my $som = $result->som;
my $method_hash = $som->method; # etc...
```

To retrieve a `Result` object with message elements parsed into accessors, including `count()` and `ids()`, run without arguments:

```perl
my $result = $fac->esearch->run()
my $count = $result->count;
my @Count = $result->Count; # counts for each member of 
                            # the translation stack
my @ids = $result->IdList_Id; # from automatic message parsing
@ids = $result->ids; # a convenient alias
```

See [Bio::DB::EUtilities::Result](https://metacpan.org/pod/Bio::DB::EUtilities::Result) for more, even gorier details.

## Adaptors

Adaptors convert EUtility `Result`s into convenient objects, via a handle that usually provides an iterator, in the spirit of [Bio::SeqIO](https://metacpan.org/pod/Bio::SeqIO). These are probably more useful than the `Result` to the typical user, and so you can retrieve them automatically by setting the `run()` parameter `-auto_adapt => 1`.

In general, retrieve an adaptor like so:

```perl
$adp = $fac->$util->run( -auto_adapt => 1 );
# iterate...
while ( my $obj = $adp->next_obj ) {
   # do stuff with $obj
}
```

The adaptor itself occasionally possesses useful methods besides the iterator. The method `next_obj` always works, but a natural alias is also always available:

```perl
$seqio = $fac->esearch->run( -auto_adapt => 1 );
while ( my $seq = $seqio->next_seq ) {
   # do stuff with $seq
}
```

In the above example, `-auto_adapt => 1` also instructs the factory to perform an `efetch` based on the ids returned by the `esearch` (if any), so that the adaptor returned iterates over [Bio::SeqI](https://metacpan.org/pod/Bio::SeqI) objects.

Here is a rundown of the different adaptor flavors:

### `efetch`, Fetch Adaptors, and BioPerl object iterators

The `FetchAdaptor` creates bona fide BioPerl objects.

Currently, there are FetchAdaptor subclasses for sequence data (both Genbank and FASTA rettypes) and taxonomy data. The choice of FetchAdaptor is based on information in the result message, and should be transparent to the user.

```perl
$seqio = $fac->efetch( -db =>'nucleotide',
                       -id => $ids,
                       -rettype => 'gb' )->run( -auto_adapt => 1 );
while (my $seq = $seqio->next_seq) {
   my $taxio = $fac->efetch( 
	-db => 'taxonomy', 
	-id => $seq->species->ncbi_taxid )->run(-auto_adapt => 1);
   my $tax = $taxio->next_species;
   unless ( $tax->TaxId == $seq->species->ncbi_taxid ) {
     print "more work for MAJ"
   }
}
```

See the pod for the FetchAdaptor subclasses (e.g., [Bio::DB::SoapEUtilities::FetchAdaptor::seq](https://metacpan.org/pod/Bio::DB::SoapEUtilities::FetchAdaptor::seq)) for more detail.

*Tip* Use the `fasta` (a.k.a. `TSeq` in the WSDL) return type to avoid long wait times. This returns seq ids, sequence, and a few metadata items. The `gb` (a.k.a. `GBSeq`) return type will fetch all [feature and annotation](http://bioperl.github.io/howtos/Features_and_Annotations_HOWTO.html) data by default.

To cut down (a little) on GenBank format parsing time, you may use the [Bio::Seq::SeqBuilder](https://metacpan.org/pod/Bio::Seq::SeqBuilder) system, as in [Bio::SeqIO](https://metacpan.org/pod/Bio::SeqIO). This is somewhat advanced, but would look like this:

```perl
use Bio::DB::SoapEUtilities;
 
my @ids = qw(1621261 89318838 68536103 20807972 730439);
my $fac = Bio::DB::SoapEUtilities->new();
my $result = $fac->efetch( -db => 'protein', 
			   -id => $ids )->run( -no_parse => 1 );
die "no result returned : ".$fac->errstr unless $result;
my $seqio = Bio::DB::SoapEUtilities::FetchAdaptor->new(-result => $result);
# clear all..
$seqio->builder->want_none();
# add back just the annotation data (references, comments, dblinks, ...)
$seqio->builder->add_wanted_slot('annotation');
# print the pubmed ids for all references...
while ( my $seq = $seqio->next_seq ) {
    print join( "\n",
		map { 
		    $_->pubmed
		} $seq->annotation->get_Annotations('reference'));
}
```

### `elink`, the Link adaptor, and the `linkset` iterator

The `LinkAdaptor` manages LinkSets. In `SoapEU`, an `elink` call *always* preserves the correspondence between submitted and retrieved ids. The mapping between these can be accessed from the adaptor object directly as `id_map()`:

```perl
my $links = $fac->elink( -db => 'protein', 
                         -dbfrom => 'nucleotide',
                         -id => $nucids )->run( -auto_adapt => 1 );
 
# maybe more than one associated id...
my @prot_0 = $links->id_map( $nucids[0] );
```

Or iterate over the linksets:

```perl
while ( my $ls = $links->next_linkset ) {
   @ids = $ls->ids;
   @submitted_ids = $ls->submitted_ids;
   # etc.
}
```

### `esummary`, the DocSum adaptor, and the `docsum` iterator

The `DocSumAdaptor` manages docsums, the `esummary` return type. The objects returned by iterating with a `DocSumAdaptor` have accessors that let you obtain field information directly. Docsums contain lots of easy-to-forget fields; use `item_names()` to remind yourself.

```perl
my $docs = $fac->esummary( -db => 'taxonomy',
                           -id => 527031 )->run(-auto_adapt=>1);
# iterate over docsums
while (my $d = $docs->next_docsum) {
   my @available_items = $d->item_names;
   # any available item can be called as an accessor
   # from the docsum object...watch your case...
   my $sci_name = $d->ScientificName;
   my $taxid = $d->TaxId;
}
```

### `egquery`, the GQuery adaptor, and the `query` iterator

The `GQueryAdaptor` manages global query items returned by calls to `egquery`, which identifies all NCBI databases containing hits for your query term. The databases actually containing hits can be retrieved directly from the adaptor with `found_in_dbs`:

```perl
my $queries = $fac->egquery( 
    -term => 'BRCA and human'
   )->run(-auto_adapt=>1);
my @dbs = $queries->found_in_dbs;
```

Retrieve the global query info returned for *any* database with `query_by_db`:

```perl
my $prot_q = $queries->query_by_db('protein');
if ($prot_q->count) {
   #do something
}
```

Or iterate as usual:

```perl
while ( my $q = $queries->next_query ) {
   if ($q->status eq 'Ok') {
     # do sth
   }
}
```

## Web environments and query keys

To make large or complex requests for data, or to share queries, it may be helpful to use the NCBI WebEnv system to manage your queries. Each EUtility accepts the following parameters:

```
-usehistory
-WebEnv
-QueryKey
```

for this purpose. These store the details of your queries serverside.

`SoapEU` attempts to make using these relatively straightforward. Use `Result` objects to obtain the correct parameters, and don't forget `-usehistory`:

```perl
my $result1 = $fac->esearch( 
    -term => 'BRCA and human', 
    -db => 'nucleotide',
    -usehistory => 1 )->run( -no_parse=>1 );
 
my $result = $fac->esearch( 
    -term => 'AND early onset', 
    -QueryKey => $result1->query_key,
    -WebEnv => $result1->webenv )->run( -no_parse => 1 );
 
my $result = $fac->esearch(
   -db => 'protein',
   -term => 'sonic', 
   -usehistory => 1 )->run( -no_parse => 1 );
 
# later (but not more than 8 hours later) that day...
 
$result = $fac->esearch(
   -WebEnv => $result->webenv,
   -QueryKey => $result->query_key,
   -RetMax => 800 # get 'em all
   )->run; # note we're parsing the result...
@all_ids = $result->ids;
```

## Error checking

Two kinds of errors can ensue on an Entrez SOAP run. One is a SOAP fault, and the other is an error sent in non-faulted SOAP message from the server. The distinction is probably systematic, and I would welcome an explanation of it. To check for result errors, try something like:

```perl
unless ( $result = $fac->$util->run ) {
   die $fac->errstr; # this will catch a SOAP fault
}
# a valid result object was returned, but it may carry an error
if ($result->count == 0) {
   warn "No hits returned";
   if ($result->ERROR) {
     warn "Entrez error : ".$result->ERROR;
   }
}
```

Design Notes
------------

The SoapEU system is designed to be as easy (few includes, available parameter facilities, reasonable defaults, intuitive aliases, built-in pipelines) or as complex (accessors for underlying low-level objects, all parameters accessible, custom hooks for builder objects, facilities for providing local copies of WSDLs) as the user requires or desires. To the extent that it does not succeed in either direction, ping [the mailing list](mailto:bioperl-l@bioperl.org).

The middleware is provided by [SOAP::Lite](https://metacpan.org/pod/SOAP::Lite) via the [Bio::DB::ESoap](https://metacpan.org/pod/Bio::DB::ESoap) package. See the POD there for details. The WSDL documents are parsed "by hand" (using [XML::Twig](https://metacpan.org/pod/XML::Twig) in [Bio::DB::ESoap::WDSL](https://metacpan.org/pod/Bio::DB::ESoap::WDSL)), to avoid adding a large number of new CPAN dependencies associated with existing packages. The WSDL module is pretty NCBI-specific, and may break if NCBI updates its specs. However, as much generality was included as possible to make it (and all the modules) shock-absorbent. The fact that the system is WSDL-based should in itself keep it pretty robust.

The adaptor system is really the heart of the (conceived) user-friendliness. It is complex under the hood in order to be transparent to the user -- to provide familiar objects and accessors in one or two lines. However, all objects can be created explicitly and tweaked as desired:

```perl
use Bio::DB::SoapEUtilities;
use Bio::DB::SoapEUtilities::Result;
use Bio::DB::SoapEUtilities::DocSumAdaptor;
 
my $fac = Bio::DB::SoapEUtilities->new( -wsdl_file => my_wsdl.xml );
my $result = $fac->esummary(
                   -db => 'gene',
                   -id => 790 )->run( -no_parse => 1);
 
my $soap_lite_message = $result->som;
 
unless ( $soap_lite_message->fault ) {
  my $docs = Bio::DB::SoapEUtilities::DocSumAdaptor->new( 
              -result => $result
             );
}
```

Synopsis
--------

```perl
use Bio::DB::SoapEUtilities;
 
# factory construction
 
my $fac = Bio::DB::SoapEUtilities->new()
 
# executing a utility call
 
#get an iteratable adaptor
my $links = $fac->elink( 
              -dbfrom => 'protein',
              -db => 'taxonomy',
              -id => $protein_ids )->run(-auto_adapt => 1);
 
# get a Bio::DB::SoapEUtilities::Result object
my $result = $fac->esearch(
              -db => 'gene',
              -term => 'sonic and human')->run;
 
# get the raw XML message
my $xml = $fac->efetch(
            -db => 'gene',
            -id => $gids )->run( -raw_xml => 1 );
 
# change parameters 
my $new_result = $fac->efetch(
                  -db => 'gene',
                  -id => $more_gids)->run;
# reset parameters
$fac->efetch->reset_parameters( -db => 'nucleotide',
                                -id => $nucid );
$result = $fac->efetch->run;
 
# parsing and iterating the results
 
$count = $result->count;
@ids = $result->ids;
 
while ( my $linkset = $links->next_link ) {
   $submitted = $linkset->submitted_id;
}
 
($taxid) = $links->id_map($submitted_prot_id);
$species_io = $fac->efetch( -db => 'taxonomy',
                            -id => $taxid )->run( -auto_adapt => 1);
$species = $species_io->next_species;
$linnaeus = $species->binomial;
```
