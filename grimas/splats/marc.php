<?php
	print "          <dl class='dl-horizontal marc'>\n";
	$xpath = new DomXPath($marc->xml);

	$fields = $xpath->query("//record/controlfield");
	foreach ($fields as $field) {
		$tag = $field->getAttribute("tag");
        print "            <dt class='tag control'>$tag</dt><dd class='field control'>{$field->nodeValue}</dd>\n";
	}

	$fields = $xpath->query("//record/datafield");

	foreach ($fields as $field) {
		$ind1 = $field->getAttribute("ind1");
		if ($ind1 == " ") { $ind1 = "_"; }
		$ind2 = $field->getAttribute("ind2");
		if ($ind2 == " ") { $ind2 = "_"; }
		$tag = $field->getAttribute("tag");
		print "            <dt class='tag data'>$tag</dt><dd><span class='indicators'>$ind1$ind2</span>";
		foreach ($field->childNodes as $subfield) {
			if ($subfield->nodeName == "subfield") {
				$code = $subfield->getAttribute("code");
				print " <span class='subfield delimiter'>ǂ$code</span> ";
				print "<span class='subfield value'>{$subfield->nodeValue}</span>";
			}
		}
        print "</dd>\n";
	}
	print "</dl>";
