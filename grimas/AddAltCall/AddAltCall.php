<?php

require_once("../grima-lib.php");

class AddAltCall extends GrimaTask {

	function do_task() {
		$item = new Item();
		$item->loadFromAlmaBarcode($this['barcode']);
		$item['alternative_call_number'] = $this['note'];
		$item->updateAlma();
		if ($this['note'] == "") {
			$this->addMessage('success',"Alternative Call Number cleared on {$this['barcode']}");
		} else {
			$this->addMessage('success',"Alternative Call Number added to {$this['barcode']}");
		}
	}

}

AddAltCall::RunIt();
