<?php
#
#   insert-oclcnum.php - replace OCLC number in a bib in Alma
#
#   (c)2017 Kathryn Lybarger. CC-BY-SA
#
require_once("grima-setup.php");
readfile('top.html');
require_once("grima-lib.php");
echo "      <div class='panel panel-default'>";

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
