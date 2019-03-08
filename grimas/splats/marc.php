<dl class="row marc">
<?php
	$xpath = new DomXPath($marc->xml);
	$fields = $xpath->query("//record/controlfield");
?>
<?php	foreach ($fields as $field): ?>
  <dt class="col-1 text-right tag control"><?= $e($field->getAttribute("tag")) ?></dt>
  <dd class="col-11 field control"><?= $e($field->nodeValue) ?></dd>
<?php	endforeach ?>
<?php	$fields = $xpath->query("//record/datafield"); ?>
<?php	foreach ($fields as $field): ?>
<?php
		$ind1 = $field->getAttribute("ind1");
		if ($ind1 == " ") { $ind1 = "_"; }
		$ind2 = $field->getAttribute("ind2");
		if ($ind2 == " ") { $ind2 = "_"; }
		$tag = $field->getAttribute("tag");
?>
  <dt class='col-1 text-right tag data'><?= $e($tag)?></dt>
  <dd class='col-11'>
    <span class='indicators'><?=$e($ind1.$ind2)?></span>
<?php		foreach ($field->childNodes as $subfield): ?>
<?php			if ($subfield->nodeName == "subfield"): ?>
    <span class='subfield delimiter'>ǂ<?= $e($subfield->getAttribute("code"))?></span>
    <span class='subfield value'><?=$e($subfield->nodeValue)?></span>
<?php			endif ?>
<?php		endforeach?>
  </dd>
<?php endforeach ?>
</dl>
