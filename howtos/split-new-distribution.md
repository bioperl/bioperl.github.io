---
title: "Splitting a subset of modules into a new distribution"
layout: howto
---

The objective is to create smaller module distribution out of a subset
of bioperl-live modules, together with its related tests and scripts,
as well as the development history of those files.  For this purpose,
we start from a bioperl-live repository, and filter out its history of
unwanted files.  This allows us to keep the development history of
those modules.

The plan goes like this:

1. select files that will go into the new distribution
2. filter out all the unwanted files from bioperl-live repository
3. restructure distribution for distzilla release
4. make a synced release of both bioperl-live and the new distribution


## Selecting files to keep

**Aim**: `files-to-keep` file with one file path per line

When deciding what files should go into a module distribution, the
following steps are helpful:

1. Start from the modules that there's no doubt will be part of the
  distribution such as the main module that will give the module its
  name.

2. Check the dependencies and reverse dependencies of those modules.
  A useful command to do it is `grep -hPo 'Bio::[:a-z0-9]*'
  module-to-check | sort -u`.  Decide if they should be part of the
  new distribution.

3. Repeat step above until there's no new modules added.

Once there's a list of modules for the new distribution, find the
scripts that make use of those modules and decide if they should be
part of that distribution too.  Finally, find the tests units for
those modules as well as any data files required for those tests.

Since we are keeping the development history, one needs to select all
related files across history.  This includes files that have been
renamed as well as files that have been removed or merged together.
Searching the whole git history with the `--stat` option is helpful as
it gives hints on renames and removed files which `--follow` will
miss.

    git log --stat

It is also helpful to scan the list of all files that ever existed in
the history of a repository:

    git log --all --pretty=format: --name-only --diff-filter=A | sort -u

List the path for the selected files, relative to the root of the
repository, one per line.  Save the file as `files-to-keep`.


## Filter out unwanted history

**Aim**: git repository with the history of selected files

This step makes use of `git filter-branch` which rewrites the history
of a repository by removing unwanted files and any empty commit.  For
this purpose, a list of files to be removed is required.  This list is
generated from the list of files to be kept that was previously
generated.

Start from a new clone of bioperl-live, name it after the new
distribution, and remove the original remote to avoid accidentally
push the modified history:

    git clone https://github.com/bioperl/bioperl-live.git Bio-New-Distribution
    cd Bio-New-Distribution
    git remote remove origin

Generate the list of files to be removed using the `files-to-keep`
file previously created:

    git log --all --pretty=format: --name-only --diff-filter=A | sort -u > all-files-ever
    awk 'NR == FNR {a[$0]; next} !($0 in a)' files-to-keep all-files-ever > files-to-rm

Leave the file `files-to-rm` at the root of the repository and call
(this command will take a very long time):

    git filter-branch \
      --prune-empty \
      --index-filter 'xargs -a ../../files-to-rm git rm --cached --ignore-unmatch --quiet -- ' \
      -- --all

Note the path `../../files-to-rm`.  While `git filter-branch` is
called from the root of the repository, the `--index-filter` command
will be called from `.git-rewrite/hash/`.

A few things remain to be done.  First, we need to remove the empty
merge commits that were left behind:

    git filter-branch \
      --force \
      --prune-empty \
      --parent-filter 'sed "s/-p //g" | xargs -r git show-branch --independent | sed "s/\</-p /g"' \
      -- --all

Second, the old tags don't make any sense, they point to objects that
no longer exist and releases that were not done from this new
repository.  Remove them all:

    git tag | xargs git tag -d

Finally, clean up the repository from references to old objects:

    git gc --aggressive --prune=all

The only unwanted commit left behind will be a root commit.  However,
removing the root commit requires a rebase which changes the committer
information (see author and committer of each commit with `git log
--format=raw`).

### Merging with another repository

The files to be part of the new distribution may already be in a
separate repositories.  For example, bioperl-live and bioperl-run each
has half of the original bioperl repository.  If the new distribution
takes files from these two repositories, one needs to merge the two
histories together.

First filter out of the history of both repositories, so you have two
directories, for example `clean-live` and `clean-run`.  The two
repositories may have different organisation, for example,
bioperl-live has `Bio` at the root of the repository while bioperl-run
has it in the `lib` directory.  It is easier to restructure each of
them in preparation for the merge.  See the section about
restructuring the files for instructions.

    cd clean-live
    git mv ..
    git commmit -m "maint: restructure for merge with ..."

    cd clean-run
    git mv ..
    git commmit -m "maint: restructure for merge with ..."

