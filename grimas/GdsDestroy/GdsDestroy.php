<?php

require_once("../grima-lib.php");

class GdsDestroy extends GrimaTask {

	function do_task() {
		if (GrimaDataStore::exists()) {
			$ds = new GrimaDataStore();
			$ds->destroy();
			$this->addMessage('success',"grima data store destroyed");
		} else {
			$this->addMessage('error', "no grima data store exists");
		}
	}
}

GdsDestroy::RunIt();
