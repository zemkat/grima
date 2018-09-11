<?php

require_once("../grima-lib.php");

class RemoveTempLocation extends GrimaTask {

	function do_task() {
		$item = new Item();
		$item->loadFromAlmaBarcode($this['barcode']);
		$item['in_temp_location'] = 'false';
		$item->updateAlma();
		$this->addMessage('success',"Temporary location removed from {$this['barcode']}");
	}

}

RemoveTempLocation::RunIt();
