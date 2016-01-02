---
title: "Advanced BioPerl HOWTO"
layout: howto
---

This page discusses code design, for anyone contributing to Bioperl. Also see the [Best Practices HOWTO](Best_Practices_HOWTO.html) and the [Using Git HOWTO](Using_Git_HOWTO.html).

Extending the toolkit
---------------------

Sometimes a function doesn't quite work they way you want for your special question or environment. You don't want to re-write the module (although discussion of that in the next section) and just want to override the function in the context of your script. This is actually quite easy to do in Perl. For example, say you wanted to parse multiple sequence alignment files where the alignments contain letters (DNA or protein residues), gaps (dashes), and additionally numbers for representing another feature in the sequence like intron phases.

Here is the function as defined in [Bio::PrimarySeq](https://metacpan.org/pod/Bio::PrimarySeq).

```perl
sub validate_seq {
        my ($self,$seqstr) = @_;
        if( ! defined $seqstr ){ $seqstr = $self->seq(); }
        return 0 unless( defined $seqstr); 
        if ((CORE::length($seqstr) > 0) && 
           ($seqstr !~ /^([$MATCHPATTERN]+)$/)) {
            $self->warn("seq doesn't validate, mismatch is " .
                        ($seqstr =~ /([^$MATCHPATTERN]+)/g));
            return 0;
        }
        return 1;
 }
```

And `$MATCHPATTERN` is defined as `$MATCHPATTERN = 'A-Za-z-.*?';`.

However we would like to additionally support numbers, and we really only want to support this in the context of alignments. Sequences in alignments are not [Bio::PrimarySeq](https://metacpan.org/pod/Bio::PrimarySeq) objects but [Bio::LocatableSeq](https://metacpan.org/pod/Bio::LocatableSeq) objects, which are extensions of [Bio::PrimarySeq](https://metacpan.org/pod/Bio::PrimarySeq).

```perl
sub Bio::LocatableSeq::validate_seq {
    my ($self,$seqstr) = @_;
    if( ! defined $seqstr ){ $seqstr = $self->seq(); }
    return 0 unless( defined $seqstr); 
    if((CORE::length($seqstr) > 0) && ($seqstr !~ /^([A-Za-z-.*?0-9]+)$/)) {
        $self->warn("seq doesn't validate, mismatch is " .
                    ($seqstr =~ /([^A-Za-z-.*?0-9]+)/g));
        return 0;
    }
    return 1;
}
```

Building new modules
--------------------

Often the toolkit has a set of functionality that supports what the authors needed, if you need additional functionality it may rely on you to write it! However we designed BioPerl to be flexible and extensible. The parser system (all the modules namespaces that end in *IO* like or or was especially designed so that new formats could be *plugged-in* to the system with minimal effort. Let's walk through and an example of writing a parser for a new sequence format.

These fall under the namespace since they are for sequence reading and writing. The convention is to be able to iterate through all the sequences in a file or data stream with the `next_seq()` method. If one wanted to write sequences the method `write_seq` accepts sequence objects and writes them out to the filehandle.

Let's pretend the new format is called *Jenny's simple format*, or *jsf* for short. Choosing a module name can be important but this three letter [acronym](https://en.wikipedia.org/wiki/acronym) should suffice. Like all format modules, the *jsf* module file should be located in a directory called SeqIO which itself is in a directory called *Bio* - so the file would be called *Bio/SeqIO/jsf.pm*. Let's assume the format is simple. Here is an example of the format we'll write a parser for:

```
JSF: ID=N0001 DESC="Sampled from compost" SEQ=CCCCCGGGGGGTTTTTAAAAA 
JSF: ID=N0002 DESC="Sampled from humanure" SEQ=CCCGCCCCGGCAATTTAGTTT 
```

The module to parse this format would look like this:

```perl
package Bio::SeqIO::jsf

use strict;
use Bio::SeqIO;
use base 'Bio::SeqIO'; # This ISA Bio::SeqIO object
use Bio::Seq;

=head2 next_seq
Title   : next_seq
Usage   : $seq = $stream->next_seq()
Function: reads and returns the next sequence in the stream
Returns : Bio::Seq object
Args    : NONE
=cut

sub next_seq {
  my $self = shift;
  my ($seqstring, $id, $description);
  # read sequences from the filehandle (Bio::SeqIO sets this up for you)
  while( $self->_readline ) { 
    if( m/^JSF: ID=(S+)s+DESC="(.+)" SEQ=(S+)/ ) {
      ($id,$description,$seqstring) = ($1,$2,$3);
      last;
    }
  }
  return unless defined $id && defined $description; # returns undef 
  return Bio::Seq->new(-seq         => $seqstring, 
                       -display_id  => $id,
                       -description => $description);
}
```


And the additional method to write the sequence:

```perl

=head2 write_seq
  
Title   : write_seq
Usage   : $stream->write_seq(@seq)
Function: writes each $seq object in @seq to the stream
Returns : 1 for success and 0 for error
Args    : array of 1 to n Bio::PrimarySeqI objects
 
=cut
 
sub write_seq {
    my ($self,@seq) = @_;
    for my $seq ( @seq ) {
        $self->_print(sprintf("JSF: ID=%s DESC="%s" SEQ=%s\n"),
        $seq->display_id, $seq->description, $seq->seq);
    }
}
```

Reusing Code and Working in Collaborative Projects
--------------------------------------------------

The biggest problem often in [reusing a code base](https://en.wikipedia.org/wiki/Reusability) like BioPerl is that it requires both the people using it and the people contributing to it to change their attitude towards code. People in bioinformatics may be self-taught programmers who put together most of their scripts or programs working alone. BioPerl is truly a collaborative project and anyone will be only contributing some part of it in the future.

Here are some notes about how a coding style can change to work in collaborative projects.

Learn to Read Documentation
---------------------------

Reading documentation is sometimes as tough as writing the documentation. Try to read documentation before you ask a question - not only might it answer your question, but more importantly it will give you idea why the person who wrote the module wrote it - and this will be the frame work in which you can understand his or her answer.

You might also want to examine the models, or class diagrams, in the models directory. These diagrams are not guaranteed to include every single class but may help you understand the overall layout of BioPerl's modules.

Respect People's Code
-----------------------

If the code does what you want, the fact that it is not written exactly the way *you* would write is not grounds for removing it or completely rewriting it. Of course, if there is an error in calculations or an identified significant performance bottleneck, then that is worth pointing it out to the author and the developer community. However, dismissing a module on the basis of its coding style is not a productive thing to do.

That said, it is still important that we periodically audit code to take advantage of new ideas in software design and to do performance profiling on code. Perl as a language is still being updated and especially as aspects of [Perl6](https://en.wikipedia.org/wiki/Perl6) make their way to the wild we will have opportunities to review BioPerl code in the light of language improvements. The toolkit is a project that is evolving and benefits from a fresh look so we still welcome your constructive criticism, especially if you are willing to help make the changes.

Learn How to Provide Good Feedback
----------------------------------

This ranges from giving very accurate bug reports through to pointing out design issues in a constructive manner (not *this sucks*). If you find a problem, then providing a patch using `diff` or a work around is a great thing to do.

Providing "I used XXX and it did just what I wanted it to do" feedback is also really great. Developers generally only hear about their mistakes. To hear about successes gives everyone a warm glow.

One trick we have learnt is that when we download a new project/code or use a new module we open up a fresh buffer in `emacs` and keep a mini diary of everything that we did or thought when we started to use the package. After we used it we could go back, edit the buffer and then send it to the author either with "it was great - it did just what I wanted, but I found that the documentation here was misleading" to "to get it to install I had to incant the following things..."

Taking on a Project
-------------------

When you want to get involved, hopefully it will be because you want to extend something or provide better facilities to something. The important thing here is not to work in a vacuum. Providing the main list with a good proposal before you start about what you are going to do (and listen to the responses) is a must. We have been pulled up so many times by other people looking at our designs that we can't imagine coding stuff now without feedback.

Designing Good Tests
--------------------

Sadly, you might think that you have written good code, but you don't know that until you manage to test it! The [CPAN](https://metacpan.org/pod/CPAN) style perl modules have a wonderful test suite system (delve around into the *t/* directories) and we have extended the [makefile](https://en.wikipedia.org/wiki/Makefile) system so that the test script which you write to test the module can be part of the *t/* system from the start. Once a test is in the *t/* system it will be run millions of times worldwide when BioPerl is downloaded, providing incredible and continual [regression testing](https://en.wikipedia.org/wiki/Regression_testing) of your module.

Writing POD documentation
-------------------------

If you are writing code for BioPerl make sure to write good POD. Fill in those NAME, SYNOPSIS, DESCRIPTION, and AUTHOR sections.

Most authors have also documented their methods. The typical approach is to give the method Name and describe the Usage, Function, Arguments, what it Returns, and then an Example. Note that private or internal method names are always preceded by "_". An example of POD for a public method:

```
=head2 new()

 Title   : new()
 Usage   : my $primer = Bio::SeqFeature::Primer(-seq => $seq_object);
 Function: Instantiate a new Bio::SeqFeature::Primer object
 Returns : A Bio::SeqFeature::Primer object
 Args    : -seq, a sequence object or a sequence string (optional)
           -id, the ID to give to the primer sequence, not feature (optional)

=cut
```

The  Object
----------

All objects in BioPerl should inherit from [Bio::Root::Root](https://metacpan.org/pod/Bio::Root::Root), except for interfaces. The BioPerl root object allows a number of very useful concepts to be provided. In particular.

### Exceptions, warning, and debugging

The BioPerl Root object allows exceptions to be thrown by the object with very nice debugging output. These are thrown by calling the method `throw()` and passing in the message string. This will cause the execution of the script to die with a stack trace.

Similarly the `warn()` method can be called which will produce a warning message - use this instead of print for warning messages to the user because if the `verbose` flag is set to `-1` warnings will be skipped. Additionally setting the `verbose` flag to `1` will print a stack trace for every warning in addition to the message and setting `verbose` to `2` will convert warnings into thrown exceptions.

Finally, the `debug()` method prints messages to `STDERR` when the `verbose` flag is set to `1`.

### _rearrange()

BioPerl root object have some helper methods, in particular `_rearrange()` to help functions which take hash inputs. This allows one to specify named arguments as a hash and map them to the expected input parameters specified by an array.

You can go to [Bio::Root::Root](https://metacpan.org/pod/Bio::Root::Root) for more information. There are also a number of useful example scripts in the *examples/root* directory.

### Using the Root object

To use the root object, the object has to inherit from it. This means the `@ISA` array should have [Bio::Root::Root](https://metacpan.org/pod/Bio::Root::Root) in it and that the module has a use `Bio::Root::Root`. The root object provides a top level `new` function. You should inherit from this new method by calling the `new()` method of the superclass which is accessible by using `SUPER`. This is called chaining the constructors and allows a child class to utilize the initialization procedure of the superclass in addition to executing its own. This is a very powerful technique and allows BioPerl to behave in an [object-oriented](https://en.wikipedia.org/wiki/Object_oriented) manner.

The full code is given below for a basic skeleton object that uses BioPerl:

```perl
 # convention is that if you are using the Bio::Root::Root object you
 # should put it inside the Bio namespace
 package Bio::MyNewObject;
 use strict;

 use base qw(Bio::Root::Root);
 # add additional use statements as needed
 sub new {
    my($class,@args) = @_;
    # call superclasses initialize
    my $self = $class->SUPER::new(@args);
    # do your own argument processing here
    my ($arg1) = $self->_rearrange([qw(NAMEDARGUMENT1)], @args);
    # set default attributes etc...
    return $self;
}
```

Method names
------------

A few general rules, not so rigorously enforced. Please keep in mind these are general guidelines; if there are questions please post them to the mailing list. Above all, [respect other's code](Advanced_BioPerl#Respect_People's_Code).

-  Historically, accessor or getter/setter method names correspond to parameters that are passed to the constructor.
-  These are normally explicitly defined (i.e. no AUTOLOAD'ed methods, though see [below](Advanced_BioPerl#Notes_on_Accessor_Methods) for a bit of BioPerl controversy regarding the use of AUTOLOAD).

```perl
$seq->alphabet; 
$seq->seq;
```

-   Methods which return lists of objects should start with a capital letter. This has been (by far) one of the least enforced rules, but it helps when trying to determine whether the data returned is scalar or an object, and (if the latter) what the object class is.
- If the method is just a getter/setter for a single object, it's safe to leave it lower-case.

```perl
$seq->species; # single Bio::Species 
$feature->location; # single Bio::LocationI
```

-   If the method is read-only (just a getter) you might consider using `get_{data/Class}` as it's more explicit.
-   Methods which return lists of data should use the *syntax get_{Class}s* or *get_{data}s* (using plural); the first returning a list of objects and the second returning a list of strings, scalars, etc.
-   If the data is nested (such as `SeqFeatures`) then using a `get_{data}` method should only retrieve the top layer. Methods which retrieve the whole flattened list would use `get_all_{Class}`. Some have used this convention interchangeably (normally not a problem if the data isn't nested).

```perl
$collection->get_all_Annotations; 
# should be a flattened list of Bio::AnnotationI 
$feature->get_all_annotation_keys; 
# list of scalar data; is it nested?
```

-   Avoid naming methods which return multiple values `each_{data/Class}` if possible as the use of `each` is ambiguous to most users. Is it an iterator? A list? Does it return a key-value pair like Perl each?
-   Iterator methods should use `next_{Class/data}` instead, which is much more explicit in meaning:

```perl
# ambiguous (dual meaning)
for my $feat ($obj->each_Feature) {
    ...
} 
my @features = $obj->each_Feature;

# more explicit
while (my $seq = $seqin->next_seq) {
    ...
}
# though should it be next_Seq()? oh well... 
my @features = $obj->get_SeqFeatures;
```

### Notes

The guidelines above are meant for developers who want to contribute new code.

We have started converting some methods over to conform to the above and have started deprecating older methods. We know that there are several (hundred?) more examples of methods that still fall outside of the above guidelines, as well as code that runs afoul of numerous [Best Practices](http://refcards.com/docs/vromansj/perl-best-practices/refguide.pdf) (most BioPerl methods, for instance, do not have separate getter and setter methods). There is no need to point out X method in Y class doesn't follow the rules.

Realize that a majority of the core code was developed prior to the introduction of the guidelines above. Lack of code changes partially stems from a reluctance to deviate from the original API, which will frustrate long-term users or interfere with older scripts when in production use (see the mailing list thread on [Feature/Annotation changes](http://thread.gmane.org/gmane.comp.lang.perl.bio.general/6924/focus=7023) if you want to see how some changes can have a very significant impact). For most users the code gets the job done, so digging into critical code that works well as-is isn't very high on the list of project priorities.

Throwing Exceptions
-------------------

Exceptions are `die` functions, in which the `$@` variable, a scalar, is used to indicate how it died. The exceptions can be caught using the `eval {}` system. The BioPerl root object has a method called `throw` which calls `die` but also provides a full stack trace of where this throw happened on. So an exception like:

```perl
$obj->throw("I am throwing an exception");
```

Provides the following output on STDERR if it is not caught.

```
------------- EXCEPTION: Bio::Root::Exception -------------
MSG: I am throwing an exception
STACK: Error::throw
STACK: Bio::Root::Root::throw /home/jason/bioperl/core/Bio/Root/Root.pm:313
-----------------------------------------------------------
```

Indicating that this exception was thrown at line 7 of subroutine `my_subroutine`, in *myscript.pl*.

Exceptions can be caught using an eval block, such as:

```perl
my $obj = Bio::SomeObject->new();
my $obj2
eval {
  $obj2 = $obj->method1();
  $obj2->method2(10);
};
if( $@ ) {
  # exception was thrown
  tell_user("Exception was thrown, preventing whatever I wanted to do. 
             Actual exception $@");
  exit(0);
}
# else - use $obj2
```

Notice that the `eval` block can have multiple statements in it, and also that if you want to use variables outside of the eval block, they must be declared with my outside of the `eval` block (and you are planning to `use strict` in your scripts, aren't you?).

This context is particularly useful when objects are produced from a database. This is because some exceptions are really due to problems with the data in an object rather than the code. These sort of exceptions are better tracked down when you know where the object came from, not where in the code the exception is thrown.

One of the drawbacks to this scheme is that the attribute name is "special" from BioPerl's perspective. We believe it is best to stay away from using `$obj->name()` to mean anything from the object's perspective (for example `id()`), leaving it free to be used as a context for debugging purposes. You might prefer to overload the name attribute to be "useful" for the object.

See *scripts/root_object/error.pl* for demonstration code.

Bioperl Interface design
------------------------

BioPerl has been moving to a split between *interface* and *implementation* definitions. An interface is solely the definition of what methods one can call on an object, without any knowledge of how it is implemented. An implementation is an actual, working implementation of an object. In languages like [Java](https://en.wikipedia.org/wiki/Java), interface definition is part of the language. In Perl, like many aspects of Perl, you have to roll your own.

In BioPerl, the interface names are called `Bio::MyObjectI`, with the trailing *I* indicating it is an interface definition of an object. The interface files (sometimes nicknamed the 'I files') provide mainly documentation on what the interface is, and how to use and implement it. All the functions which the implementation is expected to provide are defined as subroutines, and then die with an informative warning. The exception to this rule are the implementation independent functions.

Objects which want to implement this interface should inherit the `Bio::MyObjectI` file in their `@ISA array`. This means that if the implementation does not provide a method which the interface defines, rather than the user getting a "method not found error" it gets a "mymethod() was not defined in MyObjectI, but should have been" which makes it clearer that whoever provided the implementation was to blame, and not the caller/script writer.

When people want to check they have valid objects being passed to their functions they should test the presence of the interface, not the implementation. For example:

```perl
sub my_sequence_routine {
   my($seq,$other_argument) = @_;
   # this is the CORRECT way to check the argument type
   $seq->isa('Bio::SeqI') || die "[$seq] is not a sequence. Cannot process";
   # do stuff
}
```

This is in contrast to:

```perl
sub my_incorrect_sequence_routine {
   my($seq,$other_argument) = @_;
   # this line is INCORRECT
   $seq->isa('Bio::Seq') || die "[$seq] is not a sequence. Cannot process";
   # do stuff
}
```

Rationale of Interface Design
-----------------------------

Some people might justifiably argue "why do this?". The main reason is to support external objects from BioPerl, and allow them to masquerade as real BioPerl objects. For example you might have your own quite intricate sequence object which you want to use in BioPerl functions, but don't want to lose your own neat coding. One option would be to have a function which built a BioPerl sequence object from your object, but then you would be endlessly building temporary objects and destroying them, in particular if the script yo-yoed between your code and BioPerl code.

A better solution would be to implement the interface. You would read, and then provide the methods which it required, and put in your `@ISA` array. Then you could pass in your object into Bioperl routines and - *voila* - you *are* a BioPerl sequence object.

A problem might arise if your object has the same methods as the methods but use them differently - your `$obj->id()` might mean provide the raw memory location of the object, whereas the documentation for `$obj->id()` says it should return the human-readable name. If so you need to look into providing an 'Adaptor' class, as suggested in the [Design Patterns book](https://en.wikipedia.org/wiki/Design_Patterns).

Interface classes really come into their own when we start leaving Perl and enter extensions wrapped over C or over databases, or through systems like [CORBA](https://en.wikipedia.org/wiki/Corba) to other languages, like [Java](https://en.wikipedia.org/wiki/Java_programming_language)/[Python](https://en.wikipedia.org/wiki/Python_programming_language) etc. Here the "object" is often a very thin wrapper over a interface, or an XS interface, and how it stores the object is really different. By providing a very clear, implementation free interface with good documentation there is a very clear target to hit.

Some people might complain that we are doing something very "un-perl-like" by providing these separate interface files. They are 90% documentation, and could be provided anywhere, in many ways they could be merged with the actual implementation classes and just made clear that if someone wants to mimic a class they should override the following methods.

Implementation functions in Interface files
-------------------------------------------

One of the issues we discovered early on in using Interface files was that there were methods that we would like to provide for classes which were independent of their implementation. A good example is a "Range" interface, which might define the following methods

```perl
$obj->start()
$obj->end()
```

Now a client to the object might want to use a `$obj->length()` method, because it is much easier than retrieving the two attributes and subtracting them. However, the `length()` method is just a pain for someone providing the implementation to provide - once `start()` and `end()` is defined, length is. There seems to be a [Catch-22](https://en.wikipedia.org/wiki/Catch_22) here: to make an object definition good for a *client* one needs to have additional, helper methods "on top of" the interface, however to make life easier for the *object implementation* one wants to have the bare minimum of functions defined which the implementer has to provide.

In the Range interface this became more than annoyance, as a lot of the "smarts" of the Range system was that we wanted to have the ability to say

```perl
if( $range->intersection($someother_range) )
```

We wanted a generic RangeI interface that we could apply to many objects, with definitions required only for start, end and strand. However we wanted the `intersection`, and `union` methods to be on all ranges, without us having to reimplement this every time.

Our solution was to allow implementation into the RangeI interface file, but only when these implementations sat "on top" of the interface definition and therefore provided helper client operations. In a language like [Java](https://en.wikipedia.org/wiki/Java_programming_language), we would clearly have two classes, with a composition/delegation method:

```
MyPublicSomethingClass has-a MyInternalSomethingInterface
```

with

```
ADifferentImplemtation implements MyInternalSomethingInterface
```

However this is really heavy handed in [Perl](https://en.wikipedia.org/wiki/Perl) (and people were complaining about having different implementation/interface classes). We were quite happy about merging the implementation independent functions with the interface definition, and we have used this in other interfaces since then. The documentation has to be clear about what is going on, but we think in general it is.

A Note on Performance
---------------------

Since Object Oriented programming in Perl 5 is not as elegant as intentionally object oriented programming languages we incur some overhead when calling the chained new constructors. For most cases this is perfectly okay as the object creation is not a significant portion of many of the procedures. However in certain cases - reading in a large number of sequences with features requires the creation of many objects and can perform poorly. One can work around this by creating the hashes directly and NOT chaining the new calls. An example of this is implemented in the objects in the treatment of Location objects for features.

Notes on Accessor Methods
-------------------------

Questions about accessors come up quite frequently on the list, and offline in various discussions between Bioperl developers. What follows is a summary of these discussions.

The consensus is that bioperl should be consistent, and employ consistent styles throughout modules. It would be disastrous if there was a mixture of both explicit get-setters and a hodge-podge of different `AUTOLOAD` conventions.

Bioperl developers seem to be religiously divided over using `AUTOLOAD` for accessors. The majority of those that contribute most to Bioperl prefer explicit accessor methods, they feel that explicit method definitions means easier-to-understand code. However, `AUTOLOAD` appears to be used fairly frequently in [bioperl-run](http://github.com/bioperl/bioperl-run) modules.

Then there are those of us for whom the multitude of explicit getsetters (and accompanying POD docs) in Perl is the programming equivalent of fingernails scratching a blackboard, both anti-Perl and anti "every principle we hold dear in programming" such as high-level declarative compact code and data representations, accessor methods that type-check consistently, and eliminating repetition/redundancy. However, such delicate aesthetics are often a barrier to producing vast and enormously useful modules such as Bioperl.

References
----------

Erich Gamma, Richard Helm, Ralph Johnson and John Vlissides. *Design patterns : elements of reusable object-oriented software.* 1994. Addison-Wesley: Reading, Mass. Also see [Design Patterns book](https://en.wikipedia.org/wiki/Design_Patterns)
