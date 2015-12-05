---
title: "HOWTO:OBDA"
layout: howto
---

Abstract
--------

This is a HOWTO that explains how to set up and use the [Open Biological Database Access](http://obda.open-bio.org) system.

Authors
-------

-   Aaron Mackey - [amackey at virginia.edu](mailto:amackey-at-virginia.edu)
-   Brian Osborne - [briano at bioteam.net](mailto:briano@bioteam.net)
-   Peter Schattner - [schattner at alum.mit.edu](mailto:schattner-at-alum.mit.edu)
-   Heikki Lehvaslaiho - [heikki at bioperl.org](mailto:heikki-at-bioperl-dot-org)
-   Lincoln Stein - [lstein at cshl.org](mailto:lstein-at-cshl.org)

Copyright
---------

This document is copyright Lincoln Stein, 2002. For reproduction other than personal use please contact lstein at cshl.org

Introduction
------------

Importing sequences with annotations is a central part of most bioinformatics] tasks. BioPerl supports importing sequences from indexed flat-files, local relational databases, and remote (internet) databases. Previously, separate programming syntax was required for each of these types of data access. In addition, if one wanted to change one's mode of sequence-data acquisition (for example, by implementing a local relational database version of GenBank when previously the data had been stored in an indexed flat-file) one had to rewrite all of the data-access subroutines in one's application code.

The Open Biological Database Access (OBDA) system was designed so that one could use the same application code to access data from all three of the database types by simply changing a few lines in a configuration file. This makes application code more portable and easier to maintain. This document shows how to set up the required OBDA registry configuration file and how to access data from the databases referred to in the configuration file using a Perl script as well as from the command line. The Web site for OBDA is http://obda.open-bio.org.

