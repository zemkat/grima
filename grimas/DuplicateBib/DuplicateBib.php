<?php

require_once("../grima-lib.php");

class DuplicateBib extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->addToAlma();
		$this->addMessage('success',"bib duplicated. new record {$bib['mms_id']}");
	}
}

DuplicateBib::RunIt();
