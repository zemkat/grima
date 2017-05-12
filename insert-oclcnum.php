<?php
#
#   insert-oclcnum.php - replace OCLC number in a bib in Alma
#
#   (c)2017 Kathryn Lybarger. CC-BY-SA
#
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="UTF-8"/>
    <title>Alma</title>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.0.min.js"                    integrity="sha384-nrOSfDHtoPMzJHjVTdCopGqIqeYETSXhZDFyniQ8ZHcVy08QesyHcnOUpMpqnmWq" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"/>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"                   integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style type="text/css">
	.jumbotron{ min-height: 100vh; }
	img.fullwidth { max-width: 100%; }
    body { background-color: #e5dccb; }
</style>
  </head>
  <body>
    <div class="container">
      <div class="panel panel-default">

<?php
require_once("grima-lib.php");

function print_form($oclcnum,$mms_id) {
	print "        <div class=\"panel-heading\">\n";
	print "          <h1 class=\"panel-title\">\n";
	print "            <span class=\"titleproper\">Grima: Insert OCLC Number</span>\n";
	print "          </h1>\n";
	print "        </div>\n";
	print "        <div class=\"panel-body\">\n";
	print "          <form method=\"post\" action=\"insert-oclcnum.php\">";
	print "            <p>Insert oclcnum <input name=\"oclcnum\" size=\"15\"";
	if ($oclcnum) { 
		print "value=\"$oclcnum\""; 
	}
	print "/> into
	       <input name=\"mms_id\" size=\"20\""; 
	if ($mms_id) { 
    	print "value=\"$mms_id\""; 
	}
	print "/>
          <input type=\"submit\" value=\"submit\" />
        </form>
      </div>
    </div>
  </body>
</html>";
}

# check format of request
if (isset($_REQUEST['query']) && preg_match("/(\d+) into (\d+)/",$_REQUEST['query'],$m)) {
	$oclcnum = $m[1];
	$mms_id = $m[2];
	if (!preg_match("/^\d+$/",$mms_id) || (strlen($mms_id) != 16)) {
		print_form($oclcnum,$mms_id);
	}
} else {
	$mms_id = isset($_REQUEST['mms_id'])?$_REQUEST['mms_id']:null;
	$oclcnum = isset($_REQUEST['oclcnum'])?$_REQUEST['oclcnum']:null;
	if (
		(!$mms_id) ||
		(!$oclcnum) ||
		!preg_match("/^\d+$/",$mms_id) ||
		(strlen($mms_id) != 16) 
	) {
		print_form($oclcnum,$mms_id);
	}
}

$bib = new Bib($mms_id);
$xpath = new DomXpath($bib->xml);

$bib->deleteField("019");
$bib->deleteField("035");

$new_oclcnum = "(OCoLC)$oclcnum";
$bib->appendField("035","","",array('a' => "(OCoLC)$oclcnum"));
#$bib->updateAlma();

?>
        <div class="panel-heading">
          <h1 class="panel-title">
            <span class="titleproper">Grima: Insert OCLC Number</span>
          </h1>
        </div>
        <div class="panel-body">
          <p>Process complete! <a href="http://bit.ly/UK-ALMA">return to alma</a></p>
        </div>
      </div>
    </div>
  </body>
</html>
