Bug submission
--------------

BioPerl has switched (as of mid-April, 2014) to GitHub Issues. We still have
access to the [Redmine](http://redmine.org)-based tracking system, but this will
be effectively read-only. We no longer support access to the original Bugzilla
instance.

![Don't make me bug you!](Ant.jpg "fig:Don't make me bug you!") Please submit
bugs or enhancement requests to [BioPerl GitHub Issues](https://github.com/bioperl/bioperl-live/issues).
The older [BioPerl Redmine tracking system](https://redmine.open-bio.org/projects/bioperl) remains
but is will no longer be used, and the oldest [Bugzilla-based system](http://bugzilla.open-bio.org)
is not supported and addition of new bugs has been disabled.

We really do want you to submit bugs, even though it means more things for us to
do! It means there is something we didn't think of or test in the particular
module and we won't know about this unless you tell us. The Redmine system
requires you register to avoid spam and to allow us to contact you again when
the bug is fixed or to clarify the problem and solutions.

It is important that you record the version of BioPerl you are running (if you
don't know, see the
[FAQ](FAQ#How_can_I_tell_what_version_of_BioPerl_is_installed.3F "wikilink") the
question is addressed there). You can also include the version of Perl you are
using and the Operating System you are running.

[Simon Tatham](http://www.chiark.greenend.org.uk/~sgtatham/) has a great
resource on how to effectively [report bugs](http://www.chiark.greenend.org.uk/~sgtatham/bugs.html).

Submitting Bugs
---------------

When submitting new bugs on [BioPerl on GitHub](http://github.com/bioperl),
enter a brief description and other general information. You can use
[Markdown](https://daringfireball.net/projects/markdown/) to add links, syntax
highlighting, and so on; see the [GitHub Markdown docs](https://help.github.com/articles/github-flavored-markdown) for more.

You can paste example code in the description, but we suggest submitting as a
[GitHub Gist](https://gist.github.com). If you have example fixes, we highly
suggest using the tools GitHub has in place, namely the ability to [fork the code](https://help.github.com/articles/fork-a-repo)
and [create a pull request](https://help.github.com/articles/creating-a-pull-request) with the
relevant fix. This will show up as an issue automatically, so there isn't a need
to file one separately.

Note that attachments on GitHub issues only work for images. If the example is a
text file then use a [GitHub Gist](https://gist.github.com); alternatively, if
the file is something available publicly then please provide a link to the file.

Submitting Patches
------------------

We gladly welcome patches. Patches for bioperl code should be created as
described in the . Try to ensure the patch is derived against the latest code
checked out from [Git](Using_Git "wikilink"), particularly if the patch is
large.

Briefly, you can generate the patch using the following command:

`diff -u old new`

For best results, follow this example:

`cd $YOUR_WORKING_DIST_DIRECTORY/Bio/Frobnicator`
`git pull`
`git diff GrokFrobnicator.pm > my-patch.dif`

We also accept patches as an issue on GitHub; submit the patch as a [GitHub
Gist](https://gist.github.com). Even better, submit a pull request on GitHub.
It's also worth discussing these on the [mailing list](http://lists.open-bio.org/pipermail/bioperl-l).

Submitting New Modules and Code Snippets
----------------------------------------

We also accept new code, either as full-fledged modules or as snippets of code
(snippets work better as a patch). New code must include documentation, example
code (typically listed in a SYNOPSIS section), and tests with decent test
coverage following our testing standards in our ). Because we are moving to a
more modular scheme for future Bioperl installations we highly suggest
individual submission of modules to [CPAN](http://search.cpan.org), primarily to
help lower the barrier to submitting bug fixes.

Etiquette
---------

Good bug reports are ones which provide a small amount of code and the necessary
test files to reproduce your bug. By doing this work up front you insure the
developer spends most of his or her time actually working on the problem.
Pasting your entire 600 line program into the comment buffer is probably not
going to get an enthusiastic response. In addition, isolating the problem down
to a small amount of your code will help ensure that the bug is not on your end
before we dive in and start working on it.

Open Issues (GitHub)
--------------------

This list details the open Bugs on [GitHub](https://github.com/bioperl/bioperl-live/issues) for BioPerl.
