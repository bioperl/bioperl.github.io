---
title: "INSTALL.WIN"
layout: default
---

Installing Bioperl on Windows
=============================

## Contents
     * 1 Introduction
     * 2 Requirements
     * 3 Installation using the ActiveState Perl Package Manager
          * 3.1 GUI Installation
          * 3.2 Comand-line Installation
     * 4 Installation using CPAN or manual installation
     * 5 Bioperl
     * 6 Bioperl on Windows
     * 7 Beyond the Core
          * 7.1 Setting environment variables
          * 7.2 Installing bioperl-db
     * 8 Bioperl in Cygwin
     * 9 bioperl-db in Cygwin
     * 10 Cygwin tips
     * 11 MySQL and DBD::mysql
     * 12 Expat
     * 13 Directory for temporary files
     * 14 BLAST
     * 15 Compiling C code

Introduction
============

This installation guide was written by Barry Moore, Nathan Haigh
and other Bioperl authors based on the original work of Paul Boutros. The
guide was updated for the BioPerl wiki by Chris Fields and Nathan
Haigh.

Please report problems and/or fixes to the BioPerl mailing list.

An up-to-date version of this document can be found on the [BioPerl wiki](http://www.bioperl.org/wiki/Installing_Bioperl_on_Windows)

Requirements
============

There are a couple of ways of installing Perl on a Windows machine. One is
to get the most recent build from [Strawberry Perl](http://strawberryperl.com/), and the other is to get
it from [ActiveState](http://www.activestate.com/); both are software companies that provides free builds
of Perl for Windows users, but Strawberry Perl is recommended since is more
CPAN friendly because it includes a compiler (gcc), related tools and other
external libraries. The current (March 2014) build is 5.18.2.

**NOTE** - Only Perl **>= 5.8.8.819** is supported by the BioPerl team. Earlier
versions may work, but we do not support them. Perl 5.18 also works. One of
the reason for this requirement is that ActivePerl >= 5.8.8.819 now use Perl
Package Manager 4 (PPM4). PPM4 is now superior to earlier versions and also
includes a Graphical User Interface (GUI). In short, it's easier for us to
produce and maintain a package for installation via PPM and also easier for
you to do the install! Proceed with earlier versions at your own risk.

To install Perl on Windows:

1. Download the [Strawberry Perl MSI](http://strawberryperl.com/releases.html) or [ActivePerl MSI](http://www.activestate.com/activeperl/downloads).
2. Run the Installer (accepting all defaults is fine).

You can also build Perl yourself (which requires a C compiler) or download
one of the other binary distributions. The Perl source for building it
yourself is available from [CPAN](http://www.cpan.org/), as are a few other binary distributions
that are alternatives to ActiveState. This approach is not recommended
unless you have specific reasons for doing so and know what you're doing.
If that's the case you probably don't need to be reading this guide.

[Cygwin](http://en.wikipedia.org/wiki/Cygwin) is a [UNIX](http://en.wikipedia.org/wiki/UNIX) emulation environment for Windows and comes with its own copy of Perl.

Information on Cygwin and Bioperl is found below.

Installation using the ActiveState Perl Package Manager
=====

## GUI Installation

1) Start the Perl Package Manager GUI from the Start menu.

2) Go to Edit >> Preferences and click the Repositories tab. Add a
new repository for each of the following (note the difference based
on the perl version). NOTE - The [DB\_File](https://metacpan.org/pod/DB_File) installed with ActivePerl
5.10 and above is a stub (i.e. it does not work). The Trouchelle
database below has a working DB\_File.

*Repositories to add*

|           Name           |              Location               |
|--------------------------+-------------------------------------|
|BioPerl-Release Candidates|http://bioperl.org/DIST/RC           |
|BioPerl-Regular Releases  |http://bioperl.org/DIST              |
|Kobes                     |http://theoryx5.uwinnipeg.ca/ppms    |
|Bribes                    |http://www.Bribes.org/perl/ppm       |
|Trouchelle                |http://trouchelle.com/ppm            |
<br>
3) Select View >> All Packages.

4) In the search box type `bioperl`.

5) Right click the latest version of Bioperl available and choose
install. (Note for users of previous Bioperl releases: you should
not have to use the Bundle-BioPerl package anymore.)

5a) From bioperl 1.5.2 onward, all 'optional' pre-requisites will
be marked for installation. If you see that some of them complain
about needing a command-line installation (eg. XML::SAX::ExpatXS),
and you want those particular pre-requisites, stop now (skip step
6) and see the 'Command-line Installation' section.

6) Click the green arrow (Run marked actions) to complete the
installation.

