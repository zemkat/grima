<?php

require_once("../grima-lib.php");

class DeletePortfoliosFromCollection extends GrimaTask {

	function do_task() {
		$coll = new ElectronicCollection();
		$coll->loadFromAlma($this['collection_id']);
		$coll->getServices();
		foreach ($coll->services as $service) {
			$service->deleteAllPortfolios("delete");
		}

		$this->addMessage('success',"Deleted all portfolios from collection {$this['collection_id']}");
	}
}

DeletePortfoliosFromCollection::RunIt();
