<?php
#
#   hierarchy.php - view bib/mfhds/items from Alma in a hierarchy view
#
#   (c)2017 Kathryn Lybarger. CC-BY-SA
#
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Hierarchy</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style type="text/css">
      body { background-color: #e5dccb; }
      network_number { margin-right: 3ex; }
    </style>
    </head>
<?php

require_once("grima-lib.php");

if (isset($_REQUEST['mms_id'])) {
	$mms_id = $_REQUEST['mms_id']; #clean
} else {
	$mms_id = $argv[1];
}

$bib = new Bib($mms_id);

$title = $bib->get_title();
$author = $bib->get_author();
$place = $bib->get_placeOfPublication();
$publisher = $bib->get_publisher();
$netnums = $bib->get_networkNumbers();

?>
    <body>
      <div class="container">
        <div class='panel panel-default bib'>
          <div class='panel-heading'>
            <h1 class='panel-title'>Bib #<?php print $mms_id; ?></h1>
          </div>
          <div class='panel-body'>
            <dl class='dl-horizontal'>
              <dt>Title:</dt><dd><?php print $title; ?></dd>
              <dt>Author:</dt><dd><?php print $author; ?></dd>
              <dt>Network numbers:</dt>
                <dd>
<?php
	foreach ($netnums as $netnum) {
		print "                  <span class='network number'>$netnum</span>\n";
	}
?>
                </dd>
              <dt>Place of Publication:</dt><dd><?php print $place; ?></dd>
              <dt>Publisher:</dt><dd><?php print $publisher; ?></dd>
            </dl>
<?php

$bib->load_holdingsList();
foreach ($bib->holdings as $holding) {
	#$holding->fluff();
?>
            <div class='panel panel-default holding'>
              <div class='panel-heading'>
                <h2 class='panel-title'>Mfhd #<?php print $holding->holding_id; ?></h2>
              </div>
              <div class='panel-body'>
                <dl class='dl-horizontal'>
                  <dt>Location:</dt>
                    <dd><?php print $holding->library_code; ?> <b>:</b> <?php print $holding->location_code; ?> </dd>
                  <dt>Call number:</dt><dd><?php print $holding->call_number; ?></dd>
                </dl>
<?php
	$holding->load_itemList();
	foreach ($holding->items as $item) {
?>
		<div class='panel panel-default'>
                  <div class='panel-heading'>
                    <h2 class='panel-title'>Item #<?php print $item->item_pid;?></h2>
                  </div>
                  <div class='panel-body'>
                    <dl class='dl-horizontal'>
                      <dt>Barcode:</dt><dd><?php print $item->barcode; ?></dd>
                      <dt>Description:</dt><dd><?php print $item->description; ?> </dd>
                    </dl>
                  </div>
                </div>
<?php
	}
?>
              </div>
            </div>
<?php
}
?>
         </div>
       </div>
    </div>
  </body>
</html>

