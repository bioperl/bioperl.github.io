---
title: InterPro sequence format
layout: default
---

Description
-----------

This is a small fragment of a far larger InterPro XML file. More detail can be found at the [InterPro home page](http://www.ebi.ac.uk/interpro/).

Example
-------
```
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE interprodb SYSTEM "interpro.dtd">
<interprodb>
<release>
   <dbinfo dbname="INTERPRO" version="5.1" entry_count="5630" file_date="12-JUL-2002 00:00:00"/>
   <dbinfo dbname="SWISS" version="40.22" entry_count="110823" file_date="24-JUN-2002 00:00:00"/>
   <dbinfo dbname="TREMBL" version="21.2" entry_count="671586" file_date="05-JUL-2002 00:00:00"/>
   <dbinfo dbname="PRINTS" version="33.0" entry_count="1650" file_date="24-JAN-2002 00:00:00"/>
   <dbinfo dbname="PREFILE" version="N/A" entry_count="252" file_date="18-JUL-2001 00:00:00"/>
   <dbinfo dbname="PROSITE" version="17.5" entry_count="1565" file_date="21-JUN-2002 00:00:00"/>
   <dbinfo dbname="PFAM" version="7.3" entry_count="3865" file_date="17-MAY-2002 00:00:00"/>
   <dbinfo dbname="PRODOM" version="2001.3" entry_count="1346" file_date="28-JAN-2002 00:00:00"/>
   <dbinfo dbname="SMART" version="3.1" entry_count="509" file_date="16-NOV-2000 00:00:00"/>
   <dbinfo dbname="TIGRFAMs" version="1.2" entry_count="814" file_date="03-AUG-2001 00:00:00"/>
 </release>
 <interpro id="IPR000001" type="Domain" short_name="Kringle" protein_count="129">
   <name>`Kringle`</name>
   <abstract>
Kringles are autonomous structural domains, found throughout the blood 
              clotting and fibrinolytic proteins.
Kringle domains are believed to play a role in binding mediators (e.g., membranes,
other proteins or phospholipids), and in the regulation of proteolytic activity
Kringle domains 
</abstract>
<example_list>
<example>
<db_xref dbkey="P00748" db="SWISS"/>Blood coagulation factor XII (Hageman factor) (1 copy)
</example>
<example>
<db_xref dbkey="P00749" db="SWISS"/>Urokinase-type plasminogen activator (1 copy)
</example>
<example>
<db_xref dbkey="Q08048" db="SWISS"/>Hepatocyte growth factor (HGF) (4 copies)
</example>
<example>
<db_xref dbkey="Q04756" db="SWISS"/>Hepatocyte growth factor activator <cite idref="PUB00003400"/>

.....
```
