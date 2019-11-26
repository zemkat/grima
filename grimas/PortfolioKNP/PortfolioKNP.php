<?php

require_once("../grima-lib.php");

class PortfolioKNP extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		$port = new ElectronicPortfolio();

		preg_match("/(http[^ ]+)/",$this['url'],$m);

		$port['static_url'] = "jkey=${m[1]}";
		$port['proxy_enabled'] = "false";
		$port['url_type'] = "static";

		$port['material_type'] = "NEWSPAPER";
		$port['mms_id'] = $this['mms_id'];
		$port['library'] = "young";
		$port['is_standalone'] = "false";

#		error_log($port->xml->saveXML());

		/* hacky work-around */
		$port->addToAlmaBib($bib['mms_id']);
		$first_mms = $port["portfolio_id"];

		$port->addToAlmaService("61348557360002636", "62348557350002636");

		/* hacky work-around */
		$first_port = new ElectronicPortfolio();
		$first_port->loadFromAlmaX($first_mms);
		$first_port->deleteFromAlma();

		$this->addMessage('success',"Added portfolio to ${bib['mms_id']}");
	}

}

PortfolioKNP::RunIt();
