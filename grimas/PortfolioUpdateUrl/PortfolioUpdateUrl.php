<?php

require_once("../grima-lib.php");

class PortfolioUpdateUrl extends GrimaTask {

	function do_task() {
		/*
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->deleteFromAlma();
		$this->addMessage('success',"deleted bib {$this['mms_id']}");
		*/

        $port = new ElectronicPortfolio();
        $port->loadFromAlma($this['portfolio_id']);
        $port['url'] = "jkey=" . htmlspecialchars($this['url']);
        $port->updateAlma();
        $this->addMessage('success', "updated url on {$port['portfolio_id']}");

	}
}

PortfolioUpdateUrl::RunIt();
