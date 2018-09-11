<?php

require_once("../grima-lib.php");

class DeletePortfolio extends GrimaTask {

	function do_task() {
		$port = new ElectronicPortfolio();
		$port->loadFromAlmaX($this['portfolio_id']);
		$port->deleteFromAlma();
		$this->addMessage('success',"Deleted portfolio {$port['portfolio_id']}: {$port['title']}");
	}
}

DeletePortfolio::RunIt();