The actual merge is done like so:

    git remote add clean-run ../clean-run
    git fetch clean-run
    git merge --no-ff --no-commit clean-run/master
    git commit -m "Merge Bio-... (bioperl-live) and Bio-... (bioperl-run)"

## Restructure new distribution

**Aim**: reach a state where releases can be made with `dzil release`

Organise the repository like this:

* `inc/` - perl modules to be installed
* `t/` - test units and required data
* `bin/` - programs to be installed (remove the `.pl` file extension)
* `inc/` - modules required for build and test but not installed
* examples are documentation.  Documentation in perl are POD blocks
  within perl modules.  So move the examples into the POD of one of
  the modules.

In addition, create a new `Changes` file:

{% raw %}
    Summary of important user-visible changes for THIS-DISTRIBUTION
    ---------------------------------------------------------------

    {{$NEXT}}
      * First release after split from bioperl-live.
{% endraw %}

And an appropriate `dist.ini` file.  See
[Bio-ASN1-EntrezGene](https://github.com/bioperl/Bio-ASN1-EntrezGene/blob/master/dist.ini)
and
[Bio-EUtilities](https://github.com/bioperl/Bio-EUtilities/blob/master/dist.ini)
as examples.

To make releases easier, there is a distzilla pluginbundle for
bioperl.  It will perform a set of pre-defined tests and define
metadata in a standard form.  It also comes with a Pod::Weaver
configuration which will format the POD in a uniform manner across all
bioperl modules.  It's best to do things in separate, one commit for
each:

### Use of podweaver

The top of a module will look something like this:

    # ABSTRACT: Parses output from the PAML programs codeml, baseml, basemlg, codemlsites and yn00
    # AUTHOR: Jason Stajich <jason@bioperl.org>
    # AUTHOR: Aaron Mackey <amackey@virginia.edu>
    # OWNER: Jason Stajich <jason@bioperl.org>
    # OWNER: Aaron Mackey <amackey@virginia.edu>
    # LICENSE: Perl_5

Use this checklist:

* remove the NAME block and use it to set `ABSTRACT`
* remove the AUTHOR, CONTRIBUTORS, etc blocks and set AUTHOR and
  OWNER.  The OWNER is required and will often be the same as AUTHOR.
* the value of license needs to be a `Software::License::*` module.
  Check existing module for any specific license.
* remove the comment block at the top of each module that is now
  redundant, the one that starts with `BioPerl module for ...`
* remove the `# Let the code begin...` line
* fix email addresses.  Remove the non-sense FOO-AT-THIS-DOT-COM.
* remove the whole feedback POD block which is now generated
  automatically.
* replace the `=head2` with `=method`, `=attr`, or `=internal` as
  appropriate.
* in addition to all of the above, the POD for scripts will need a
  `PODNAME` with the script name too.

When this is done:

    git commit -m "maint: fix POD to make use of PodWeaver"

### Declare package name and required modules at the top

This stuff should be at the top of each module, before the POD blocks.

* move the `package Foo;` declaration to the very first line
* leave an empty second line.  It will be filled automatically at
  release with a `VERSION` variable.
* follow with perl pragmas such as `utf8`, `strict`, and `warnings`.
  If `utf8` is set, should be the first.
* add all other `use` statements next.  It's handy to have them at the
  top.

When it is done:

    git commmit -m "maint: move 'package' and 'use' statements to top"

### Perl best practices and bioperl tests

The bioperl dist zilla plugin will enable a bunch of new tests.  Most
of the existing bioperl code will fail and will require changes.  In
addition, there is a lot of Perl best practices that are not followed.
Some of the things to do are:

* remove trailing whitespace
* replace hard tabs with normal spaces
* `use warnings`
* `use utf8`.  This should be the third line of a module.  The first
  is the package declaration, the second is empty for dzil's
  `[PkgVersion]`, the third declares file encoding.
* `use parent` instead of `use base` and manually modigying `@ISA`
* `our` instead of `use vars`
* set all shebang lines in the scripts to `#!/usr/bin/env perl`

## New releases of both bioperl and new distribution

**Aim**: new releases

First remove the files that are part of the new distribution from
bioperl-live.  It is important for the release to happen soon after
because some people will be installing bioperl-live from the git
repository.  As such, it's important that the removed modules are
available for installation on CPAN from some distribution.

When removing files, be careful with data files for tests.  Those
files are sometimes shared between multiple tests so some may still be
required.

Set both bioperl-live and the new distribution for the same version
number.  Add a note on the bioperl distribution about the removal of
this modules.

Ask on the mailing list for a new bioperl release.  Make the new
distribution.
