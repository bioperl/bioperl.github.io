---
title: Newick tree format
layout: default
---

Description
-----------

In mathematics, *Newick tree format* (or *Newick notation* or *New Hampshire tree format*) is a way to represent graph-theoretical trees with edge lengths using parentheses and commas. It was created by James Archie, William H. E. Day, Joseph Felsenstein, Wayne Maddison, Christopher Meacham, F. James Rohlf, and David Swofford, at two meetings in 1986, the second of which was at Newick's restaurant in Dover, New Hampshire, USA.

Examples of Newick tree format
------------------------------

```
(,,(,));                               ''no nodes are named''
(A,B,(C,D));                           ''leaf nodes are named''
(A,B,(C,D)E)F;                         ''all nodes are named''
(:0.1,:0.2,(:0.3,:0.4):0.5);           ''all but root node have a distance to parent''
(A:0.1,B:0.2,(C:0.3,D:0.4):0.5);       ''distances and leaf names'' '''(popular)'''
(A:0.1,B:0.2,(C:0.3,D:0.4)E:0.5)F;     ''distances and all names''
A;                                     ''a (degenerate) tree with one named node''
((B:0.2,(C:0.3,D:0.4)E:0.5)F:0.1)A;    ''a tree rooted on a leaf node'' '''(rare)'''

```

Newick format is typically used for tools like PHYLIP and is a minimal definition for a phylogenetic tree.

Rooted, unrooted, and binary trees
----------------------------------

When an ''unrooted'' tree is represented in Newick notation, an arbitrary node is chosen as its root. Whether rooted or unrooted, typically a tree's representation is rooted on an internal node and it is rare (but legal) to root a tree on a leaf node.

A ''rooted binary'' tree that is rooted on an internal node has exactly two immediate descendant nodes for each internal node. An ''unrooted binary'' tree that is rooted on an arbitrary internal node has exactly three immediate descendant nodes for the root node, and each other internal node has exactly two immediate descendant nodes. A ''binary tree rooted from a leaf'' has at most one immediate descendant node for the root node, and each internal node has exactly two immediate descendant nodes.

Grammar
-------

A grammar for parsing the Newick format:

### The grammar nodes

* Tree: The full input Newick Format for a single tree
* Subtree: an internal node (and its descendants) or a leaf node
* Leaf: a leaf node
* Internal: an internal node (and its descendants)
* BranchSet: a set of one or more Branches
* Branch: a tree edge and its descendant subtree.
* Name: the name of a node
* Length: the length of a tree edge.
 
### The grammar rules

Note, *|** separates alternatives.

* Tree --> Subtree ";"
* Subtree --> Leaf | Internal
* Leaf --> Name
* Internal --> "(" BranchSet ")" Name
* BranchSet --> Branch | Branch "," BranchSet
* Branch --> Subtree Length
* Name --> empty | string
* Length --> empty | ":" number

Whitespace (spaces, tabs, carriage returns, and linefeeds) within ''number'' is prohibited. Whitespace within ''string'' is often prohibited. Whitespace elsewhere is ignored.

See also
--------

-  [Gary Olsen's Interpretation of the "Newick's 8:45" Tree Format Standard](http://evolution.genetics.washington.edu/phylip/newick_doc.html)'


