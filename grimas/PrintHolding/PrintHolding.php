<?php

require_once("../grima-lib.php");

class PrintHolding extends GrimaTask {

	function do_task() {
		$this->holding = new Holding();
		$this->holding->loadFromAlmaX($this['holding_id']);
		$this->splatVars['width'] = 12;
		$this->splatVars['marc'] = $this->holding;
		$this->splatVars['body'] = array( 'marc', 'messages' );
		$this->splatVars['title'] = "Alma Holding #${this['holding_id']}";
	}

}

PrintHolding::RunIt();
