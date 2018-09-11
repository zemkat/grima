<?php

require_once("../grima-lib.php");

class ViewXmlHolding extends GrimaTask {

	function do_task() {
		$this->holding = new Holding();
		$this->holding->loadFromAlmaX($this['holding_id']);
	}

	function print_success() {
		XMLtoWeb($this->holding->xml);
	}

}

ViewXmlHolding::RunIt();
