<?php

require_once("../grima-lib.php");

class Recycle extends GrimaTask {

	function do_task() {
		$recyclebin = new Set();
		$recyclebin->loadFromAlma("9543638640002636");
		
		$add = new Set();
		$add->addMember($this['mms_id']);

		$recyclebin->appendInAlma($add);

		$this->addMessage('success',"Recycled {$this['mms_id']}");
	}
}

Recycle::RunIt();
