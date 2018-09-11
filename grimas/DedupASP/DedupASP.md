# DeleteTree - delete a bib record and its inventory from Alma

This grima deletes a bib record and all of its inventory
(holdings, items, and portfolios) from Alma.

## Input
* MMS ID of bib record (root of the tree) to delete

## Output
This grima outputs a message indicating either:
* success - indicating the MMS ID of the bib (and inventory) deleted
* error - including the error message from Alma

## Procedure
I often use this as a skinny grima with Alma and OCLC. I search Alma for
the record (and inventory) I want to remove.


## API requirements
* Bibs - read/write
* Electronic - read/write (to delete electronic inventory)
