<?php

require_once("../grima-lib.php");

class DeleteTree extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->deleteTreeFromAlma();
		$this->addMessage('success',
			"deleted bib {$this['mms_id']} and all inventory");
	}
}

DeleteTree::RunIt();
