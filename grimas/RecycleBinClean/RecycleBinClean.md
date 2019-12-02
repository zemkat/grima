# Recycle, RecycleBinEmpty, RecycleBinClean - delete records that are annoying to delete

The Recycle grima puts annoying-to-delete records into a set for later deletion
(all at once). This might be useful for:
* Community Zone bibs with no inventory
* Records that ask if you are sure
* Records that ask how you want various things handled

The RecycleBinEmpty grima schedules an Alma job to delete any records
in the recycle bin set. You will probably not receive a job completion
email, but you can check on the status of the job using Monitor Jobs in
Alma. This can be run a few times in a day, though there is a limit to
how often Alma is willing to run a given job.

The RecycleBinClean grima is a maintenance grima, which removes all
records from the recycle bin set (they do not get disassociated from
the set after they are deleted, which can lead to confusing messages).
This should be run periodically after emptying the recycle bin (weekly
or monthly is probably fine, though nothing bad will happen if you do
it more often).

## Setup

The first time you run the Recycle grima, it will create an itemized
set called GRIMA RECYCLE BIN which will contain any recycled records.

The set identifier for the recycle bin set is stored in the Grima Data
Store under the key 'recycle bin'.

## Input
* (no input)

## Output
The RecycleBinClean grima outputs a message indicating either:
* success - indicating that the records were all removed from the set
* error - including the error message from Alma

## API requirements
* Configuration - read/write
