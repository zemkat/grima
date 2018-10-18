<?php

require_once("../grima-lib.php");

class AddInternalNote extends GrimaTask {

	function do_task() {
		$item = new Item();
		$item->loadFromAlmaBarcode($this['barcode']);
		$item['internal_note_1'] = $this['note'];
		$item->updateAlma();
		if ($this['note'] == "") {
			$this->addMessage('success',"Internal note 1 cleared on {$this['barcode']}");
		} else {
			$this->addMessage('success',"Internal note 1 added to {$this['barcode']}");
		}
	}

}

AddInternalNote::RunIt();
