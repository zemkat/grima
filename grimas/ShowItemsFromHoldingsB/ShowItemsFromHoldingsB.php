<?php

require_once("../grima-lib.php");

class ShowItemsFromHoldingsB extends GrimaTask {

	function do_task() {
		$this->holding = new Holding();
		$this->holding->loadFromAlma($this['mms_id'],$this['holding_id']);
		$this->holding->getItems();
		$this->splatVars['holding'] = $this->holding;
	}
}

ShowItemsFromHoldingsB::RunIt();
