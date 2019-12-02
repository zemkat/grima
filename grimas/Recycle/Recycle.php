<?php

require_once("../grima-lib.php");

class Recycle extends GrimaTask {

	function do_task() {

		$ds = new GrimaDataStore();

		if (! isset($ds['recycle bin'])) {
			$set = new Set();
			$set['name'] = 'GRIMA RECYCLE BIN';
			$set['type'] = 'ITEMIZED';
			$set['status'] = 'ACTIVE';
			$set['content'] = 'BIB_MMS';
			$set->addToAlma();
			$set_id = $set['id'];
			$this->addMessage('success',"added recycle bin at $set_id");
			$ds['recycle bin'] = $set_id;
		} else {
			$set_id = $ds['recycle bin'];
		}

		$recyclebin = new Set();
		$recyclebin->loadFromAlma($set_id);
		
		$add = new Set();
		$add->addMember($this['mms_id']);

		$recyclebin->appendInAlma($add);

		$this->addMessage('success',"Recycled {$this['mms_id']}");
	}
}

Recycle::RunIt();
