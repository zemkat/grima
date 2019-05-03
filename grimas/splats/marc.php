<table class="marc">
<?php
	$xpath = new DomXPath($marc->xml);
	$fields = $xpath->query("//record/controlfield");
?>
<?php	foreach ($fields as $field): ?>
  <tr>
    <td class="text-right tag control pr-2"><?= $e($field->getAttribute("tag")) ?></td>
    <td colspan="2" class="field control"><?= $e($field->nodeValue) ?></td>
  </tr>
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
  <tr class="align-top">
    <td class='text-right tag data pr-2'><?= $e($tag)?></td>
    <td class='indicators pr-2'><?=$e($ind1.$ind2)?></td>
    <td class="data subfields">
<?php		foreach ($field->childNodes as $subfield): ?>
<?php			if ($subfield->nodeName == "subfield"): ?>
    <span class='subfield delimiter'>ǂ<?= $e($subfield->getAttribute("code"))?></span>
    <span class='subfield value'><?=$e($subfield->nodeValue)?></span>
<?php			endif ?>
<?php		endforeach?>
    </td>
  </tr>
<?php endforeach ?>
</table>
