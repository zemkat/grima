<?php

require_once("../grima-lib.php");

class PortfolioETD extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		$port = new ElectronicPortfolio();
		preg_match("/u (http[^ ]+)/",$this['url'],$m);

		$port['static_url'] = "jkey=${m[1]}";
		$port['proxy_enabled'] = "false";
		$port['url_type'] = "static";

		$types = array("DISSERTATION","MASTERTHESIS");
		if (in_array($this['type'],$types)) {
			$port['material_type'] = $this['type'];
		}
		$port['library'] = "young";

		$port->addToAlmaBib($bib['mms_id']);

		$this->addMessage('success',"Added portfolio to ${bib['mms_id']}");
	}

}

PortfolioETD::RunIt();
