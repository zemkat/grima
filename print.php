<?php
#
#	print.php - view a record from Alma on a printable web page
#
#	(c)2017 Kathryn Lybarger. CC-BY-SA
#
require_once("grima-setup.php");
readfile('top.html');
require_once("grima-lib.php");

if (!($mms_id = $_REQUEST['mms_id'])) {
	print_form(); exit;
}
if (!(Bib::valid_MMSID($mms_id))) {
	print_form($mms_id); exit;
}

function print_form($mms_id = null) {
	print "      <div class='panel panel-default bib'>\n";
	print "        <div class='panel-heading'>\n";
	print "          <h1 class='panel-title'>Grima: Print Record</h1>\n";
	print "        </div>\n";
	print "        <div class='panel-body'>\n";
	print "          <dl class='dl-horizontal'>\n";
	print "            <form method=\"post\" action=\"print.php\">\n";
	print "              <p>Print record \n";
	print "                <input name=\"mms_id\" size=\"20\"";
	if ($mms_id) {
		print "value=\"$mms_id\"";
	}
	print "/> into ";
	print "<input type=\"submit\" value=\"submit\" />\n";
	print "              </p>\n";
	print "            </form>\n";
	print "           </dl>\n";
	print "         </div>\n";
	print "      </div>\n";
	print "    </div>\n";
	print "  </body>\n";
	print "</html>\n";
}

$bib = new Bib($mms_id);

$xpath = new DomXpath($bib->xml);

	print "      <div class='panel panel-default bib'>\n";
	print "        <div class='panel-heading'>\n";
	print "          <h1 class='panel-title'>Alma #" . $bib->mms_id . ": " . 
		$bib->get_title();
	print "</h1>
        </div>
        <div class='panel-body'>
          <dl class='dl-horizontal'>
";

$fields = $xpath->query("//record/controlfield");
foreach ($fields as $field) {
	print "            <dt>" . $field->getAttribute("tag") . "</dt><dd>" . $field->nodeValue . "</dd>\n";
}

$fields = $xpath->query("//record/datafield");
foreach ($fields as $field) {
	$ind1 = $field->getAttribute("ind1");
	if ($ind1 == " ") { $ind1 = "_"; }
	$ind2 = $field->getAttribute("ind1");
	if ($ind2 == " ") { $ind2 = "_"; }
	print "            <dt>" . $field->getAttribute("tag") . "</dt><dd>" . $ind1 . $ind2;
	foreach ($field->childNodes as $subfield) {
		print " ǂ" . $subfield->getAttribute("code") . " " .
		$subfield->nodeValue;
	}
	print "</dd>\n";
}
print "          </dl>\n";
print "        </div>\n"; 
print "      </div>\n"; 
print "    </div>\n"; 
print "  </body>\n"; 
print "</html>\n"; 
