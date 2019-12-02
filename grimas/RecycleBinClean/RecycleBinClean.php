<?php

require_once("../grima-lib.php");

class RecycleBinClean extends GrimaTask {

	function do_task() {

		$ds = new GrimaDataStore();

		if (isset($ds['recycle bin'])) {
			$set_id = $ds['recycle bin'];
		} else {
			throw Exception("no recycle bin; recycle something to set one up");
		}

		$recyclebin = new Set();
		$recyclebin->loadFromAlma($set_id);
		$recyclebin->removeAllMembersInAlma(); 

		$this->addMessage('success',"Recycle Bin Cleaned");
	}
}

RecycleBinClean::RunIt();
