<?php

require_once("../grima-lib.php");

class DeleteBib extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->deleteFromAlma();
		$this->addMessage('success',"deleted bib {$this['mms_id']}");
	}
}

DeleteBib::RunIt();
