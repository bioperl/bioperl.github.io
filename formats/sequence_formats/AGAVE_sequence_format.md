---
title: AGAVE sequence format
layout: default
---

Description
-----------

*AGAVE* - *A*rchitecture for *G*enomic *A*nnotation, *V*isualization and *E*xchange - is an XML format developed by "DoubleTwist" (now defunct of genomic sequences.

See <http://www.agavexml.org/>

Example
-------

    <bio_sequence seq_length="11727"        
       molecule_type="DNA" 
       organism_name="Homo sapiens" t
       axon_id="9606" 
       clone_id="RP11-17E16" 
       clone_library="RPCI-11 Human Male BAC" chromosome="8 (Fingerprint)">
        <db_id id="AC011652F7" version="AC011652F7.4" db_code="gb"/>
        <description>Homo sapiens clone RP11-17E16, WORKING DRAFT SEQUENCE,   
    10 unordered pieces.</description>
        <keyword>HTGS_PGD; HTG; HTGS_PHASE1; HTGS_DRAFT.</keyword>
        <sequence>
    caactctggtggtttggggctttggcatctaaactcttaggaaaaaggcacggtctcccttgacctttgtc
    ...
       </sequence>
       <xrefs>
          <xref>
             <db_id id="9606" db_code="taxon"/>
          </xref>
          <xref>
             <db_id id="AC011652" db_code="gb"/>
          </xref>
       </xrefs>
       <sequence_map label="GenBank Annotations">
          <annotations>
          ...
          </annotations>
       </sequence_map>
       <map_location map_type="radiation_hybrid" source="washu" units="cR" 
    chromosome="8">
          <map_position pos="498.92"/>
        </map_location>
        <map_location map_type="fingerprint" source="washu" units="kb" 
    chromosome="8">
           <map_position pos="8748">
              <db_id id="ctg17944" db_code="washu_ctg"/>
           </map_position>
        </map_location>
     </bio_sequence>
