<?php

require_once("../grima-lib.php");

class GdsSetup extends GrimaTask {

	function do_task() {
		if (! GrimaDataStore::exists()) {
			$ds = new GrimaDataStore();
			$mms_id = $ds->bib['mms_id'];
			$this->addMessage('success',"grima data store set up at $mms_id");
		} else {
			$this->addMessage('error', "grima data store already exists");
		}
	}
}

GdsSetup::RunIt();
