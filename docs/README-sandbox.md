# Grima: Sandbox/playground
by [Zemkat](https://works.bepress.com/kathrynlybarger/)

This allows you to try out a few grimas on read-only, fake data.

## Printable versions of records

* [PrintBib](grimas/PrintBib/PrintBib.php?mms_id=991129830000541) 
It's hard to print a bib record in Alma. Getting the font size right, reducing
clutter, etc. Usually my staff just need a quick print-out to do some manual
process, and it is worth exactly 1 second to send it to the printer. Luckily,
this grima is that fast :-)
* [Hierarchy](grimas/Hierarchy/Hierarchy.php?mms_id=991129830000541) Similar,
but prints holdings and items too.

There are also:
* [PrintHolding](grimas/PrintHolding/PrintHolding.php?mms_id=991129830000541&holding_id=224687740000541)
* [PrintHoldingFromBib](grimas/PrintHoldingFromBib/PrintHoldingFromBib.php) XXX
* [PrintItem](grimas/PrintItem/PrintItem.php?mms_id=991129830000541&holding_id=224687740000541&item_pid=234687730000541)

## XML Debug grimas

See the raw xml Alma sends:

* [ViewXmlBib](grimas/ViewXmlBib/ViewXmlBib.php?mms_id=991129830000541)
* [ViewXmlHolding](grimas/ViewXmlHolding/ViewXmlHolding.php?holding_id=224687740000541)
* [ViewXmlItem](grimas/ViewXmlItem/ViewXmlItem.php?item_pid=234687730000541)
* [ViewXmlPortfolio](grimas/ViewXmlPortfolio/ViewXmlPortfolio.php?portfolio_id=XXX)

## Local grimas

These are unlikely to be useful to you, but they the kinds of things grima
can do with only read-only access. Most grimas are written in order to fix,
update, or finish some task, so usually require read-write access.

* [MetsForExploreUK](grimas/MetsForExploreUK/MetsForExploreUK.php)
* [ShowItemsFromHoldings](grimas/ShowItemsFromHoldings/ShowItemsFromHoldings.php)
* [ShowItemsFromHoldingsB](grimas/ShowItemsFromHoldingsB/ShowItemsFromHoldingsB.php)

## Want to work with your own institutions catalog?

The data is provided by Ex Libris, and is read-only, so you'll probably prefer
to [get an API key](docs/APIKEY.md) and check out the [public grima
server](https://public-grima.zemkat.org). This will allow you to run more
grimas, and on your own data.

