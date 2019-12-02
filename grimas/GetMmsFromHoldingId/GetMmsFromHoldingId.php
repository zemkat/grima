<?php

require_once("../grima-lib.php");

class GetMmsFromHoldingId extends GrimaTask {

	function do_task() {
		$mms_id = Holding::getMmsFromHoldingID($this['holding_id']);
		$this->addMessage('success',"Holding is on record $mms_id");
	}
}

GetMmsFromHoldingId::RunIt();
