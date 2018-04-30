<?php

require_once("grima-lib.php");

class PrintHolding extends GrimaTask {

	function do_task() {
		$this->holding = new Holding();
		$this->holding->loadFromAlmaX($this['holding_id']);
	}

	function print_success() {
		$an = $this->holding;
print <<<OUT
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
      .jumbotron{ min-height: 100vh; }
      img.fullwidth { max-width: 100%; }
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
    </style>
  </head>
  <body>
    <div class="container">
      <div class='panel panel-default bib'>
        <div class='panel-heading'>
OUT;

	print "<h1 class='panel-title'>Alma Holding #" . $this->args['holding_id'] . ":";
        print "</h1>
        </div>
        <div class='panel-body'>
          <dl class='dl-horizontal'>
";
	$xpath = new DomXPath($an->xml);

	$fields = $xpath->query("//record/controlfield");
	foreach ($fields as $field) {
        print "            <dt>" . $field->getAttribute("tag") . "</dt><dd>" . $field->nodeValue . "</dd>\n";
	}

	$fields = $xpath->query("//record/datafield");

	foreach ($fields as $field) {
		$ind1 = $field->getAttribute("ind1");
		if ($ind1 == " ") { $ind1 = "_"; }
		$ind2 = $field->getAttribute("ind2");
		if ($ind2 == " ") { $ind2 = "_"; }
		print "            <dt>" . $field->getAttribute("tag") . "</dt><dd>" . $ind1 . $ind2;
		foreach ($field->childNodes as $subfield) {
			if ($subfield->nodeName == "subfield") {
				print " ǂ" . $subfield->getAttribute("code") . " " .
				$subfield->nodeValue;
			}
		}
        print "</dd>\n";
	}
	print "
    </div>
  </body>
</html>
";

	}

}

PrintHolding::RunIt();
