# Combine Holdings - delete a bib record from Alma

This grima combines the contents of the specified holdings records
from one bib onto one holding record.

## Input
* Holdings record to keep
* Holdings records to add (or 'ALL' to combine in all from this bib)

## Output
This grima outputs a message indicating either:
* success - indicating the MMS ID of the record deleted
* error - including the error message from Alma

## API requirements
* Bibs - read/write
* Analytics - read-only
