# What's New in Grima

## Public Grima Server

If you are just running the standard grimas and don't need to modify
or add any, you can use Grima on your own library's data from the new
public server. You will still need an API key to work with your own data.

While you will have to enter your API key, it is not stored on the
public server. It is stored encrypted in a cookie on your local computer,
so you should only have to enter it the first time you login to Grima.

Just visit https://public.grima.zemkat.org/ to get started!

## Grima Data Store

If your grimas need to store a little bit of data, they can now do
so using the Grima Data Store. This is a suppressed bib / holding / item
chain in your Alma catalog where grimas can store key-value pairs.

For more information, see [DATASTORE.md](Grima Data Store).

## Run a functions on set elements

Grima's Set object now has a runOnElements method which allows you
to define a new function in a grima and then run that function
on all of the elements of the Set. If you are processing sets with
a grima and want to do the same process to each element of the set,
using this method will be more reliable (and less memory-intensive)
than gathering all of the objects in the set and looping through them,
especially for large sets.

For more information, see the
[../grimas/FunctionSetTest.md](Function Set Test grima).

## New Grimas:
* Recycle, RecycleBinEmpty, RecycleBinClean
* PortfolioNoteFrom856
* PortfolioUrlUpdate
* BatchAddItems