## Comand-line Installation

Use the ActiveState `ppm-shell`:

- Open a cmd window by going to Start >> Run and typing
'cmd' and pressing return.

- Do

```
C:> ppm-shell
ppm>
```

- Make sure you have the module `PPM-Repositories`. Try
installing it:

`ppm> install PPM-Repositories`

- For BioPerl 1.6.1, we require at least the following
repositories. You may have some present already.

```
ppm> repo add http://bioperl.org/DIST
ppm> repo add uwinnipeg
ppm> repo add trouchelle
```

Because you have installed `PPM-Repositories`, PPM will know
your Perl version, and select the correct repo from the
table above.

- Install BioPerl (**not** "bioperl").

`ppm> install BioPerl`

If you are running ActiveState Perl 5.10, you may have a
glitch involving [SOAP::Lite](https://metacpan.org/pod/SOAP::Lite). Use the following workaround:

- Get the index numbers for your active repositories:

```
ppm> repo

| id | pkgs  | name                           |
|  1 | 11431 | ActiveState Package Repository |
|  2 |    14 | bioperl.org                    |
|  3 |   291 | uwinnipeg                      |
|  4 | 11755 | trouchelle                     |
```

- Execute the following commands. (The session here is
based on the above table. Substitute the correct index
numbers for your situation.)

```
rem -turn off ActiveState, trouchelle repos
ppm> repo off 1
ppm> repo off 4
rem -to get SOAP-Lite-0.69 from uwinnipeg...
ppm> install SOAP-Lite
rem -turn ActiveState, trouchelle back on...
ppm> repo on 1
ppm> repo on 4
rem -now try...
ppm> install BioPerl
```

Installation using CPAN or manual installation
====

When using ActivePerl, installation using PPM is preferred since it is
easier, but if you run into problems, or a PPM isn't available for the
latest version/package of BioPerl, or you want to choose which optional
dependencies to install, you can install manually by [downloading the appropriate package](http://bioperl.org/DIST/) or by using [CPAN](http://www.bioperl.org/wiki/CPAN) (installation using CPAN will always
get you the latest version). Both manual methods ultimately need an
accessory compiling program like MinGW, which incorporates the necessary
tools like dmake and gcc. MinGW comes by default with Strawberry Perl, but
must be installed through PPM for ActivePerl. Also CPAN neeed to be upgraded
to >= v1.81, [Module::Build](https://metacpan.org/pod/Module::Build) to be installed (>= v0.2805) and [Test::Harness](https://metacpan.org/pod/Test::Harness) to
be upgraded to >= v2.62:

### Dmake for ActivePerl

- 1) Install MinGW package through PPM: Using a cmd window type
`ppm install MinGW` for 32bits Windows or `ppm install MinGW64` for
64bits Windows. Is IMPORTANT to check previously if ActiveState
provides the [MinGW](http://code.activestate.com/ppm/MinGW/) package for your ActivePerl version. For example,
although ActivePerl 5.18.2.1802 is currently available (May 2014),
the download page point mainly at ActivePerl 5.16.3.1604, and the
MinGW package is available for version 5.16 but NOT for version 5.18.

### CPAN for ActivePerl and Strawberry Perl

- 1) Open a cmd window by going to `Start >> Run` and typing `cmd`
into the box and pressing return.

- 2) Type `cpan` to enter the CPAN shell.

- 3) At the `cpan>` prompt, type 'install CPAN' to upgrade to the
latest version.

- 4) Quit (by typing 'q') and reload cpan. You may be asked some
configuration questions; accepting defaults is fine.

- 5) At the `cpan>` prompt, type `o conf prefer_installer MB` to tell
CPAN to prefer to use `Build.PL` scripts for installation. Type `o conf commit` to save that choice.

