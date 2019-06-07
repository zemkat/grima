grima
=======

Welcome to Grima, the library that makes it easy to work with Alma
using APIs. Check out the [documentation](docs/) to learn how to
[set up grima](docs/SETUP.md) or try some of the
grimas below:

### Display or Print records
* [PrintBib](grimas/PrintBib/PrintBib.php) -- display bib record in printable web page
* [PrintHolding](grimas/PrintHolding/PrintHolding.php) -- display holding record in printable web page
* [Hierarchy](grimas/Hierarchy/Hierarchy.php) -- view bib/mfhds/items in hierarchy view
* [ResolveLink](grimas/ResolveLink/ResolveLink.php) -- resolve a link in Alma/Primo window
* [ShowItemsFromHoldings](grimas/ShowItemsFromHoldings/ShowItemsFromHoldings.php) -- display all items from a holding record

### Edit Records
* [InsertOclcNo](grimas/InsertOclcNo/InsertOclcNo.php) -- insert OCLC number into 035
* [Boundwith](grimas/Boundwith/Boundwith.php) -- create boundwith in Alma using bib 501/774, holding 014
* [RemoveTempLocation](grimas/RemoveTempLocation/RemoveTempLocation.php) -- remove temporary location from item
* [AddInternalNote](grimas/AddInternalNote/AddInternalNote.php) -- add internal note 1 to an item record
* [MarkImportTemporaryLocation](grimas/MarkImportTemporaryLocation/MarkImportTemporaryLocation.php) -- mark items from an import job as being in a temporary location
* [AppendToNoteOnSet](grimas/AppendToNoteOnSet/AppendToNoteOnSet.php) -- add a note to every item in a set, appending if there is already a note there

### Add New Records
* [CreateBriefBib](grimas/CreateBriefBib/CreateBriefBib.php) -- create a brief bib with specified 245a
* [DuplicateBib](grimas/DuplicateBib/DuplicateBib.php) -- create a duplicate copy of a bib
* [MoreItems](grimas/MoreItems/MoreItems.php) -- add more items to a serial or set, based on the first one

### Delete Records
* [DeleteTree](grimas/DeleteTree/DeleteTree.php) -- delete bib and all of its inventory from Alma
* [DeleteBib](grimas/DeleteBib/DeleteBib.php) -- delete bib from Alma
* [DeleteItem](grimas/DeleteItem/DeleteItem.php) -- delete item from Alma
* [DeletePortfolio](grimas/DeletePortfolio/DeletePortfolio.php) -- delete portfolio from Alma

### View XML for Development or Debugging
* [ViewXmlBib](grimas/ViewXmlBib/ViewXmlBib.php) -- view Bib record as Bib object XML
* [ViewXmlHolding](grimas/ViewXmlHolding/ViewXmlHolding.php) -- view Holding record as Holding object XML
* [ViewXmlItem](grimas/ViewXmlItem/ViewXmlItem.php) -- view Item record as Item object XML
* [ViewXmlPortfolio](grimas/ViewXmlPortfolio/ViewXmlPortfolio.php) -- view Portfolio record as Electronic Portfolio object XML
