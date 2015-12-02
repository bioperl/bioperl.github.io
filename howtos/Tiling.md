---
title: "HOWTO:Tiling"
layout: default
---

Abstract
--------

A detailed description of the namespace, including how-tos for the application of to obtain global alignment statistics from BLAST-type search reports parsed by .

[Quick Link] to user tips.

__TOC__

Why Tile?
---------

Frequently, users want to use a set of high-scoring pairs (HSPs) obtained from a BLAST or other search to assess the overall level of identity, conservation, or coverage represented by matches between a subject and a query sequence. Because a set of HSPs frequently describes multiple overlapping sequence fragments, a simple summation of a statistics over the HSPs will generally be an overestimate of the true value. To obtain an accurate estimate of global hit statistics, a ''[tiling]'' of HSPs onto either the subject or the query sequence must be performed, in order to properly correct for this. ''(Aside for pedants: The use of the term tiling is very imprecise in this article, but the underlying algorithm is based on the construction of an actual tiling of certain subsets of the natural numbers.)''

BioPerl has long provided the means to estimate global statistics for a search report based on tiling ideas. The module, along with concurrent modifications to and , allows approximate tiling estimates of statistics such as number of identities, number of conserved sites, and fraction of site aligned. While these have been useful for many, the algorithm is not perfect, as the authors acknowledge (see [this post](http://lists.open-bio.org/pipermail/bioperl-l/2008-November/028584.html)), and there is a lack of trust in these estimates by core developers, to the extent that a ''de facto'' deprecation appears to be underway (see bioperl-l threads [here](http://lists.open-bio.org/pipermail/bioperl-l/2008-November/028502.html) and [here](http://lists.open-bio.org/pipermail/bioperl-l/2009-May/029959.html), for examples).

The namespace and its associated modules were written from the ground up, in an attempt to provide BioPerl with a sound "pure-BioPerl" way to calculate global search statistics, and to address actual and perceived weaknesses in the current implementation. By creating a new namespace as well as an associated abstract interface (), we're forced to consider what constitutes a tiling as an entity separate from results, hits, and HSPs. This leads to easier conceptualization of the problem and the possibilities for both users and developers. We're also forced to isolate the code involved, rather than spread out the details over multiple namespaces, as the current implementation does. This hopefully makes the maintainer's job easier. This organization also creates an easy handle for developers interested in implementing different tiling algorithms, or extending the present one, .

Key Organizing Concepts
-----------------------

`MapTiling` was designed around several concepts that drive the names of objects, methods and variables in the code, and that I will use freely in this article. These were invented to be able to talk concretely about some of the more fuzzy issues, where the difficulties and bugs tended to creep in.

''Search report'' : This is just the file read by , which can come in many flavors. The flavor is the ''algorithm'', and '''not''' the ''format'' specified to `Bio::SearchIO::new()`. ''Algorithms'' are, e.g., BLASTX, BLASTN, TBLASTX, and so on.  

<!-- -->

Sequence ''type'' : An HSP involves two sequences, aligned to each other: the ''query'' and the ''hit'' (or ''subject''). The query is often the sequence entered by the user, who is looking for matches among the subject sequences in a database. When a subject sequence matches (a portion of) the query well enough, it is reported as a hit, and the HSP describing the alignment is written to the search report. A sequence in an HSP is defined by its ''type'': ''query'' or ''hit'' (''subject'').  

<!-- -->

Sequence ''length'' : There are two lengths associated with each of the sequences in a given HSP: the ''aligned'' length, which is the length of the sequence (query or hit) actually involved in the HSP alignment, and the ''total'' length, which is the length of the entire sequence input by the user as a query, or the entire length of the hit sequence in the database. The distinction becomes important when calculating the fraction of a sequence successfully aligned over multiple HSPs in a search report.  

<!-- -->

Coordinate ''mapping'' and ''conversion'' : These are concepts to help handle what has been the hardest part to get right in tiling. When aligning amino acid queries to amino acid subjects, or nucleotide queries to nucleotide subjects, there's no problem. The difficulties crop up when nucleotides are translated into amino acids, which are then aligned to protein (amino acid) or translated nucleotide databases.  

<!-- -->

  
There is a convention in BLAST search reports; the trick is to work it out, and code within it as transparently as possible. There is one more concept to get us through, that of ''reported'' vs. ''enumerated'' coordinates. ''Coordinates'' are just the numbers that represent the position of a residue in a sequence fragment. The reported coordinate of a residue, then, is a number ''given or deduced from the coordinate or length numbers contained in a search report''. An enumerated coordinate is a number calculated by ''measuring a string of symbols in a sequence'' parsed from a search report. The convention is: when a sequence is given as translated nucleotides (query sequences in BLASTX, subject sequences in TBLASTN, and both in TBLASTX), the reported coordinates count nucleotides, but the sequence symbols represent amino acids, so that 1 enumerated residue = 3 reported residues, for the translated sequence type, in a given search report.

<!-- -->

  
The ''mapping coefficient'' or ''mapping'' of a sequence type for a given report is either 1 for raw amino acids or untranslated nucleotides, or 3 for translated nucleotides. We don't hard code 3s everywhere, however, but attempt to make the mapping idea as general as possible. Because, who knows?

<!-- -->

The ''context'' of a sequence type in an HSP : Either the plus or the minus strand of a nucleotide sequence can be involved in an alignment. When translated nucleotides are involved, then the translation frame must also be specified. Several algorithms automatically attempt to match both strands and all translations frames of query and/or subject sequences, so that search reports will contain HSPs involving different strand/frame ''contexts'' for the query and/or hit. Tiling HSPs onto a sequence type makes sense only if the HSPs share the same context for that type. `MapTiling` therefore provides methods for convenient context bookkeeping, and insists on the specification of strand/frame context when operating on search reports that require it.  

<!-- -->

The ''action'' of a method : There are several ways to assess the global "goodness" of a search: for example, the maximum number of identities over all tilings, or the average fraction identical over all HSPs in a given context. The ''action'' of a `MapTiling` stats method is just the descriptive string that selects the algorithm used to calculate the desired statistic. I used "action" in order to avoid using "method", which I like to reserve for its coding connotation.   

Tiling Your Searches with 
--------------------------

### Overview

Searches parsed with are organized as follows ''(see and [Parsing BLAST HSPs] for much more detail)'':

-   the object contains
    -   , which contain

        -   , which contain

            -   .

So the basic procedure is:

-   Parse the search report with ;
-   Obtain the result from the report;
-   Obtain the desired hit object from the result;
-   Construct the `MapTiling` object, using the desired hit object;
-   Obtain the desired statistics or contigs using `MapTiling` object methods.

For example,

```perl

# simple use of MapTiling object

use Bio::SearchIO; use Bio::Search::Tiling::MapTiling;

my $hit;

my $blio = Bio::SearchIO->new( -format => 'blast',

                              -file   => \'myblast.txt\');`

my $result = $blio->next_result;

while ($hit = $result->next_hit) {

 last if $hit->name =~ /Xenopus/;`

}

my $tiling = Bio::Search::Tiling::MapTiling->new($hit);

printf("global fraction identical against Xenopus: %.2f ",

      $tiling->frac_identical(-type => \'query\', -action => \'exact\'));`

```

### Use Cases

Add your own! (Along with solutions, that is...)

#### Finding the "Best" Tiling

Frequently users want to find and extract the "best" set of HSPs in the hit. The definition of "best" will vary with the user and the problem, so `MapTiling` tries to be flexible, providing the user with several options for calculating statistics, but no built-in function `tiling_that_the_user_ought_to_be_grateful_for()`. The user can create her own optimizers. If they are useful, please submit them as enhancements on [Redmine](http://redmine.open-bio.org), and we can collect them into their own module in the namespace for the benefit of other users.

-   ''Use case'':

A user has a TBLASTN report, where a nucleotide query is compared to a translated nucleotide database. The user wants to find the hit that produced the most identities with the query, and then obtain HSPs from that hit in the subject strand/frame context that has the highest fraction of aligned, conserved residues. These HSPS should "cover" the query, to the extent possible within the context.

```perl

use Bio::SearchIO; use Bio::Search::Tiling::MapTiling;

my $blio = Bio::SearchIO->new( -format => 'blast', ttt -file => 'myblast.tblastn' );

my $result = $blio->next_result;

my ($best_tiling, $best_context, $max_ident, $max_frac_cons) =

   get_the_best($result);`

while (my @tiled_hsps = $best_tiling->next_tiling('subject', $best_context)) {

   # do stuff with my array of tiled HSP objects`

}

sub get_the_best {

   my $result = shift;`
   my @hits = $result->hits;`

# initialize

   my $best_hit = pop @hits;`
   my $best_tiling = Bio::Search::Tiling::MapTiling->new($best_hit);`
   my $max_ident = $best_tiling->identities(\'query\',\'exact\');`
   `

# search through hits

   foreach my $hit (@hit) {`

tmy $tiling = Bio::Search::Tiling::MapTiling->new($hit); tmy $ident = $tiling->identities('query','exact'); tif ( $ident > $max_ident ) { t $max_ident = $ident; t $best_tiling = $tiling; t}

   }`
   `

# now, have the hit providing the most identities against the query
  search the contexts in the \*subject\* sequences

<!-- -->

# initialize

   my @contexts = $best_tiling->contexts(\'subject\');`
   my $best_context = pop @contexts;`
   my $max_frac = $best_tiling->frac_conserved(-type=>\'subject\',`

tttttt-action=>'exact', tttttt-context=>$best_context);

# search through contexts

   for my $context (@contexts) {`

tmy $frac = $best_tiling->frac_conserved(-type=>'subject', tttttt-action=>'exact', tttttt-context=>$context); tif ($frac > $max_frac) { t $max_frac = $frac; t $best_context = $context; t}

   }`

# return the objects and the values

   return ($best_tiling, $best_context, $max_ident, $max_frac);`

}

```

#### Quick and Dirty "Tiling"

In `MapTiling`, no work is done until it is requested and necessary. No tiling algorithm is executed until an action is called that needs it. So, we can create a `MapTiling` object, then perform other work on the HSPs (collect their contexts, for example, which ''is'' automatically done on construction), without triggering more complex calculations.

When one is faced with a huge number of hits, one occasionally hears someone mumble: "well, if you loop through the HSPs you could pick out the best one; should take about 20 lines of Perl". Huh? So, `MapTiling` provides the `\'fast\'` action as a convenience. This doesn't involve tiling at all (see comment above), but instead takes an average of a ''reported'' statistic (actually written to the report, not calculated), over all HSPs in a hit, weighted by the (reported) length of the HSPs. This estimate is often very good (generally within 1-5% of the 'est' action (see below), which relies on tiling), but sometimes bombs (~30% error). Decent for quick and dirty, >10000 hits, and no time to code.

The 'fast' action respects context.

-   ''Use case'':

A user has a BLASTP report that contains 5000 hits against the query, with each hit containing between 1 and 1000 HSPs. The user really just wants to see if his favorite organism came out on top.

```perl

use Bio::SearchIO; use Bio::Search::Tiling::MapTiling;

my $blio = Bio::SearchIO->new( -format => 'blast', ttt -file => 'myhugeblast.blastp' );

my $result = $blio->next_result;

# play Tetris on your phone here....then

my $top_name, $top_value = 0; while (my $hit = $result->next_hit) {

   $tiling = Bio::Search::Tiling::MapTiling->new($hit);`
   if (my $id = $tiling->identical(\'subject\',\'fast\') > $top_value) {`

t$top_name = $hit->name; t$top_value = $id;

   }`

}

print "Best hit (Q&D): ${$top_name}, with est identities ${top_value} ";

```

#### Creating BioPerl Alignments from a Tiled Hit

-   ''Use case:''

A user has BLASTed a set of contigs against a reference sequence, and would like to obtain an alignment constructed from a tiling of those contigs. The user really wants a concatenated sequence of the portions of the contigs that mapped to the reference.

```perl

use Bio::SearchIO; use Bio::Search::Tiling::MapTiling

# Note that to get one hit, the user first blasts
  the set of contigs against single sequence, the reference sequence.
  The result of this BLAST run is in 'contig_tile.bls'

$blio = Bio::SearchIO->new( -file => 'contig_tile.bls'); $result = $blio->next_result; $hit = $result->next_hit; $tiling = Bio::Search::Tiling::MapTiling->new($hit); @alns = $tiling->get_tiled_alns('query');

# here's the concatenation:

$concat_seq_obj = $alns\[0\]->get_seq_by_id('query');

```

The experimental method `get_tiled_alns()` uses a tiling to concatenate tiled HSPs into a series of objects. Each alignment contains two sequences with ids 'query' and 'subject', and consists of a concatenation of tiling HSPs which overlap or are directly adjacent. The alignment are returned in `$type` sequence order. When HSPs overlap, the alignment sequence is taken from the HSP which comes first in the [coverage map] array.

The sequences in each alignment contain features (even though they are objects) which map the original query/subject coordinates to the new alignment sequence coordinates. You can determine the original BLAST fragments this way:

```perl

$aln = ($tiling->get_tiled_alns)\[0\]; $qseq = $aln->get_seq_by_id('query'); $hseq = $aln->get_seq_by_id('subject'); foreach my $feat ($qseq->get_SeqFeatures) {

  $org_start = ($feat->get_tag_values(\'query_start\'))[0];`
  $org_end = ($feat->get_tag_values(\'query_end\'))[0];`
  # original fragment as represented in the tiled alignment:`
  $org_fragment = $feat->seq;`

} foreach my $feat ($hseq->get_SeqFeatures) {

  $org_start = ($feat->get_tag_values(\'subject_start\'))[0];`
  $org_end = ($feat->get_tag_values(\'subject_end\'))[0];`
  # original fragment as represented in the tiled alignment:`
  $org_fragment = $feat->seq;`

}

```

Read more about features at [the HOWTO].

### Statistics Method "Actions"

The global statistics are calculated by summing quantities over the disjoint component intervals of the tiling, taking into account coverage of those intervals by multiple HSPs. The ''action'' parameter is a descriptive string used to select a particular algorithm for calculating the desired statistic. Here is a brief description of the algorithms.

-   '''''exact''''' counts characters in the appropriate segment of HSPs "homology string" (that's the one between the subject and query with the symbols)

<!-- -->

-   '''''est''''' will estimate the statistics by multiplying the fraction of the HSP overlapped by the tiling components by the BLAST-reported identities/postives (this may be convenient for BLAST summary report formats)

''Both ''exact'' and ''est'' take the average over the number of HSPs that overlap the component interval. ''est'' does not require sequence data to be present in the search report.''

-   '''''max''''' uses the exact method to calculate the statistics, but returns only the maximum identites/positives over overlapping HSPs for the component interval. No averaging is involved here.

<!-- -->

-   '''''fast''''' doesn't involve tiling at all and uses only reported values, and so does not require sequence data. It calculates an average of reported identities, conserved sites, and lengths, over unmodified hsps in the hit, weighted by the length of the hsps.

### Specifying Strand/Frame Context

In the `MapTiling` implementation, strand/frame ''contexts'' (see [key concepts] above) are properties of sequence ''types'' within HSPs, and not of HSPs themselves.

To avoid the proliferation of ` -strand => $strand, -frame => $frame ` arguments in already long argument lists, and to reduce the ''context'' to a single simple entity, `MapTiling` uses yet another ad-hoc representation of strand/frame specification. In the code and pod, this is called the ''context string''. Its syntax is as follows (with apologies to the W3C, as well as the reader):

`ContextString ::= \'all\' | (ContextStrandFrameString)`
`ContextStrandFrameString ::= (\'m\' | \'p\') (\'0\' | \'1\' | \'2\' | \'_\')`

The context `all` indicates that, for the given sequence type, all HSPs in the hit are in the same context. This is true, e.g., for both query and hit types in a BLASTP report. However, the `all` context cannot be used to indicate that for all HSPs in a given hit, a given sequence type happens be in the same ''strand/frame context''. This context must be given explicitly. However, the user can test whether a type has only one context represented with something like:

```perl

sub lone_context {

 my $tiling = shift;`
 return ($tiling->contexts($type))[0] if scalar ($tiling->contexts($type)) == 1;`
 return;`

}

```

The first character of a strand/frame context represents the strand: `m` for minus, `p` for plus. The second character represents the frame: `(0|1|2)` for frames 1, 2 or 3. An underscore for the second character indicates that only the strand spec is meaningful, as in the case of BLASTN reports.

For the user that prefers to specify `-strand` and `-frame` arguments, there is the `_context()` method, which converts strand and frame parameters specified in constructor format to context strings. Example:

```perl

$tiling->conserved('query', 'fast', $tiling->_context(-type=>'query', -strand=>-1, -frame=>-2))

# same as...

$tiling->conserved('query', 'fast', 'm2')

```

The ''type'' must be specified to `_context` due to a semi-predicate issue in the `frame()` method of `B:S:HSP::HSPI`; ''viz.'', the frame is set to 0 and not `undef` when the frame context is not meaningful for a sequence type in the algorithm. So, we need to check the algorithm and sequence type to see whether the frame character should be '_' or '(0)' (see [Encapsulating the Kludge]).

### A Note on Argument Defaults

The `MapTiling` API generally requests ''type'', ''action'', and ''context'' as arguments (see the section on [key concepts] above). Some internal methods require only ''type'' and ''context'', while the `frac` method also expects a ''denominator'' (''denom''). This is a pain, so some defaults are set up. Most of the stat methods default to

:\* ''type'' : 'query';

:\* ''action'' : 'exact' or 'est' (if the search report does not contain sequence information, e.g. BLAST `-m8`); and,

:\* ''context'' : depends on algorithm - `undef` for any translated nucleotide sequence, 'p_' for nucleotides, 'all' for others

The 'all' context won't work in translated nucleotide searches for the translated sequence type; you'll get a throw if you expect the default (as it should be), and you'll need to provide an explicit context.

Design Ideas
------------

Here are more detailed explanations and motivations behind the design decisions in `Bio::Search::Tiling` and the `MapTiling` implementation.

### The TilingI Interface

is a typical BioPerl interface module, that implements little but explains much, particularly to the developer of a new tiling module. It provides skeletal methods that are expected to be overridden by any new tiling module that intends to comply with the existing `Bio::Search::*` API. It also informs devs who wish to code for a general tiling object what methods they can count on having in such an object.

The choice of methods for the `TilingI` interface was driven by the idea that users often want a "global" value (i.e., a value representative of all "relevant" HSPs in the entire hit) for the same statistics that can be obtained for ''individual'' HSP alignments. Many of these methods hark back to the global stats methods provided objects via the tiling algorithm implemented in . The method names were chosen in part to make code conversion from hit-object-based tiling relatively easy.

Therefore, the `TilingI`-specified methods include the following:

`identities()`
`conserved()`
`length()`
`frac_identical()`
`percent_identity()`
`frac_conserved()`
`percent_conserved()`
`frac_aligned()`
`range()`

`TilingI` also expects devs to make the tilings themselves accessible to the user, via

`next_tiling()`
`rewind_tilings()`

Since the name of the algorithm is typically used to infer the nature of the sequences, the accessor

`algorithm()`

is also prescribed.

The hows of the implementation are left up to the developer. No specification is made of how to handle sequence types or contexts, or whether a tiling object should carry its own copy of the hit's HSPs, or other such details. The [pod](http://doc.bioperl.org/releases/bioperl-current/bioperl-live/Bio/Search/Tiling/TilingI.html#_pod_STATISTICS%20METHODS) provides some details about algorithms and sequence context that the developer may want to keep in mind.

### Coordinate Handling

As explained [above], the search algorithms that use translated nucleotides contain sequence types whose reported and enumerated coordinates differ. Thus the coordinate systems being used, and the lengths calculated from them, need to be tracked explicitly. In particular, for the fractional statistics (`frac_identical`, e.g), coordinate conversions must be performed so that numerators and denominators are in the same coordinate system.

#### Encapsulating the Kludge

The algorithms that cause the difficulty are currently BLASTX, TBLASTN, and TBLASTX, and the FASTA versions of these. The easiest way to detect when coordinate systems require conversion is to look at the algorithm name, and then decide whether the sequence type under consideration needs conversion for this algorithm. This can lead to code like

```perl

if ($alg =~ /^T/ && $type eq 'hit') {

  $length = $self->length/3;`

}

```

and so on. If the issue is not designed at the outset, then the code gradually becomes filled with kludges along these lines, as different search reports break the code. In `MapTiling`, the algorithm-studying kludge is put in one place, so hopefully the code will snap at a single weak point.

To do this, there is an algorithm lookup table in meant to contain specific kludgy details about the algorithm, essentially keyed by algorithm name (not precisely so, but check out the for the gory details).

In the table, a ''mapping coefficient'' (either 1 or 3) is associated with each sequence type for each algorithm. The reading of the algorithm name is localized to the tiling object constructor, which sets the `mapping()` attribute to the looked-up mapping coefficient. In the algorithmic code, only the mapping attribute is accessed, and conversions are performed with calculations involving the mapping coefficient. The cost is that when the coefficient is 1, the conversions are essentially no-ops. The benefit is that the algorithmic code doesn't care how the coefficient is calculated, or even what value it takes, as long as it comes from `mapping`. This (IMHO) reduces the maintenance burden (new algorithms are basically added as configurations in the lookup table) and makes the prospect of extending the code more palatable.

#### Doing the Conversions

Again, the (or my) goal is to make conversions between enumerated and reported coordinates happen in as few places in the code as possible. In the current incarnation of `MapTiling`, conversions happen at two points: once in the calculation of the "coverage map" (described in [The Algorithm]), and once in the single "foreign" namespace method, `Bio::Search::HSP::HSPI::matches_MT()`, defined in (see [Splitting Decisions] for a few more details). These correspond roughly to denominators (the coverage map is used to determine lengths across the tiling), and numerators ( `matches_MT()` does many of the identities/conserved sites calculations), respectively. The reader can look at the code directly for the details. In both places, as discussed above, the mapping coefficients are requested on every call and the conversion calculation (which doesn't cost much) proceeds regardless of the algorithm name.

The MapTiling Algorithm
-----------------------

Here are few not-too-gory details about the underlying algorithm, with more code snippets.

### The Strict Tiling

The guts of `MapTiling` are based on a fairly simple back-of-envelope idea. To describe it, first we back out of the sequence-related details, and just consider a set of possibly overlapping real intervals, with endpoints that are positive integers, like so:

          111111111122222222223`
`0123456789012345678901234567890`
`-------------------------------`
   [       ]`
         [     ]`
      [      ]`
                   [       ]`
                      []`

Of course, each interval represents the range of a sequence type in an HSP. Then the union of all these intervals is the minimum set of intervals that cover all the intervals; in our example, it's { \[3,15\], \[19,27\] }:

          111111111122222222223`
`0123456789012345678901234567890`
`-------------------------------`
   [           ]   [       ]`

So, this is the set of intervals we want to tile in the strict sense. We want to divide this ''minimum covering set'' into a set of disjoint (i.e., non-overlapping) intervals whose union also equals the minimum covering set. You can see there are uncountably infinitely many ways to do this.

Reduce the complexity of the problem now by considering an "interval" `[$a0, $a1]` with `$a0 <= $a1` both positive integers, to be the set of positive integers {`$a0`, `$a0 + 1`, ..., `$a1`}. The tiling of the minimum covering set in this context that is constructed by the algorithm, which I'm calling the ''disjoint decomposition'', is unique and based directly on the endpoints of the original input intervals. Here is a graphical construction of a disjoint decomposition:

          111111111122222222223`
`0123456789012345678901234567890`
`-------------------------------`
`* the input intervals...      *`
   [       ]`
   .     [ .   ]`
   .  [  . . ] .               `
   .  .  . . . .   [       ]`
   .  .  . . . .   .  []   .   `
   .  .  . . . .   .  ..   .  `
   .  .  . . . .   .  ..   .   `
   [ ][ ][ ][][]   [ ][][  ]  `
`* the decomposition...        *`

The code in identifies the minimum covering set and the disjoint decomposition, given set of intervals (which are coded as arrays of 2 element arrays `[$a0, $a1]`, with scalar integers `$a0 <= $a1`). The code uses some tricks to do this pretty fast, and to get the creation of singleton intervals right.

The disjoint decomposition is explicitly constructed such that each ''component interval'' of the decomposition is '''completely contained''' within one or more of the original input intervals. This allows us to make certain assumptions later when doing the statistic calculations in `MapTiling` (see example below).

### The "Coverage Map"

The ''coverage map'' is the useful association of the disjoint decomposition with the HSPs the input intervals represent. It is an array of the following structures:

`[ [$a0, $a1], [ $hsp_object_0, $hsp_object_1, ...] ]`

The first element is one of the ''component intervals'', and the second is an array containing all the ''HSP objects'' whose coordinate range '''for the given sequence type''' contain the component interval. So we note that coverage maps are specific for hit objects '''and''' sequence type. For reports with translated nucleotides, the coverage map is also dependent on the sequence ''context'' (see [key concepts]). The coverage map for a tiling can be accessed with the `coverage_map($type, $context)` method:

```perl

$query_map = $tiling->coverage_map('query', 'all'); $hit_map = $tiling->coverage_map('hit', 'all');

```

You can get a visual on a coverage map with `coverage_map_as_text`. For example (this is from test file `t/data/dcr1_sp.WUBLASTP`, hit `ASPTN`):

`\tIntvl`
`HSPS\t0\t1\t2\t3\t4\t5\t6\t7\t8\t9\t10\t11\t12`
`0\t*\t*\t\t\t\t\t\t\t\t\t\t\t`
`1\t\t\t\t\t\t\t\t\t\t\t\t\t*`
`2\t\t\t\t\t\t\t\t\t\t\t\t*\t`
`3\t\t*\t*\t*\t*\t\t\t\t\t\t\t\t`
`4\t\t\t\t\t\t*\t*\t*\t\t\t\t\t`
`5\t\t\t\t\t\t\t\t\t\t*\t\t\t`
`6\t\t\t\t*\t\t\t\t\t\t\t\t\t`
`7\t\t\t\t\t\t\t*\t\t\t\t\t\t`
`8\t\t\t\t\t\t\t\t\t*\t\t\t\t`
`9\t\t\t\t\t\t\t\t\t\t\t*\t\t`
`Interval legend`
`0\t[12, 510]`
`1\t[511, 517]`
`2\t[518, 573]`
`3\t[574, 589]`
`4\t[590, 592]`
`5\t[692, 728]`
`6\t[729, 807]`
`7\t[808, 898]`
`8\t[926, 962]`
`9\t[1028, 1101]`
`10\t[1241, 1275]`
`11\t[1344, 1417]`
`12\t[1503, 1834]`
`HSP legend`
`0\t[12, 517]`
`1\t[1503, 1834]`
`2\t[1344, 1417]`
`3\t[511, 592]`
`4\t[692, 898]`
`5\t[1028, 1101]`
`6\t[574, 589]`
`7\t[729, 807]`
`8\t[926, 962]`
`9\t[1241, 1275]`

From this output, we see that the disjoint decomposition is what we advertised: non-overlapping, and completely covering (if we move from saying that `[$a0, $a1]` represents an interval, to saying that it represents the set of consecutive positive integers from `$a0` to `$a1`, inclusive).

We can see, too, how this representation could help in calculating estimates for global parameters. For example, from column 3, we see that interval \[574, 589\] in the query is contained only by 2 HSPs: indices 3 and 6. To estimate, say, the number of identites for this component, we calculate like so:

```perl

my $ident = 0; for (3, 6) {

   $ident += ($tiling->hsps)[$_]->matches_MT(-type=>\'query\', `

ttttt -action=>'identities', ttttt -start=>574, ttttt -end=>589); } $ident /= 2;

```

Implementation Doodads
----------------------

### Splitting Decisions

`MapTiling` is divided into and . `MapTiling.pm` defines the tiling object and contains the method overrides for compliance, as well the "high-level internals" that do the coverage map and statistics calculations. These depend on the "low-level internals" defined in `MapTileUtils.pm`. These are the functions (not object methods) that implement the bona fide tiling on closed real intervals. The split allows the hard work to be performed without object-related overhead, and on simple data types with simple comparison operations. The conversions back into object-related structures happen in `MapTiling`.

`MapTileUtils` also assists the ["kludge encapsulation"]: it holds the algorithm lookup table and the functions that read it, as well as the foreign definition of `matches_MT`.

I chose to define a new `matches_MT()` function in the namespace, partly because it was natural to use a method off the HSP objects directly at the point in the code where its functionality was required (in `MapTiling::_calc_stats`), and partly because it mimics the `matches()` function set up in that namespace to work with and so provides some familiarity for other developers. In particular, like `matches()`, it allows the specification of a subsequence of the HSP in the arguments. However, it works under the design conventions described above for handling coordinate conversions, so it seemed more reasonable to roll my own rather than patch the existing version. The location and existence of `matches_MT` is definitely subject to change, and is provided without any warranty expressed or implied.

### Memoized Results

The major calculations are made just-in-time, and then memoized, i.e., stored in slots off the object hash with a long meaningful key. This goes for internal quantities as well as the statistics most users want. So, for example, for a given MapTiling object, a coverage map would usually be calculated only once (for the query), and at most twice (if the subject perspective is also desired), and then only when a statistic is first accessed. Afterward, the map and/or any statistic is read directly from storage.

This means the user should feel free to call the statistic methods frequently if it seems natural in the code, rather than create copies of the results only to avoid recalculation.

### Most Attribute Methods are Getters

Setting complex object attributes happens under the hood (usually in the constructor, but also just-in-time as described above) as much as possible. Most (maybe all) of the attribute methods are set up as getters only. You want to set the attribute, knock yerself out. But again, all hash keys are provided as-is with no warranty.

### The Importance of POD

Read it. Write it. Live it.

> ''Don't listen to fools; the POD rules.''

'