- 6) At the `cpan>` prompt, type `install Module::Build`.

- 7) At the `cpan>` prompt, type `install Test::Harness`.

- 8) At the `cpan>` prompt, type `install Test::Most`.

### You can now follow the unix instructions for [installing using CPAN](http://www.bioperl.org/wiki/Installing_Bioperl_for_Unix#INSTALLING_BIOPERL_THE_EASY_WAY_USING_CPAN) (preferred), or install manually:

- 9) [Download](http://bioperl.org/DIST/) the `.zip` version of the package you want.

- 10) Extract the archive in the normal way.

- 11) In a cmd window `cd` to the directory you extracted to. Eg. if
you extracted to directory 'Temp', `cd Temp\bioperl-1.5.2_100`

- 12) Type `perl Build.PL` and answer the questions appropriately.

- 13) Type `perl Build test`. All the tests should pass, but if they
don't, [let us know](http://bioperl.org/wiki/Mailing_lists#Main_BioPerl_list). Your usage of Bioperl may not be affected
by the failure, so you can choose to continue anyway.

- 14) Type `perl Build install` to install Bioperl.

Bioperl
====

   [Bioperl](http://www.bioperl.org/wiki/Bioperl) is a large collection of Perl modules (extensions to the
   Perl language) that aid in the task of writing Perl code to deal
   with sequence data in a myriad of ways. Bioperl provides objects for
   various types of sequence data and their associated features and
   annotations. It provides interfaces for analysis of these sequences with a
   wide variety of external programs ([BLAST](http://www.bioperl.org/wiki/BLAST), [FASTA](http://www.bioperl.org/wiki/FASTA), [clustalw](http://www.bioperl.org/wiki/Clustalw) and
   [EMBOSS](http://www.bioperl.org/wiki/EMBOSS) to name just a few). It provides interfaces to various types of
   databases both remote ([GenBank](http://www.bioperl.org/wiki/GenBank), [EMBL](http://www.bioperl.org/wiki/EMBL) etc) and local ([MySQL](http://www.mysql.com/),
   Flat_databases flat files, [GFF](http://www.bioperl.org/wiki/GFF) etc.) for storage and retrieval of
   sequences. And finally with its associated [documentation](http://doc.bioperl.org/) and
   [mailing lists](http://www.bioperl.org/wiki/Mailing_lists), Bioperl represents a community of bioinformatics
   professionals working in Perl who are committed to supporting both
   development of Bioperl and the new users who are drawn to the project.

   While most bioinformatics and computational biology applications are
   developed in UNIX/Linux environments, more and more programs are
   being ported to other operating systems like Windows, and many users
   (often biologists with little background in programming) are looking for
   ways to automate bioinformatics analyses in the Windows environment.

   Perl and Bioperl can be installed natively on Windows NT/2000/XP.
   Most of the functionality of Bioperl is available with this type of
   install. Much of the heavy lifting in bioinformatics is done by programs
   originally developed in lower level languages like C and Pascal
   (e.g. BLAST, clustalw, Staden etc). Bioperl simply acts as
   a wrapper for running and parsing output from these external programs.

   Some of those programs (BLAST for example) are ported to Windows.
   These can be installed and work quite happily with Bioperl in the native
   Windows environment. Some external programs such as Staden and the
   EMBOSS suite of programs can only be installed on Windows by using
   [Cygwin](http://www.cygwin.com/) and its [gcc C compiler](http://gcc.gnu.org/) (see Bioperl in Cygwin, below).
   Recent attempts to port EMBOSS to Windows, however, have been mostly
   successful.

   If you have a fairly simple project in mind, want to start using Bioperl
   quickly, only have access to a computer running Windows, and/or don't mind
   bumping up against some limitations then Bioperl on Windows may be a
   good place for you to start. For example, downloading a bunch of sequences
   from GenBank and sorting out the ones that have a particular
   annotation or feature works great. Running a bunch of your sequences
   against [remote](https://metacpan.org/pod/Bio::Tools::Run::RemoteBlast) or [local](https://metacpan.org/pod/Bio::Tools::Run::StandAloneBlast) BLAST, parsing the output and storing it
   in a MySQL database would be fine also.

   Be aware that most Bioperl developers are working in some type of a
   UNIX environment (Linux, OS X, Cygwin). If you have
   problems with Bioperl that are specific to the Windows environment, you
   may be blazing new ground and your pleas for help on the Bioperl mailing
   list may get few responses (you can but try!) - simply because no one
   knows the answer to your Windows specific problem. If this is or becomes a
   problem for you then you are better off working in some type of UNIX-like
   environment. One solution to this problem that will keep you working on a
   Windows machine it to install Cygwin, a UNIX emulation environment for
   Windows. A number of Bioperl users are using this approach successfully
   and it is discussed in more detail below.

Bioperl on Windows
====

   [Perl](http://www.bioperl.org/wiki/Perl) is a programming language that has been extended a lot by the
   addition of external modules.

   These modules work with the core language to extend the functionality of
   Perl.

   [Bioperl](http://www.bioperl.org/wiki/Bioperl) is one such extension to Perl. These modular extensions to
   Perl sometimes depend on the functionality of other Perl modules and this
   creates a dependency. You can't install module X unless you have already
   installed module Y. Some Perl modules are so fundamentally useful that the
   Perl developers have included them in the core distribution of Perl - if
   you've installed Perl then these modules are already installed. Other
   modules are freely available from CPAN, but you'll have to install them
   yourself if you want to use them. Bioperl has such dependencies.

   Bioperl is actually a large collection of Perl modules (over 1000
   currently) and these modules are split into seven packages. These seven
   packages are:

|    Bioperl Group     |                    Functions                    |
|----------------------+-------------------------------------------------|
|bioperl (the core)    |Most of the main functionality of Bioperl        |
|bioperl-run           |Wrappers to a lot of external programs           |
|bioperl-ext           |Interaction with some alignment functions and the Staden package |
|bioperl-db            |Using Bioperl with BioSQL and local relational databases |
|bioperl-microarray    |Microarray specific functions                    |
|bioperl-pedigree      |manipulating genotype, marker, and individual data for linkage studies |
|bioperl-gui           |Some preliminary work on a graphical user interface to some Bioperl functions |
<br>
   The Bioperl core is what most new users will want to start with. Bioperl
   (the core) and the Perl modules that it depends on can be easily installed
   with the perl package Manager [PPM](http://aspn.activestate.com/ASPN/docs/ActivePerl/5.8/faq/ActivePerl-faq2.html). PPM is an ActivePerl utility for
   installing Perl modules on systems using ActivePerl. PPM will look online
   (you have to be connected to the internet of course) for files (these
   files end with .ppd) that tell it how to install the modules you want and
   what other modules your new modules depends on. It will then download and
   install your modules and all dependent modules for you.

   These .ppd files are stored online in PPM repositories. ActiveState
   maintains the largest PPM repository and when you installed ActivePerl PPM
   was installed with directions for using the ActiveState repositories.
   Unfortunately the ActiveState repositories are far from complete and other
   ActivePerl users maintain their own PPM repositories to fill in the gaps.
   Installing will require you to direct PPM to look in three new
   repositories as detailed in Installation Guide.

   Once PPM knows where to look for Bioperl and it's dependencies you simply
   tell PPM to search for packages with a particular name, select those of
   interest and then tell PPM to install the selected packages.

Beyond the Core
====

   You may find that you want some of the features of other Bioperl groups
   like bioperl-run or bioperl-db. Currently, plans include setting up PPM
   packages for installing these parts of Bioperl; check this by doing a
   Bioperl search in PPM.  If these are not available, though, you can use
   the following instructions for installing the other distributions.

   For bioperl-run, bioperl-db and bioperl-network v1.5.2 or higher you can use
   the PPD or CPAN installation instructions above. For other packages you will
   need nmake (see also the CPAN installation instructions), and a willingness
   to experiment. You'll have to read the installation documents for each
   component that you want to install, and use nmake where the instructions
   call for make, like so:

```
 perl Makefile.PL
 nmake
 nmake test
 nmake install
```

   `nmake test` will likely produce lots of warnings, many of these can be
   safely ignored. You will have to determine from the installation documents
   what dependencies are required, and you will have to get them, read their
   documentation and install them first. It is recommended that you look
   through the PPM repositories for any modules before resorting to using `nmake`
   as there isn't any guarantee modules built using `nmake` will work. The
   details of this are beyond the scope of this guide. Read the documentation.
   Search Google. Try your best, and if you get stuck consult with others on
   the [BioPerl mailing list](http://www.bioperl.org/wiki/Mailing_lists).

### Setting environment variables

   Some modules and tools such as [Bio::Tools::Run::StandAloneBlast](https://metacpan.org/pod/Bio::Tools::Run::StandAloneBlast) and
   `clustal_w`, require that [environment variables](http://en.wikipedia.org/wiki/environment_variables) are set; a few examples
   are listed in the INSTALL document. Different versions of Windows utilize
   different methods for setting these variables. **NOTE**: The instructions that
   comes with the BLAST executables for setting up BLAST on Windows are
   out-of-date. Go to the following web address for instructions on setting
   up standalone BLAST for Windows:
   http://www.ncbi.nlm.nih.gov/staff/tao/URLAPI/pc_setup.html

- For Windows XP, go [here](http://www.microsoft.com/resources/documentation/windows/xp/all/proddocs/en-us/environment_variables.mspx). This does not require a reboot but all
       active shells will not reflect any changes made to the environment.
- For older versions (Windows 95 to ME), generally editing the `C:\autoexec.bat` file to add a variable works. This requires a reboot. Here's an example:

`set BLASTDB=C:\blast\data`

For either case, you can check the variable this way:

```
C:\Documents and Settings\Administrator>echo %BLASTDB%
C:\blast\data
```

   Some versions of Windows may have problems differentiating forward and
   back slashes used for directories. In general, always use backslashes `\`.
   If something isn't working properly try reversing the slashes to see if it
   helps.

   For setting up Cygwin environment variables quirks, see an example
   [below](#Directory_for_temporary_files).

### Installing bioperl-db

   bioperl-db now works for Windows w/o installing CygWin. This has
   primarily been tested on WinXP using MySQL5, but it is expected that other
   bioperl-db supported databases (PostgreSQL, Oracle) should work.

   You will need Bioperl rel. 1.5.2, a relational database (I use MySQL5 here
   as an example), and the Perl modules DBI and DBD::mysql, which
   can be installed from PPM as desribed above (make sure the additional
   repositories for Kobes and Bribes are added, they will have the latest
   releases). Do NOT try using nmake with these modules as they will not
   build correctly under Windows! The PPM builds, by Randy Kobes, have been
   modified and tested specifically for Windows and ActivePerl.

   NOTE: we plan on having a PPM for bioperl-db available along with the
   regular bioperl 1.5.2 release PPM. We will post instructions at that
   time on using PPM to install bioperl-db.

   To begin, follow instructions detailed in the Installation Guide for
   adding the three new repositories (Bioperl, Kobes and Bribes). Then
   install the following packages:

           1) DBI
           2) DBD-mysql

   The next step involves creating a database. The following steps are for
   MySQL5:

 >mysqladmin -u root -p create bioseqdb
 Enter password: **********

   The database needs to be loaded with the BioSQL schema, which can be
   downloaded as a tarball here.

 >mysql -u root -p bioseqdb < biosqldb-mysql.sql
 Enter password: **********

   Download bioperl-db from the anonymous Git repository. Use the following
   to install the modules:

 perl Makefile.PL
 nmake

   Now, for testing out bioperl-db, make a copy of the file
   DBHarness.conf.example in the bioperl-db test subdirectory (bioperl-db\t).
   Rename it to DBHarness.biosql.conf, and modify it for your database setup
   (particularly the user, password, database name, and driver). Save the
   file, change back to the main bioperl-db directory, and run 'nmake test'.
   You may see lots of the following lines,

 ....
 Subroutine Bio::Annotation::Reference::(eq redefined at C:/Perl/lib/overload.pm line 25,
     <GEN0> line 1.
 Subroutine new redefined at C:\Perl\src\bioperl\bioperl-live/Bio\Annotation\Reference.pm line 80,
     <GEN0> line 1.
 ....

   which can be safely ignored (again, these come from ActivePerl's paranoid
   '-w' flag). All tests should pass. NOTE : tests should be run with
   a clean database with the BiOSQL schema loaded, but w/o taxonomy loaded
   (see below).

   To install, run:

 nmake install

   It is recommended that you load the taxonomy database using the script
   load_ncbi_taxonomy.pl included in biosql-schema\scripts. You will need to
   download the latest taxonomy files. This can be accomplished using the
   -download flag in load_ncbi_taxonomy.pl, but it will not 'untar' the file
   correctly unless you have GNU tar present in your PATH (which most Windows
   users will not have), thus causing the following error:

 >load_ncbi_taxonomy.pl -download -driver mysql -dbname bioseqdb -dbuser root -dbpass **********
 The system cannot find the path specified.
 Loading NCBI taxon database in taxdata:
         ... retrieving all taxon nodes in the database
         ... reading in taxon nodes from nodes.dmp
 Couldn't open data file taxdata/nodes.dmp: No such file or directory rollback ineffective with
 AutoCommit enabled at C:\Perl\src\bioperl\biosql-schema\scripts\load_ncbi_taxonomy.pl line 818.
 Rollback ineffective while AutoCommit is on at
 C:\Perl\src\bioperl\biosql-schema\scripts\load_ncbi_taxonomy.pl line 818.
 rollback failed: Rollback ineffective while AutoCommit is on

   Use a file decompression utility like 7-Zip to 'untar' the files in
   the folder (if using 7-Zip, this can be accomplished by right-clicking on
   the file and using the option 'Extract here'). Rerun the script without
   the -download flag to load the taxonomic information. Be patient, as this
   can take quite a while:

 >load_ncbi_taxonomy.pl -driver mysql -dbname bioseqdb -dbuser root -dbpass **********

 Loading NCBI taxon database in taxdata:
         ... retrieving all taxon nodes in the database
         ... reading in taxon nodes from nodes.dmp
         ... insert / update / delete taxon nodes
         ... (committing nodes)
         ... rebuilding nested set left/right values
         ... reading in taxon names from names.dmp
         ... deleting old taxon names
         ... inserting new taxon names
         ... cleaning up
 Done.

   Now, load the database with your sequences using the script
   load_seqdatabase.pl, in bioperl-db's bioperl-db\script directory:

 C:\Perl\src\bioperl\bioperl-db\scripts\biosql>load_seqdatabase.pl -drive mysql
                               -dbname bioseqdb -dbuser root -dbpass **********
 Loading NP_249092.gpt ...
 Done.

   You may see occasional errors depending on the sequence format, which is a
   non-platform-related issue. Many of these are due to not having an updated
   taxonomic database and may be rectified by updating the taxonomic
   information as detailed in load_ncbi_taxonomy.pl's POD.

   Thanks to Baohua Wang, who found the initial Windows-specific problem in
   Bio::Root::Root that led to this fix, to Sendu Bala for fixing
   Bug #1938, and to Hilmar Lapp for his input.

Bioperl in Cygwin
====

   Cygwin is a Unix emulator and shell environment available free at
   http://www.cygwin.com. Bioperl v. 1.* supposedly runs well within Cygwin,
   though the latest release has not been tested with Cygwin yet. Some
   users claim that installation of Bioperl is easier within Cygwin than
   within Windows, but these may be users with UNIX backgrounds. A note on
   Cygwin: it doesn't write to your Registry, it doesn't alter your system or
   your existing files in any way, it doesn't create partitions, it simply
   creates a cygwin/ directory and writes all of its files to that directory.
   To uninstall Cygwin just delete that directory.

   One advantage of using Bioperl in Cygwin is that all the external modules
   are available through CPAN - the same cannot be said of ActiveState's PPM
   utility.

   To get Bioperl running first install the basic Cygwin package as well as
   the Cygwin perl, make, binutils, and gcc packages. Clicking the View
   button in the upper right of the installer window enables you to see
   details on the various packages. Then start up Cygwin and follow the
   Bioperl installation instructions for UNIX in Bioperl's INSTALL file
   (for example, THE BIOPERL BUNDLE and INSTALLING BIOPERL THE EASY WAY USING
   CPAN).

bioperl-db in Cygwin
====

   This package is installed using the instructions contained in the package,
   without modification. Since postgres is a package within Cygwin this is
   probably the easiest of the 3 platforms supported in bioperl-db to
   install (postgres, Mysql, Oracle).

Cygwin tips
====

   If you can, install Cygwin on a drive or partition that's
   NTFS-formatted, not FAT32-formatted. When you install Cygwin on
   a FAT32 partition you will not be able to set permissions and ownership
   correctly. In most situations this probably won't make any difference but
   there may be occasions where this is a problem.

   If you're trying to use some application or resource outside of Cygwin
   directory and you're having a problem remember that Cygwin's path syntax
   may not be the correct one. Cygwin understands /home/jacky or
   /cygdrive/e/cygwin/home/jacky (when referring to the E: drive) but the
   external resource may want `E:/cygwin/home/jacky`. So your `*rc` files may end
   up with paths written in these different syntaxes, depending.

MySQL and DBD::mysql
====

   You may want to install a relational database in order to use BioPerl
   db, BioSQL or OBDA. The easiest way to install Mysql is to use
   the Windows binaries available at http://www.mysql.com. Note that
   Windows does not have sockets, so you need to force the Mysql connections
   to use TCP/IP instead. Do this by using the -h, or host, option from the
   command-line. Example:

 >mysql -h 127.0.0.1 -u <user> -p<password> <database>

   Alternatively you could install postgres instead of MySQL, postgres is
   already a package in Cygwin.

   One known issue is that DBD::mysql can be tricky to install in Cygwin
   and this module is required for the bioperl-db, Biosql, and
   bioperl-pipeline external packages. Fortunately there's some good
   instructions online:

     * Instructions included with DBD::mysql:
       http://search.cpan.org/src/JWIED/DBD-mysql-2.1025/INSTALL.html#windows/cygwin

     * Additional instructions if you run into any problems; this
       information is more up-to-date, covers post-2.9 DBD::mysql quirks in
       Cygwin.
       http://rage.against.org/installingdbdmysqlInCygwin

Expat
====

   Note that expat comes with Cygwin (it's used by the modules
   XML::Parser and XML::SAX::ExpatXS, which are used by certain
   Bioperl modules).

Directory for temporary files<a name="Directory_for_temporary_files"></a>
====

   Set the environmental variable TMPDIR, programs like BLAST and
   clustalw need a place to create temporary files. e.g.:

 setenv TMPDIR e:/cygwin/tmp     # csh, tcsh
 export TMPDIR=e:/cygwin/tmp    # sh, bash

   This is not the syntax that Cygwin understands, which would be something
   like /cygdrive/e/cygwin/tmp or /tmp, this is the syntax that a Windows
   application expects.

   If this variable is not set correctly you'll see errors like this when you
   run Bio::Tools::Run::StandAloneBlast:

   ------------- EXCEPTION: Bio::Root::Exception -------------
   MSG: Could not open /tmp/gXkwEbrL0a: No such file or directory
   STACK: Error::throw
   ..........

   [edit]

BLAST
====

   If you want use BLAST we recommend that the Windows binary be obtained from
   NCBI (ftp://ftp.ncbi.nih.gov/blast/executables/blast+/LATEST/ - the file
   will be named something like ncbi-blast-2.2.29+-win64.exe). Then follow the
   Windows instructions from BLAST Help
   (http://www.ncbi.nlm.nih.gov/books/NBK1762). You will also need to set the
   BLASTDIR environment variable to reflect the directory which holds the blast
   executable and data folder. You may also want to set other variables to
   reflect the location of your databases and substitution matrices if they
   differ from the location of your blast executables; see Installing Bioperl
   for Unix for more details.

Compiling C code
====
   Although we've recommended using the BLAST and MySQL binaries
   you should be able to compile just about everything else from source code
   using Cygwin's gcc. You'll notice when you're installing Cygwin that many
   different libraries are also available (gd, jpeg, etc.).
