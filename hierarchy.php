<?php
#
#   hierarchy.php - view bib/mfhds/items from Alma in a hierarchy view
#
#   (c)2017 Kathryn Lybarger. CC-BY-SA
#
require_once("grima-setup.php");
readfile('top.html');
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

