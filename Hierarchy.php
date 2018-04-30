<?php

require_once("grima-lib.php");

class Hierarchy extends GrimaTask {

	function do_task() {
		$this->bib = new Bib();
		$this->bib->loadFromAlma($this['mms_id']);
		$this->bib->getHoldingsList();

		foreach ($this->bib->holdingsList->holdings as $holding) {
			$holding->getItemList();
		}
	}

	function print_success() {

        print <<<TEMPLATE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Grima by Zemkat</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style type="text/css">
      body { min-height: 100vh;  padding: 0; margin: 0; }
      .jumbotron{ min-height: 100vh; margin: 0; }
    </style>
  </head>
    <body>
      <div class="container">
        <div class='panel panel-default bib'>
          <div class='panel-heading'>
TEMPLATE;
$bib = $this->bib;
print "
            <h1 class='panel-title'>Bib #${bib['mms_id']}</h1>
          </div>
          <div class='panel-body'>
            <dl class='dl-horizontal'>
              <dt>Title:</dt><dd>${bib['title']}</dd>
              <dt>Author:</dt><dd>${bib['author']}</dd>
              <dt>Network numbers:</dt>
                <dd>
";
#	foreach ($netnums as $netnum) {
#		print "                  <span class='network number'>$netnum</span>\n";
#	}
print "
                </dd>
              <dt>Place of Publication:</dt><dd>${bib['place_of_publication']}</dd>
              <dt>Publisher:</dt><dd>${bib['publisher']}</dd>
            </dl>";

	foreach ($bib->holdingsList->holdings as $holding) {
	print "
            <div class='panel panel-default holding'>
              <div class='panel-heading'>
                <h2 class='panel-title'>Mfhd #${holding['holding_id']}</h2>
              </div>
              <div class='panel-body'>
                <dl class='dl-horizontal'>
                  <dt>Location:</dt>
                    <dd>${holding['library_code']} <b>:</b> ${holding['library']}</dd>
                  <dt>Call number:</dt><dd>${holding['call_number']}</dd>
                </dl>";

		foreach ($holding->itemList->items as $item) {
print "
                <div class='panel panel-default'>
                  <div class='panel-heading'>
                    <h2 class='panel-title'>Item #${item['item_pid']}</h2>
                  </div>
                  <div class='panel-body'>
                    <dl class='dl-horizontal'>
                      <dt>Barcode:</dt><dd>${item['barcode']}</dd>
                      <dt>Description:</dt><dd>${item['description']}</dd>
                    </dl>
                  </div>
                </div>
";
		}
print "              </div>
            </div>
";
	}
print "         </div>
       </div>
    </div>
  </body>
</html>
";
	}
}

Hierarchy::RunIt();
