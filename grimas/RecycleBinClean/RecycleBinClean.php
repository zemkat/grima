<?php

require_once("../grima-lib.php");

class RecycleBinClean extends GrimaTask {

	function do_task() {

		$recyclebin = new Set();
		$recyclebin->loadFromAlma("9543638640002636");
		
		$recyclebin->removeAllMembersInAlma(); 

		$this->addMessage('success',"Recycle Bin Cleaned");
	}
}

RecycleBinClean::RunIt();
