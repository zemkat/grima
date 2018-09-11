<?php

require_once("../grima-lib.php");

class Hierarchy extends GrimaTask {

	function do_task() {
		$this->bib = new Bib();
		$this->bib->loadFromAlma($this['mms_id']);
		$this->bib->getHoldings();
		foreach ($this->bib->holdings as $holding) {
			$holding->getItemList();
		}
		$this->splatVars['bib'] = $this->bib;
	}
}

Hierarchy::RunIt();
