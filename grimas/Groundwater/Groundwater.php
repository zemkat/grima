<?php

require_once("../grima-lib.php");

class Groundwater extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		$callno = $bib->getLCCallNumber();

		$holding = new Holding();
		$holding['library_code'] = 'science';
		$holding['location_code'] = 'sciref';
		$holding->setCallNumber($callno[0],$callno[1],0);
		$holding->addToAlmaBib($this['mms_id']);

		$bararr = $bib->getSubfieldValues("949","i");

		$item = new Item();
		$item['barcode'] = $bararr[0];
		$item->addToAlmaHolding($this['mms_id'],$holding['holding_id']);

		$webarr = $bib->getSubfieldValues("856","u");

		$collection_id = '61338238140002636';
		$service_id = '62338238130002636';
		$port = new ElectronicPortfolio();
		$port['is_local'] = "true";
		$port['is_standalone'] = "false";
		$port['material_type'] = "WEBSITE";
		$port['static_url'] = 'jkey=' . $webarr[0];
		$port['url'] = 'jkey=' . $webarr[0];
		$port['url_type'] = 'static';
		$port['collection_id'] = $collection_id;
		$port['service_id'] = $service_id;

		#$port->addToAlmaService($collection_id,$service_id);

		$this->addMessage('success',"Added tree: ${this['mms_id']} " . $bib->get_title_proper());
	}

}

Groundwater::RunIt();
