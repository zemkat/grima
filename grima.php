<?php
#
#       grima.php - pass messages to grima minions, display usage
#
#       (c) 2017 Kathryn Lybarger. CC-BY-SA
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
    </style>
  </head>
  <body>
    <div class="container">

<?php

require_once("grima-lib.php");

function do_redirect($url) {
        if(!headers_sent()) header("Location: $url");
        echo "<head><title>Redirect</title><meta http-equiv=refresh content='1; url=$url'></head>\n";
        echo "<body><p><a href='$url'>Go here</a>\n";
}

$msg = $_REQUEST["msg"];

#
#	print <mms_id>
#
if (preg_match("/^print( .*)/",$msg,$m)) {
	do_redirect("print.php?mms_id=" . ltrim($m[1]));
	exit;
}

#
#	filter <mms_id> with <filter>
#
if (preg_match("/(norm|normalize|filter) (\d+) (with (.*))/",$msg,$m)) {
	if (is_mmsid($m[2])) {
		do_redirect("zf.php?mms_id=" . $m[2] . "&filter=" . $m[4]);
	} else {
		# MMS ID form
	}
	exit;
}

#
#	add mfhd <template> to <mms_id>
#
#
if (preg_match("/^add mfhd(.*)/",$msg,$m)) {
	do_redirect("add_mfhd.php?query=" . ltrim($m[1]));
	exit;
}

#
#	add tree <template> to <mms_id> with barcode <barcode>
#
#

#
#	insert oclcnum <number> into <mms_id>
#
if (preg_match("/insert oclcnum(.*)/",$msg,$m)) {
	do_redirect("insert-oclcnum.php?query=" . urlencode(ltrim($m[1])));
	exit;
}

#
#	overlay <mms_id> with <oclcnum>
#	maybe more direct, but needs both APIs
#

#
#	unmerge phys/electronic
#

#
#	combine multiple bib/mfhd/item chains
#

# FORMS
# PRINT
# add this to object?

print "      <div class='panel panel-default bib'>\n";
print "        <div class='panel-heading'>\n";
print "          <h1 class='panel-title'>Print Record</h1>\n";
print "        </div>\n";
print "        <div class='panel-body'>\n";
print "          <dl class='dl-horizontal'>\n";
print "            <form method=\"post\" action=\"print.php\">\n";
print "              <p>Print record \n";
print "                <input name=\"mms_id\" size=\"20\"/>";
print "<input type=\"submit\" value=\"submit\" />\n";
print "              </p>\n";
print "            </form>\n";
print "           </dl>\n";
print "         </div>\n";
print "      </div>\n";
print "\n";

# HIERARCHY

print "      <div class='panel panel-default bib'>\n";
print "        <div class='panel-heading'>\n";
print "          <h1 class='panel-title'>View Hierarchy</h1>\n";
print "        </div>\n";
print "        <div class='panel-body'>\n";
print "          <dl class='dl-horizontal'>\n";
print "            <form method=\"post\" action=\"hierarchy.php\">\n";
print "              <p>View \n";
print "               <input name=\"mms_id\" size=\"20\"/>\n";
print "                       <input type=\"submit\" value=\"submit\" />\n";
print "              </p>\n";
print "            </form>\n";
print "          </dl>\n";
print "        </div>\n";
print "      </div>\n";

print "\n";
# INSERT

print "      <div class='panel panel-default bib'>\n";
print "        <div class=\"panel-heading\">\n";
print "          <h1 class=\"panel-title\">\n";
print "            <span class=\"titleproper\">Insert OCLC Number</span>\n";
print "          </h1>\n";
print "        </div>\n";
print "        <div class=\"panel-body\">\n";
print "          <dl class='dl-horizontal'>\n";
print "            <form method=\"post\" action=\"insert-oclcnum.php\">\n";
print "              <p>Insert oclcnum <input name=\"oclcnum\" size=\"15\" /> into\n";
print "                <input name=\"mms_id\" size=\"20\"/>
                <input type=\"submit\" value=\"submit\" />
              </p>
            </form>
          </dl>
        </div>
      </div>\n\n";

?>
    </div>
  </body>
</html>
