# CreateBriefBib - create a brief bib in Alma

This grima creates a very brief MARC record, with only the
245$a you specify. 

Good for certain types of projects.

Also for testing that grima writes are working properly on
your system.

## Input
* The title proper of the MARC record you want to create

## Output
This grima outputs a message indicating either:
* success - including the MMS ID of the newly created bib record
* error - including the error message from Alma

## API requirements
* Bibs - read/write
