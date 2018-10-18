# AddAltCall - add an alternate call number to an item record
*Thanks to Kathryn Lybarger for all help with this grima!*

This grima sets Alternate Call Number to the specified note. If no
note is entered into the form, any existing Alt Call Number will be cleared.

## Input
* Alternate Call Number string to add to item record
* Barcode of item record to have internal note added

## Output
This grima outputs a message indicating either:
* success - including the MMS ID of the new copy of the bib record
* error - including the error message from Alma

## API requirements
* Bibs - read/write
