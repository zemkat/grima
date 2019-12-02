<?php

require_once("../grima-lib.php");

function myfunc($mms_id,$args) {
    $bib = new Bib();
	$bib->loadFromAlma($mms_id);
	$bib->getHoldings();

	if (count($bib->holdings) > 1) {
		addMessage('warn', "More than one holding on bib $mms_id");
		return;
	} 

	$holding = $bib->holdings[0];
	$holding->getItemList();
	if (count($holding->itemList->items) > 1) {
		addMessage('warn', "More than one item on holding {$holding['holding_id']}");
		return;
	}

	$item = $holding->itemList->items[0];
	$item['in_temp_location'] = "true";
	$item['temp_library'] = $args['temp_library'];
	$item['temp_location'] = $args['temp_location'];
	$item->updateAlma();
}

class MarkImportTemporaryLocation extends GrimaTask {

	function do_task() {
		$set = new Set();
		$set->createFromImport($this['job_id'],"TOTAL_RECORDS_IMPORTED");
		sleep(2);
		$set->runOnElements('myfunc',array(
			'temp_library' => $this['temp_library'],
			'temp_location' => $this['temp_location'],
		));

		$set->deleteFromAlma();
		$this->addMessage('success',"Updated all records for {$this['job_id']}");

	}
}

MarkImportTemporaryLocation::RunIt();
