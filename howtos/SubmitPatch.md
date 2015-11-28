---
title: "HOWTO:Local Databases"
layout: default
---

Author
------

[Torsten Seemann](Torsten_Seemann "wikilink") <[mailto:torsten.seemann-at-infotech-monash-edu-au torsten.seemann-at-infotech-monash-edu-au]>

[Victorian Bioinformatics Consortium](http://www.vicbioinformatics.com), [Monash University](http://www.monash.edu.au/), Australia.

Copyright
---------

This document is copyright Torsten Seemann, 2005. It can be copied and distributed under the terms of the [Perl Artistic License](Perl_Artistic_License "wikilink").

Revisions
---------

-   First draft - [Tseemann](User:Tseemann "wikilink") 02:50, 29 December 2005 (EST)

Introduction
------------

This HOWTO describes the steps you should take to get your patch (enhancement or bug fix) accepted into [BioPerl](BioPerl "wikilink"), from checking out the latest CVS to creating a diff file and submitting it to Bugzilla.

Step by Step
------------

### Get the latest version from Subversion

You should ensure you are using the latest developer version of BioPerl - this means checking out [bioperl-live](bioperl-live "wikilink") (or the appropriate repository) from Subversion. Here are [instructions on how to do this](Using_Subversion#Checking_out_code_from_the_repository_with_a_developer_account "wikilink"). This is important because the change you want to make may have already been made!

` mkdir -p ~/src/bioperl`
` cd ~/src/bioperl `
` svn co `[`svn://code.open-bio.org/bioperl/bioperl-live/trunk`](svn://code.open-bio.org/bioperl/bioperl-live/trunk)` bioperl-live`

### Back up the original file

Let's imagine you want to modify something in `Bio::SeqIO::fasta`. First make a backup copy:

` cd ~/src/bioperl/bioperl-live`
` cp Bio/SeqIO/fasta.pm Bio/SeqIO/fasta.pm.orig`

### Modify the file

Now go ahead and modify `Bio/SeqIO/fasta.pm` to make the changes you think will enhance the module or rectify a bug. Make sure you check it for syntax too.

` perl -I. -c Bio/SeqIO/fasta.pm`

### Try your script

You need to now check that you fixed the problem. At this point you will probably re-run the original script which brought the bug to your attention in the first place.

` perl -I. /path/to/my_test_script.pl `

### Write a test

Although trivial bug fixes will be accepted as-is, anything which modifies functionality or any major change will require a [test case](wp:Test_case "wikilink") for it to be accepted with any confidence. This will mean either adding extra tests to an existing test file (`t/fasta.t` in this example - make sure you back it up to `t/fasta.t.orig`), or you will need to create a new test file. I would recommend naming it `SeqIO-fasta.t` in this case to avoid future clashes. If your test needs data files, place them in `t/data/`.

` cp t/fasta.t t/fasta.t.orig`

### Run the test

You need to make sure the test is successful too.

` perl -w -I. t/fasta.t`

The -w option ensures that all the warnings are reported just like they will be when the perl test harness runs all BioPerl tests.

### Make the patch

You now need to produce a difference file for each modified (or new) file related to the patch. There are two ways you can accomplish this:

-   Use `svn diff` from the base bioperl-live directory. This is the preferred method for multiple file changes (i.e. test files and modules).

For single files or directories:

` svn diff Bio/SeqIO/`
` svn diff Bio/SeqIO/fasta.pm`

For everything:

` svn diff `

-   Use the [diff](wp:diff "wikilink") tool in Unix.

` diff -Bub Bio/SeqIO/fasta.pm.orig Bio/SeqIO/fasta.pm > /tmp/fasta.pm.diff`
` diff -Bub t/fasta.t.orig          t/fasta.t          > /tmp/fasta.t.diff`

### Submit the patch

First read about [Bugs](Bugs "wikilink") then log into [Redmine](http://redmine.bioperl.org/). Submit a new feature request, and attach `/tmp/fasta.pm.diff` and `/tmp/fasta.t.diff` to your bug submission. Make sure you write a clear and concise description for the feature request.

### The waiting game

Eventually your bug submission will be processed and assimilated into [bioperl-live](bioperl-live "wikilink") (assuming it wasn't rejected). A notification will be sent to you, and to the [bioperl-guts-l mailing list](Mailing_lists "wikilink") which most [BioPerl](BioPerl "wikilink") developers read.

` while true; do echo "Waiting..."; sleep 3600; done`

### Update your local Git repository

Don't forget to regularly update your Git version of [BioPerl](BioPerl "wikilink")!

` git pull origin master`

Conclusion
----------

It takes a little bit of effort to submit a patch to [BioPerl](BioPerl "wikilink") but you are rewarded with that warm fuzzy feeling that giving back to your community provides. If the patch showed BioPerl aptitude, there's a good chance that you will be invited to [become a BioPerl developer](Becoming_a_developer "wikilink") via your own [developer account](Using_CVS#Checking_out_code_from_the_repository_with_a_developer_account "wikilink").

Further reading
---------------

-   [Advanced BioPerl](Advanced_BioPerl "wikilink")
-   [Project priority list](Project_priority_list "wikilink")
-   [Orphan modules](Orphan_modules "wikilink")
-   [Becoming a developer](Becoming_a_developer "wikilink")
-   [Using Subversion](Using_Subversion "wikilink")
-   [Mailing lists](Mailing_lists "wikilink")'

<Category:HOWTOs> <Category:TODO>
