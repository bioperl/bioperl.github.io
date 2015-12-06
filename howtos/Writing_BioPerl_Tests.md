---
title: "HOWTO:Writing BioPerl Tests"
layout: howto
---

Abstract
========

This is a quick HOWTO on writing tests for old and new BioPerl code.

Authors
=======

Christopher Fields(http://www.bioperl.org/wiki/Christopher_Fields)

Spiros Denaxas(http://www.bioperl.org/wiki/Spiros_Denaxas)

Sendu Bala(http://www.bioperl.org/wiki/Sendu_Bala)

Introduction
============

The BioPerl developers are currently switching from using the [Test](https://metacpan.org/pod/Test&mode=all) module to the more current [Test::More](https://metacpan.org/pod/Test::More&mode=all), accessed via [Bio::Root::Test](http://code.open-bio.org/svnweb/index.cgi/bioperl/view/bioperl-live/trunk/Bio/Root/Test.pm). This is meant to be a general guide on how to write BioPerl-related regression tests using [Test::More](https://metacpan.org/pod/Test::More&mode=all) (and possibly other modules related to test writing that we may include in the future, which can have additional sections added as needed).

Some guidelines
===============

1. Use Bio::Root::Test and its methods wherever possible.
2. Tests that require internet access should be skipped unless network tests have been enabled by the user. Use Bio::Root::Test's `test_skip()` method to handle this.
3. BioPerl has a long list of [dependencies](http://www.bioperl.org/wiki/BioPerl_Dependencies) which are optionally installed. If a set of tests require some of those dependencies, use Bio::Root::Test's `test_skip()` method to skip them if the dependencies aren't present.

Using Bio::Root::Test
=====================

The `BEGIN` block
-----------------

Since BioPerl still supports Perl 5.6.1 (which doesn't include [Test::More](https://metacpan.org/pod/Test::More&mode=all) in the core modules), we must run a quick check for the presence of the module and load it if it is absent. We include a local copy of [Test::More](https://metacpan.org/pod/Test::More&mode=all) within the BioPerl distribution (in `t/lib`) just in case. Likewise for [Test::Exception](https://metacpan.org/pod/Test::Exception&mode=all) and [Test::Warn](https://metacpan.org/pod/Test::Warn&mode=all).

We also like to skip all tests if they all require external modules that haven't been installed, or internet access when the user hasn't enabled network tests.

The Bio::Root::Test module in core ('live') distribution handles these things for us:

```perl
use strict;

BEGIN {
   use lib '.'; # for core package test scripts only
   use Bio::Root::Test;

   test_begin(-tests => 20,
              -requires_modules => [qw(IO::String XML::Parser)],
              -requires_networking => 1);

   # at this point Test::More, Test::Exception and Test::Warn have been
   # loaded for us, and if network tests have been enabled and IO::String
   # and XML::Parser are installed, we will continue with our tests
}
```

Printing debugging information
------------------------------

When tests are run normally, output should be kept to a minimum: only test passes, fails and warnings should be visible. However, when actively developing or debugging a particular module, it can be useful to be more verbose. Currently, setting the environment variable `BIOPERLDEBUG` to `1` or more acts as the specifier to output debugging information. The recommended way of detecting that choice is as follows:

```perl
my $debug = test_debug();

# set the verbosity of instantiated objects appropriately
my $obj = Bio::ModuleUnderTest->new(-verbose => $debug);
```

Some method calls may issue an expected warning that is non-informative. These should be hidden during normal test runs, but printed when debugging:

```perl
my $debug = test_debug();

my $obj = Bio::ModuleUnderTest->new(-verbose => $debug ? $debug : -1);
$obj->method_that_gives_warning();
$obj->verbose($debug);
```

Input & Output files
--------------------

Many tests involve parsing some data stored in a file. The standard location for test data is in the `data` subdirectory of the `t` subdirectory of the distribution. To get the path to a desired data file in a platform-independent way, make use of `test_input_file()`:

```perl
# in unix terms, we want to test with a file t/data/input_file.txt
my $input_file = test_input_file('input_file.txt');
$obj->(-file => $input_file);
...
```

Some tests involve writing out some data to a file and then testing if the output file is ok. To get the path of a file that can be written to in a platform-independent way, and to also automatically delete the file after the test script finishes, make use of `test_output_file()`:

```perl
my $output_file = test_output_file();
$obj->(-file => ">$output_file");

...

# do something that should output data to $output_file,
# then at the very least test that $output_file has some
# size: an -e isn't good enough since the file exists
# as soon as you request the file name with test_output_file()

ok -s $output_file;
# once you're done, don't try and delete $output_file yourself
```

Using [Test::More](https://metacpan.org/pod/Test::More&mode=all) with BioPerl via [Bio::Root::Test](https://metacpan.org/pod/Bio::Root::Test)
=======================

This is a general guideline on how to use [Test::More](https://metacpan.org/pod/Test::More&mode=all) with BioPerl. You are not bound to using this format and may very well find a more suitable (possibly much better) style for writing your own tests. If so, please add it here. However, note that currently Bio::Root::Test only supports [Test::More](https://metacpan.org/pod/Test::More&mode=all).

Module tests
------------

We highly recommend testing module compilation by using `use_ok()` or `require_ok()`. These are normally placed in the `BEGIN` block in order to ensure compile-time exporting of functions and prototype declarations. In most cases this will be unnecessary due to the highly object-oriented nature of BioPerl and the general lack of function prototypes in the current code base. If you are unsure, it never hurts to place these in the `BEGIN` block (all current BioPerl core tests are set up this way):

```perl
use strict;

BEGIN {
    use lib '.'; # for core package test scripts only
    use Bio::Root::Test;

    test_begin(-tests => 20,
               -requires_modules => [qw(IO::String)],
               -requires_networking => 1);

    use_ok('Bio::ModuleUnderTest');
}
```

Simple tests
------------

Now for the actual tests! If you are only worried about checking success or failure, you can use `ok()`.

```perl
ok($value, 'Testing value');
```

The test can have an optional description (`'Testing value'`) which is displayed when tests are run. This is generally the last argument for most functions.

If possible one should use more explicit tests. For instance if you expect a specific value, you can test using `is()`:

```perl
is($value, 10);
```

Or if something is not supposed to be a specific value:

```perl
isnt($value, 10);
```

Fuzzy values, where the value is expected to be less than or greater than a known value, use `cmp_ok()`:

```perl
cmp_ok($value, '<', 10);
cmp_ok($value, '>', 1);
```

Similarly, if fuzzy string match is needed, use `like()` or `unlike()` along with a regex string:

```perl
like($string, qr/GenBank/);
unlike($string, qr/EMBL/);
```

If you want to display a diagnostic message when a test fails, use `diag($msg)`

```perl
ok($value, 'Testing value') || diag("Something wrong with value $value");
is($value, 10, 'DB ID') || diag("Unexpected value:not equal to 10");
```

If you are instantiating a new BioPerl object you can check whether it is of a particular class or implement a particular interface using `isa_ok`:

```perl
isa_ok($testobj, 'Bio::DB::MyDB');
isa_ok($testobj, 'Bio::DB::RandomAccessI');
```

and which methods it implements:

```perl
my @methods = qw(get_Seq_by_id get_Stream_by_id);
can_ok($testobj, @methods);
```

Significant differences between [Test](https://metacpan.org/pod/Test&mode=all) and [Test::More](https://metacpan.org/pod/Test::More&mode=all)
---------------------------------------------------

When either converting tests over from [Test](https://metacpan.org/pod/Test&mode=all) or writing new tests based on older [Test](https://metacpan.org/pod/Test&mode=all)-based code, take note of some small gotchas that occur.

For instance, descriptive messages passed as an additional argument to `ok()` in [Test](https://metacpan.org/pod/Test&mode=all) are diagnostic in nature (show up on failure).

```perl
ok($value, 12, "$value not okay");
```

With [Test::More](https://metacpan.org/pod/Test::More&mode=all) you can have both descriptive messages (always displayed, normally to describe the test in some way) and optional user-based diagnostic messages (appear during the test run, which do not interfere with tests). The above [Test](https://metacpan.org/pod/Test&mode=all) could be the following in [Test::More](https://metacpan.org/pod/Test::More&mode=all), with an added descriptive message:

```perl
ok($value, "Total sequences in SeqIO stream") || diag("$value not okay");
```

Of course, you'll probably want to use `is()` or something more explicit instead:

```perl
is($value, 12, "Total sequences in SeqIO stream") || diag("$value wrong; Got $value, expected 12");
```

Note that just substituting the older Test-based `ok()` to the newer Test::More-based `is()` in the first example:

```perl
is($value, 12, "$value not okay");
```

will have a misleading descriptive message when tests are run, which may lead one to think tests have failed:

```
...
ok 66
ok 67 - 12 not okay
ok 68
...
```

More complex tests
------------------

If full arrays or hashes need to be compared, one could use `is_deeply()`:

```perl
is_deeply(\@arr1, \@arr2);
is_deeply(\%h1, \%h2);
```

`is_deeply()` is also capable of comparing more complex data structures. Note that this hasn't been currently tested extensively in BioPerl.

Alternatively, one could also use [Test::Deep](http://search.cpan.org/~fdaly/Test-Deep-0.096/) which provides us with a greater level of output an a more detailed insight into where the data structures differ. The syntax is very similar to the native `is_deeply`

```perl
cmp_deeply(\@arr1, \@arr2);
cmp_deeply(\%h1, \%h2);
```

Skipping tests
--------------

Skipped tests require the use of a `SKIP:{}` block. When a skip statement is encountered it will skip the listed number of tests to the end of the block. Note that this assumes you have checked to make sure the number of skipped tests matches those expected. If the planned tests do not match then [Test::Harness](https://metacpan.org/pod/Test::Harness&mode=all) will indicate a failure.

```perl
SKIP: {
   ...
   skip('No val', 10) if !$val;
   is($val, 10);
   # and 9 other tests
}
```

It is also required that these are used in cases where issues beyond the control of the code base could cause tests to fail, such as remote database accession:

```perl
SKIP: {
   my $db = Bio::DB::GenBank->new();
   eval {$seq = $db->get_Seq_by_id('AB12345');};
   skip('Remote database issues',10) if $@;
   isa_ok($seq, 'Bio::SeqI');
   # and 9 other tests
}
```

In the special cases of skipping due to missing optional external dependencies, or skipping a subset of tests that need network access, use Bio::Root::Test's `test_skip()` method:

```perl
SKIP: {
   test_skip(-tests => 10, -requires_module => 'Optional::Module');
   use_ok('Optional::Module');

   # 9 other optional tests that need Optional::Module
}

SKIP: {
   test_skip(-tests => 10, -requires_networking => 1);

   # 10 optional tests that require internet access (only makes sense in the
   # context of a script that doesn't use -requires_networking in the call to
   # &test_begin)
}

```

It is possible to nest `SKIP:{}` blocks if needed. See the [Test::More](https://metacpan.org/pod/Test::More&mode=all) documentation for more details.

TODO tests
----------

TODO blocks are very useful during development. They let you declare tests you expect to fail (because you haven't coded the method yet), whilst still letting the script pass ok.

```perl
TODO: {
   local $TODO = 'next_val not implemented yet';
   my $val = $foo->next_val;
   is ($val, 1);
   ...
}
```

Using [Test::Exception](https://metacpan.org/pod/Test::Exception&mode=all) with BioPerl
==================

Along with [Test::More](https://metacpan.org/pod/Test::More&mode=all), Bio::Root::Test also loads [Test::Exception](https://metacpan.org/pod/Test::Exception&mode=all). This module provides a number of useful methods. From its synopsis:

```perl
# Check that something died
dies_ok { $foo->method1 } 'expecting to die';

# Check that something did not die
lives_ok { $foo->method2 } 'expecting to live';

# Check that the stringified exception matches given regex
throws_ok { $foo->method3 } qr/division by zero/, 'zero caught okay';

# Check an exception of the given class (or subclass) is thrown
throws_ok { $foo->method4 } 'Error::Simple', 'simple error thrown';

# all Test::Exceptions subroutines are guaranteed to preserve the state
# of $@ so you can do things like this after throws_ok and dies_ok
like $@, 'what the stringified exception should look like';

# Check that a test runs without an exception
lives_and { is $foo->method, 42 } 'method is 42';
```

Using [Test::Warn](https://metacpan.org/pod/Test::Warn&mode=all) with BioPerl
==================

Bio::Root::Test loads a special version of Test::Warn that can cope with Bioperl's warnings. You can use Test::Warn syntax as normal, though note the following:

```perl
my $debug = test_debug();

# make sure verbosity is set to 0 so that we get normal warnings
$obj->verbose(0);

# do something that is supposed to generate a warning
# (you can run a normal Test::More test at the same time)

warning_is { is $obj->method(), undef } 'You must supply an argument to this method, or return value will be undefined';

$obj->verbose($debug);
```

Additional Resources
====================

Some resources you might find useful:

-   [Ian Langworth's amazing Perl testing reference card](http://langworth.com/pages/perltestref)
-   [The Perl Quality Assurance Projects website](http://qa.perl.org/)
-   [chromatic's Perl testing tutorial](http://www.wgz.org/chromatic/perl/IntroTestMore.pdf)


[Category](http://www.bioperl.org/wiki/Special:Categories): [Developer resources](http://www.bioperl.org/wiki/Category:Developer_resources)
