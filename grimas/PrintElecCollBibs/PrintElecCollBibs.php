<?php

require_once("../grima-lib.php");

class PrintElecCollBibs extends GrimaTask {

	function do_task() {
		$this->collection = new ElectronicCollection();
		$this->collection->loadFromAlma($this['collection_id']);
		
		$this->collection->getServices();

		foreach ($this->collection->services as $service) {
			$service->retrieveAllPortfolios();
			foreach ($service->portfolios as $portfolio) {
				$portfolio->bib = new Bib();
				$portfolio->bib->loadFromAlma($portfolio['mms_id']);
			}
		}

		$this->splatVars['width'] = 12;
		$this->splatVars['collection'] = $this->collection;
		$this->splatVars['body'] = array( 'collection', 'messages' );
	}

}

PrintElecCollBibs::RunIt();