*Note* Accessing data via the OBDA system is optional in BioPerl. One can easily access sequence data via the usual database-format-specific modules such as  [Bio::Index::Fasta](https://metacpan.org/pod/Bio::Index::Fasta) or  [Bio::DB::Fasta](https://metacpan.org/pod/Bio::DB::Fasta).

Using the OBDA Registry System
------------------------------

The OBDA Registry is a platform-independent system for specifying how BioPerl programs find sequence databases. It uses both local and site-wide configuration files, known as the registry, which define one or more databases and the access methods to use to access them.

For instance, you might start out by accessing sequence data over the web, and later decide to install a locally mirrored copy of GenBank. By changing one line in the registry file, all Bio{Perl,Java,Python,Ruby} applications will start using the mirrored local copy automatically - no source code changes are necessary.

Installing the Registry File
----------------------------

The registry file should be named `seqdatabase.ini`. By default, it should be installed in one or more of the following locations:

-   `$HOME/.bioinformatics/seqdatabase.ini`
-   `/etc/bioinformatics/seqdatabase.ini`

The Bio{Perl,Java,Python,Ruby} registry-handling code will initialize itself from the registry file located in the home directory first, and then it will read the system-wide default in `/etc`. Windows Perl users should make sure to set the `$HOME` variable, otherwise the `seqdatabase.ini` file may not be found. Unix users need not do this since the code will use the `getpwuid()` method.

If a local registry file cannot be found, the registry-handling code will attempt to copy the file located at `http://www.open-bio.org/registry/seqdatabase.ini` to a `$HOME/.bioinformatics` directory.

Modifying the Search Path
-------------------------

The registry file search path can be modified by setting the environment variable `OBDA_SEARCH_PATH`. This variable is a semicolon-delimited string of directories and URLs, for example:

`OBDA_SEARCH_PATH=/home/lstein/;http://foo.org/`

***Important*** The fact that the search path is for an entire file (`seqdatabase.ini`) rather than for single entry (e.g. `genbank`) means that you have to copy any default values you want to keep from the (old) default configuration file to your new configuration file.

For example, say you have been using *biofetch* with the default configuration file `http://www.open-bio.org/registry/seqdatabase.ini` for all your sequence-data retrieval. If you now install a local copy of Genbank, your local `seqdatabase.ini` must not only have a section indicating that the Genbank copy is local but it must have sections configuring the web access for all the other databases you use, since `http://www.open-bio.org/registry/seqdatabase.ini` will no longer be found in a registry-file search.

Format of the Registry File
---------------------------

The registry file is a simple text file, as shown in the following example:

```
VERSION=1.00
[embl]
protocol=biofetch
location=http://www.ebi.ac.uk/cgi-bin/dbfetch
dbname=embl
[swissprot]
protocol=biofetch
location=http://www.ebi.ac.uk/cgi-bin/dbfetch
dbname=swall
```

The first line is the registry format version number in the format `VERSION=X.XX`. The current version is 1.00. The rest of the file is composed of simple sections, formatted as:

```
[database-name] 
tag=value 
tag=value
[database-name] 
tag=value 
tag=value
```

Each section starts with a symbolic database name enclosed in square brackets. Database names are case-insensitive but should not contain spaces. The remainder of the section is followed by a series of tag=value pairs that configure access to the service.

Database name sections can be repeated, in which case the client should try each service in turn from top to bottom. The options under each section must have two non-optional `tag=value` lines:

```
protocol="protocol-type"
location="location-string"
```

The Protocol Tag
----------------

The protocol tag specifies what access mode to use. Currently it can be one of:

* flat  
used to fetch sequences from local flat files that have been indexed using [BerkeleyDB] or binary search indexing.

* biofetch  
used to fetch sequences from web-based databses. Due to restrictions on the use of these databases, this is recommended only for lightweight applications.

* biosql  
fetches sequences from [BioSQL](http://biosql.org) database. To use this module you will need to have an instantiated relational database conforming to the [BioSQL](http://biosql.org) schema, and install the [bioperl-db package](https://github.com/bioperl/bioperl-db).

***Important*** Support for the [biosql](http://biosql.org) protocol is only available in recent versions of the [bioperl-db package](https://github.com/bioperl/bioperl-db).

The Location Tag
----------------

The location tag tells the BioPerl sequence fetching code where the database is located. Its interpretation depends on the protocol chosen. For example, it might be a directory on the local file system, or a remote URL. See below for protocol-specific details.

If you are using the `flat` protocol make sure that the location is a directory and the dbname is also a directory, *contained within the location directory*.

The Other Tags
--------------

Any number of additional tag values are allowed. The number and nature of these tags depends on the access protocol selected. Some protocols require no additional tags, whereas others will require several.

| Protocol | Tag      | Description  | Note       |
|----------|----------|--------------------|--------------------|
| flat     | location | Directory in which the database directory is stored |    |
| flat     | dbname   | Name of database directory, "config.dat" file generated during indexing must be found here |   |
| biofetch | location | Base URL for the web service     | The only current biofetch service is http://www.ebi.ac.uk/cgi-bin/dbfetch |
| biofetch | dbname   | Name of the database  | `embl, swall` (SwissProt + TREMBL), `refseq, uniprot, swissprot`    |
| biosql   | location | host:port   |     |
| biosql   | dbname   | Database name   |                   |
| biosql   | driver   | |`mysql,Pg,oracle,sybase,sqlserver,access,csv,informix,odbc,rdb`                           | `Pg` is the driver name for PostgreSQL                                      |
| biosql   | user     | username      |      |
| biosql   | passwd   | password   |  |
Table 1. OBDA protocols

Installing Local Databases
--------------------------

If you are using the `biofetch` protocol, you're all set. You can start reading sequences immediately. 

For the `flat` protocol, you will need to create and initialize a local database:

* [Flat Databases HOWTO](OBDA_Flat_databases.html)

Once the flat database is created you can configure your seqdatabase.ini file. Let's say that you have used the `bioflat_index.pl` script to create the flat database and a new directory called `ppp` has been created in your `/home/sally/bioinf/` directory (and the `ppp/` directory contains the `config.dat` file). Your `sequence.ini` entry should contain these lines:

```
[ppp]
protocol=flat       
location=/home/sally/bioinf       
dbname=ppp
```

The database name, in brackets, can be any useful name, it does not have to refer to existing files or directories, but the `dbname` should be the name of the newly created directory.

For the `biosql` protocol, you will need to create a `BioSQL` database and install `bioperl-db`:

* [BioSQL](http://biosql.org)
* [bioperl-db package](https://github.com/bioperl/bioperl-db)


Writing Code to Use the Registry
--------------------------------

Once you've set up the OBDA registry file, accessing sequence data from within a BioPerl script is simple. The following examples shows how - note that nowhere in the script do you explicitly specify whether the data is stored in a flat file, a local relational database or a database on the internet.

To use the registry from a Perl script, use something like the following:

```perl
use Bio::DB::Registry; 
$registry = Bio::DB::Registry->new;   

$db = $registry->get_database('embl');   
$seq = $db->get_Seq_by_acc("J02231");   
print $seq->seq,"\n";
```

In lines 1 and 2, we bring in the [Bio::DB::Registry](https://metacpan.org/pod/Bio::DB::Registry) module and create a new [Bio::DB::Registry](https://metacpan.org/pod/Bio::DB::Registry) object. We then ask the registry to return a database accessor for the symbolic data source *embl*, which must be defined in an `embl` section in the `seqdatabase.ini` registry file.

The returned accessor is a [Bio::DB::RandomAccessI](https://metacpan.org/pod/Bio::DB::RandomAccessI) object which has these three methods:

*   `$db->get_Seq_by_id($id);`
*   `$db->get_Seq_by_acc($acc);`
*   `$db->get_Seq_by_version($versioned_acc);`

These methods return [Bio::Seq](https://metacpan.org/pod/Bio::Seq) objects by searching for their primary IDs, accession numbers, and accession.version numbers respectively.

Using *biogetseq.pl* to Access Registry Databases
-----------------------------------------------

As a convenience, the BioPerl distribution includes the script `biogetseq.PLS` that enables one to have OBDA access to sequence data from the command line. It's located in the `scripts/DB` directory of the BioPerl distribution (it may also have been installed in your system if you asked for a script installation during the `make install` step). Move or add it into your path to run it. Here's an example of how it's used:

```perl
biogetseq.pl --dbname embl --format embl --namespace acc id1 id2 id3     
```

The following are the script's defaults:

-   `dbname` defaults to *embl*
-   `format` defaults to *embl*
-   `namespace` defaults to *acc* (options are *id*, *acc*, *version*)

The last arguments are a list of ids in the given namespace.

If you have a set of ids you want to fetch from EMBL database, you just give them as space-separated parameters:

```perl
biogetseq.pl J02231 A21530 A10516
```

The output is directed to `STDOUT`, so it can be redirected to a file. The options can be given in the long "double hyphen" format or abbreviated to one-letter format (`--fasta` or `-f`):

```perl
biogetseq.pl -f fasta -n acc J02231 A21530 A10516 > filed.seq
```

