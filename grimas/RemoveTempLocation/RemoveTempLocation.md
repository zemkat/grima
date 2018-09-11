# RemoveTempLocation - remove temporary location from item record

This grima sets an item record to not be in a temporary
location. 

Note that if the item had a temporary location set, that will
still appear in the item record -- the item will just no longer
be in that temporary location.

## Input
* Barcode of item record to have temporary location status removed

## Output
This grima outputs a message indicating either:
* success - including the MMS ID of the new copy of the bib record
* error - including the error message from Alma

## API requirements
* Bibs - read/write
