---
title: Nexus tree format
layout: default
---

Description
-----------

The NEXUS file format nexus97 was layed down in 1997 by Maddison, Swofford and Maddison. A definition can be seen at a page on the [Workshop of Molecular Evolution](http://workshop.molecularevolution.org/resources/fileformats/tree_formats.php) website.

Examples
--------

### Integer labels with score

    #NEXUS
    begin trees;
    tree 'name' =(1:0.212481,8:0.297838,(9:0.222729,((6:0.201563,7:0.194547):0.282035,(4:1.146091,(3:1.008881,
    (10:0.384105,(2:0.235682,5:0.353432):0.323680):0.103875):0.413540):0.254687):0.095341):0.079254):0.000000;
    end;

### Case labels

    #NEXUS
    begin trees;
    tree=(((Hippo,((Horse,(Monkey,Orangutan)),(Donkey,Mule))),((((Zebra,Camel),(Llama,Bison)),Elephant),((Buffalo,
    (Sheep,Fox)),((Pig,GuineaPig),Cat)))),((((Dog,Rat),((Deer,Reindeer),Whale)),Rabbit),(Dolphin,Seal)))
    end;

References
----------

http://www.ncbi.nlm.nih.gov/pubmed/11975335

