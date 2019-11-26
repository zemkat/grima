<?php

require_once("../grima-lib.php");

class DeletePortfoliosFromBib extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->deleteAllPortfolios();
		$this->addMessage('success',"Deleted all portfolios from {$this['mms_id']}");
	}
}

DeletePortfoliosFromBib::RunIt();
