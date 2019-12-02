<?php

require_once("../grima-lib.php");

class PrintHoldingFromBib extends GrimaTask {

	function do_task() {
		$this->holding = new Holding();
		$this->holding->loadFromAlma($this['mms_id'],$this['holding_id']);
		$this->splatVars['width'] = 12;
		$this->splatVars['marc'] = $this->holding;
		$this->splatVars['body'] = array( 'marc', 'messages' );
		$this->splatVars['title'] = "Alma Holding #${this['holding_id']}";
	}

}

PrintHoldingFromBib::RunIt();
