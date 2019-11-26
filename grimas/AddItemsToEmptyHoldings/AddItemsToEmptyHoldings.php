<?php

require_once("../grima-lib.php");

function myfunc($mms_id) {
	global $item_model;
	global $restrict_to;
	$bib = new Bib();
	$bib->loadFromAlma($mms_id);
	foreach ($bib->getHoldings() as $holding) {
		if (($restrict_to != '') 
				and (! in_array($holding['location_code'], $restrict_to))
				and (! in_array($holding['library_code'], $restrict_to))) {
			continue;
		}
		if (! $holding->hasItems()) {
			$item_model->addToAlmaHolding($mms_id,$holding['holding_id']);
		}
	}
}

class AddItemsToEmptyHoldings extends GrimaTask {

	function do_task() {
		global $item_model;
		global $restrict_to;
		$item_model = new Item();
		$item_model->loadFromAlmaBCorX($this['item']);
		$item_model['barcode'] = '';

		if (rtrim($this['library_or_location'])) {
			$restrict_to = explode(" ", $this['library_or_location']);
		} else {
			$restrict_to = null;
		}

		$set = new Set();
		$set->loadFromAlma($this['set_id']);

		if (($set['content'] == 'IEP') or ($set['content'] == 'BIB_MMS')) {
			$set->runOnElements('myfunc');
			$this->addMessage('success',"added items to {$this['set_id']}");
		} else {
			throw new Exception("Set {$this['set_id']} has wrong content type {$this['set_id']}");
		}
	}
}

AddItemsToEmptyHoldings::RunIt();
