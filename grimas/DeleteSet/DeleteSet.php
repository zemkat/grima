<?php

require_once("../grima-lib.php");

class DeleteSet extends GrimaTask {

	function do_task() {
		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->deleteFromAlma();
		$this->addMessage('success',"Deleted set {$this['set_id']}");
	}
}

DeleteSet::RunIt();
