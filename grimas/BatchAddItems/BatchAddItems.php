<?php

require_once("../grima-lib.php");

class BatchAddItems extends GrimaTask {

	function do_task() {

		/*
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		$holding = new Holding();
		$holding->loadFromAlma($this['mms_id'],$this['holding_id']);
		*/

		$item = new Item();
		$item->loadFromAlmaBCorX($this['item']);
		$mms_id = $item['mms_id'];
		$holding_id = $item['holding_id'];

		$this->barcodes = preg_split('/\r\n|\r|\n/',$this['barcodes']);

		foreach ($this->barcodes as $barcode) {
			$item['barcode'] = $barcode;
			$item->addToAlmaHolding($mms_id,$holding_id);
		}

		$this->addMessage('success',"Items successfully added to {$this['holding_id']}");


	}
}

BatchAddItems::RunIt();
