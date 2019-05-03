# MarkImportTemporaryLocation - mark all items in the recent import as being in a temporary location

*Inspired by Alma Enhancement Request #5886*

Import profiles in Alma currently don't have a way to set all imported 
items as being in a temporary location.

This Grima creates a Set in Alma of all titles affected by the import
job whose ID you specify. If they only have one holding and one item, 
that item will be marked as being in the specified temporary library
and location. If any titles have additional holdings or items, they
will be unaffected, and you will receive a warning including their IDs.
The temporary set will be deleted.

## Input
* Job ID of import job 
* Temporary Library
* Temporary Location

## Output
This grima outputs a message indicating either:
* success - including the Job ID of the import job, plus any warnings
* error - including the error message from Alma

## API requirements
* Bibs - read/write
* Configuration - read/write
