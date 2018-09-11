<?php

require_once("../grima-lib.php");

class DedupASP extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->getPortfolioList();
		foreach ($bib->portfolioList as $port) {
			if ($port['service'] == '62339175230002636') {
				$this->addMessage('info',"leaving " . $port['portfolio_id']);
			}
			if ($port['service'] == '') {
				$port->deleteFromAlma();
				$this->addMessage('success',"deleted " . $port['portfolio_id']);
			}
		}
	}
}

DedupASP::RunIt();
