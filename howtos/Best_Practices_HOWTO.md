---
title: "Best Practices HOWTO"
layout: howto
---

This page is a list of ''best practices'' for anyone contributing to Bioperl. Also see [Advanced BioPerl](Advanced_BioPerl_HOWTO.html) and the [Using Git HOWTO](Using_Git_HOWTO.html).

Style
=====

-   Use spaces instead of tabs for indenting
-   Prefix protected/private subroutines/fields with an underscore
-   Interface modules end with a capital "I", e.g. `Bio::LocationI`
-   Driver modules which are loaded dynamically from a "deployer" module are all lower case, e.g. `Bio::SeqIO::genbank`
-   Use blessed hashes for class fields
-   Use a combined getter/setter accessor function for each class field
-   Parse potentially large input files "on-demand" rather than reading all in at once into memory (e.g. make use of [Bio::PullParserI](http://metacpan.org/pod/Bio::PullParserI))

Coding
======

General coding practices
------------------------

-   Always `use strict;`
-   Use `return;` instead of `return undef;`
-   Use `our $x;` instead of `use vars ($x); BEGIN { $x=... };`
-   Use generalized quotes instead of escaping, e.g. `qq{error in "$file"}` not `"error in "$file""`
-   Use clearer `uc($s)`, `lc($s)`, `quotemeta($s)` rather than `\U$s`, `\L$s`, `\Q$s`

Error handling and debugging
----------------------------

-   Use `$self->throw()` instead of `die() / confess()`
-   Use `$self->warn()` instead of `warn() / carp() / cluck()`
-   Use `$self->debug()` instead of `print STDERR "...."`

I/O and cross-platform
----------------------

-   Build file paths with `Bio::Root::IO->catfile(@dir)` or `File::Spec->catfile()` instead of `join(\'/\',@dir)`
-   Use `File::Spec` functions for portability across platforms
-   Use the 3-argument form of `open`, e.g. `open my $FH, '<', 'filename.txt'`
-   Use lexical auto-vivified file handles rather than globs, e.g. `open my $OUT, '>', 'output.txt'`
-   Pre-declare file handles so they don't mask earlier declarations in the same scope (specially when switching from read to write `open()` modes and vice-versa):

```perl
{
my $FH; # 1st and unique declaration
open $FH, "<", $file or $self->throw("Cannot open $file: $!");
my @data = <$FH>;
# do something with @data...
open $FH, ">", $file or $self->throw("Cannot write to $file: $!");
print $FH @data;
close $FH;
}
```

Not:

```perl
{
# 1st declaration
open my $FH, "<", $file or $self->throw("Cannot open $file: $!"); 
my @data = <$FH>;
# do something with @data...
# 2nd declaration
open my $FH, ">", $file or $self->throw("Cannot write to $file: $!"); 
print $FH @data;
close $FH;
}
```

BioPerl Object-oriented programming and modules
-----------------------------------------------

-   Use `use base qw(Bio::Class);` instead of `use vars qw(@ISA); @ISA=qw(Bio::Class);`
-   Use `Bio::Class->new()` instead of `new Bio::Class()`
-   Indirect object syntax can lead to subtle errors which are best avoided.
-   Never use `method Bio::Class(@args)`: this simply doesn't work on some systems.
-   Modules must end by returning true: have `1;` as the last line

Methods
-------

-   For easier code maintenance, unload `@_` into named variables. If there are more that two arguments present, use named parameters and `Bio::Root::RootI->_rearrange()`. In general, always use `Bio::Root::RootI->_rearrange()` for maintainability unless there is a demonstrable and significant performance issue.
-  The method `_rearrange()` takes two arguments. The first argument is an array reference containing the name of the parameters in upper-case letters. The second argument is the array of parameter-value pairs.

Unloading method arguments, two args:

```perl
sub foobar {
    my ($self, $start, $end) = @_;
    #  ...
}
```

Unloading method arguments, more than two args:

```perl
sub barfoo {
    my ($self, @args) = @_;
    my ($start, $end, $score, $strand) = $self->_rearrange(
            [ qw( START END SCORE STRAND ) ], @args);
    # ...
}
```

-   The use of `AUTOLOAD` is controversial for most core BioPerl developers but has been used for [bioperl-run](https://github.com/bioperl/bioperl-run)
-   See the links http://thread.gmane.org/gmane.comp.lang.perl.bio.general/394/focus=397 and http://thread.gmane.org/gmane.comp.lang.perl.bio.general/3927/focus=3927 for the mail list threads concerning the use of `AUTOLOAD` in BioPerl.
-   In short, it is highly recommended not to use `AUTOLOAD` in the core modules unless absolutely necessary, primarily for performance reasons but also because the `UNIVERSAL` method `$self->can()` will not work for `AUTOLOAD`'ed methods.
-   As an alternative, especially for Run wrappers, the use of `_set_from_args()` is recommended, most likely in combination with `_setparams`:

```perl
sub new {
    my($class, @args) = @_;
    my $self = $class->SUPER::new(@args);
    $self->_set_from_args(@args, -methods => [@allowed_methods],
                                 -create => 1);
    return $self;
}

sub _setparams {
    # ...
    my $param_string = $self->SUPER::_setparams(
                   -params => [@settable_methods], -dash => 1);
    return $param_string;
}
```

Regular Expressions
-------------------

-   Don't use the slow special regexp variables *$`*, *$&*, *$'*, *$-*, *$+*
-   Avoid regexps where possible: string `eq` > `index()` > `=~`
-   Use generalized quotes instead of escaping with back-slashes, e.g. `m{//}` not `/\/\//`
-   Avoid using the `o` (compile-once) modifier when combining regular expressions with interpolated variables and loops, which will result in subtle errors. The following compiles the regex to only find 'start', so here the regex will always match, even with 'foobar':

```perl
my @strings = qw(hello goodbye start end flag score);

while (my $string = shift @strings) {
    for my $flag (qw(start end hello foobar)) {
        if ($string =~ m{^$flag}o) {
            print "Got $flag!\n";
        }
    }
}
```

-   Use `qr/.../` rather than strings to pre-store regexps as they provide compile-time syntax checking
-   Use capture parentheses only for capturing, otherwise use `(?:)`
-   For easier code maintenance, unload regex capture variables like `$1` into named variables (similar to what is done for methods, above):

```perl
if ( my ($start, $end, $strand, $score) = 
    $line =~ m{^(d+)s+(d+)s+(d)s+(d+)}xms) {
    # ...
}
```

Sorting
-------

- Never directly `return` from a `sort` (for background see http://use.perl.org/~schwern/journal/28577):

```perl
sub foo {
    # ...
    @sorted = sort @unsorted;
    return @sorted;
}
```

Not:

```perl
sub bar {
    # ...
    return sort @unsorted;
}
```

The latter form has undefined behaviour if `bar()` is called in scalar context.

-   When sorting objects by their method values, use a Schwartzian transformation:

```perl
@sorted = map { $_->\[1\] }
         sort { $a->[0] <=> $b->[0] }
         map { [$_->method(), $_] }
         @unsorted;
```

Not:

```perl
@sorted = sort { $a->method() <=> $b->method() } @unsorted;
```

The latter form is inefficient and can cause subtle bugs if `method()` (indirectly) calls its own sort subroutine

Testing
=======

-   Every module must have tests
-   Test scripts should be named `t/Module.t`
-   Test data files go in `t/data/` in the version control repository
-   Use [Bio::Root::Test](http://metacpan.org/pod/Bio::Root::Test) to write your test script. See the [HOWTO:Writing_BioPerl_Tests](Writing_BioPerl_Tests_HOWTO.html) for details.
-   Before committing changes to the version control repository, make sure that the relevant test script passes:

Do this once, answering 'no' to script installation:

```perl
perl Build.PL
```

Then do this every time you want to run a test script where test.t is the name of the script:

```perl
./Build test --test_files t/test.t --verbose
```

Note that `perl -I. -w t/test.t` is NOT good enough, since it won't catch all problems.

When you're happy the script passes on its own, run the entire test suite:

```perl
./Build test
```

If everything passes, `commit`.

POD
===

-  Ensure your POD has a `=head1 NAME` section with the fully qualified module name and a description, for example:

```
=head1 NAME

Bio::Tools::MyTool - parse MyTool gene predictions

=head1 SYNOPSIS

# Synopsis code demonstrating the module goes here

=head1 DESCRIPTION

A description about this module.

=cut 
```

-  Tests will be included that check there is POD for each public method in a module. Although these tests will not enforce POD for private methods (those starting with an underscore: '_'), it is also advisable to include POD for these methods as it helps other developers to identify what the method is supposed to be for. POD for methods should be in a form such as:

```
=head2 method_name

Title    : method_name
Usage    : Some small examples of method usage
Function : Some description about what the method does
Returns  : What the method does
Args     : What arguments the method takes

=cut
```

-   It is preferable that you also include the following boilerplate in the POD (with the author section filled in appropriately), for example:

```
=head1 FEEDBACK

=head2 Mailing Lists

User feedback is an integral part of the evolution of this and other
Bioperl modules. Send your comments and suggestions preferably to one
of the Bioperl mailing lists.  Your participation is much appreciated.

  bioperl-l@bioperl.org                  - General discussion

=head2 Support

Please direct usage questions or support issues to the mailing list:

I<bioperl-l@bioperl.org>

rather than to the module maintainer directly. Many experienced and
reponsive experts will be able look at the problem and quickly
address it. Please include a thorough description of the problem
with code and data examples if at all possible.

=head2 Reporting Bugs

Report bugs to the Bioperl bug tracking system to help us keep track
the bugs and their resolution. Bug reports can be submitted via the web:

  https://github.com/bioperl/bioperl-live/issues

=head1 AUTHOR

=cut
```

-   All the general documentation about a module should be placed before any code, and each method should have its own documentation just before the method code.
- Use `podchecker` to check your POD syntax
- If using Emacs, use the [bioperl.lisp](Emacs "wikilink") macros - there is a standard boilerplate you can follow.'
