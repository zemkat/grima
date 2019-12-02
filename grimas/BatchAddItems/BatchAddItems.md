# BatchAddItems - add multiple items to an alma holding with provided barcodes

This grima adds a batch of new item records to a holding, differing only
by barcode.

## Input
* Item PID or barcode of bib record to use as a template
* List of barcodes to use for new items

## Output
This grima outputs a message indicating either:
* success - indicating that all items have been created
* error - including the error message from Alma

## API requirements
* Bibs - read/write
