<?php
#
#	print.php - view a record from Alma on a printable web page
#
#	(c)2017 Kathryn Lybarger. CC-BY-SA
#
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Grima: Print record</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style type="text/css">
      body { background-color: #e5dccb; }
	@media print { 
.dl-horizontal dt {
    float: left;
    width: 60px;
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.dl-horizontal dd {
    margin-left: 80px;
}

	} 
    </style>
  </head>
  <body>
    <div class="container">
<?php
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
