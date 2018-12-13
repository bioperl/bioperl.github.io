---
title: "Submit Patch HOWTO"
layout: howto
---

Author
------

Torsten Seemann

[Victorian Bioinformatics Consortium](http://www.vicbioinformatics.com), [Monash University](http://www.monash.edu.au/), Australia.

Copyright
---------

This document is copyright Torsten Seemann, 2005. It can be copied and distributed under the terms of the [Perl Artistic License](http://www.bioperl.org/wiki/Perl_Artistic_License).


Introduction
------------

This [HOWTO](/howtos/index.html) describes the steps you should take to get your patch (enhancement or bug fix) accepted into BioPerl, from checking out the latest version to creating a diff file and submitting it to Bugzilla.

Step by Step
------------

### Get the latest version from GitHub

You should ensure you are using the latest developer version of BioPerl - this means checking out bioperl-live from GitHub. Here are [instructions on how to do this](/INSTALL.html). This is important because the change you want to make may have already been made!

### Back up the original file

Let's imagine you want to modify something in `Bio::SeqIO::fasta`. First make a backup copy:

```
 cd ~/src/bioperl/bioperl-live
 cp Bio/SeqIO/fasta.pm Bio/SeqIO/fasta.pm.orig
```

### Modify the file

Now go ahead and modify `Bio/SeqIO/fasta.pm` to make the changes you think will enhance the module or rectify a bug. Make sure you check it for syntax too.

`perl -I. -c Bio/SeqIO/fasta.pm`

### Try your script

You need to now check that you fixed the problem. At this point you will probably re-run the original script which brought the bug to your attention in the first place.

`perl -I. /path/to/my_test_script.pl`

### Write a test

Although trivial bug fixes will be accepted as-is, anything which modifies functionality or any major change will require a [test case](http://en.wikipedia.org/wiki/Test_case) for it to be accepted with any confidence. This will mean either adding extra tests to an existing test file (`t/fasta.t` in this example - make sure you back it up to `t/fasta.t.orig`), or you will need to create a new test file. I would recommend naming it `SeqIO-fasta.t` in this case to avoid future clashes. If your test needs data files, place them in `t/data/`.

`cp t/fasta.t t/fasta.t.orig`

### Run the test

You need to make sure the test is successful too.

`perl -w -I. t/fasta.t`

The -w option ensures that all the warnings are reported just like they will be when the perl test harness runs all BioPerl tests.

### Make the patch

You now need to produce a difference file for each modified (or new) file related to the patch. There are two ways you can accomplish this:

-   Use `git diff` from the base bioperl-live directory. This is the preferred method for multiple file changes (i.e. test files and modules).

By default `git diff` will show you any uncommitted changes since the last commit.

For single files or directories:

```
git diff Bio/SeqIO/ > patch.txt
git diff Bio/SeqIO/fasta.pm > patch.txt
```

For everything:

```
git diff > patch.txt
```


-   Use the [diff](http://en.wikipedia.org/wiki/diff) tool in Unix.

```
diff -Bub Bio/SeqIO/fasta.pm.orig Bio/SeqIO/fasta.pm > fasta-patch.txt
diff -Bub t/fasta.t.orig          t/fasta.t          > test-patch.txt
```

### Submit the patch

Log into the [Issues](https://github.com/bioperl/bioperl-live/issues) page of bioperl-live on Github. Submit a new feature request, and attach `fasta-patch.txt` and `test-patch.txt` to your bug submission. Make sure you write a clear and concise description for the feature request.

### The waiting game

Eventually your bug submission will be processed and assimilated into bioperl-live (assuming it wasn't rejected). A notification will be sent to you, and to the [bioperl-guts-l mailing list, which most BioPerl developers read.

`while true; do echo "Waiting..."; sleep 3600; done`

### Update your local Git repository

Don't forget to regularly update your Git version of BioPerl!

`git pull origin master`

Conclusion
----------

It takes a little bit of effort to submit a patch to BioPerl but you are rewarded with that warm fuzzy feeling that giving back to your community provides.

Further reading
---------------

-   [Advanced BioPerl](/howtos/Advanced_BioPerl_HOWTO.html)
-   [Using Git](/howtos/Using_Git_HOWTO.html)
