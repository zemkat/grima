<?php

require_once("../grima-lib.php");

class StickSlipWilcox extends GrimaTask {

	private $header = "Locked Stacks<br />Wilcox Collection";
	private $library_code = "finearts";
	private $location_code = "faartbk";
	private $shelving_scheme = "0";
	
	function do_task() {

		$this->bibs = preg_split('/\r\n|\r|\n/',$this['mms']);
		# confirm five
		# remove blanks
                
		foreach ($this->bibs as $mmsid) {
			$bib = new Bib();
			$bib->loadFromAlma($mmsid);
			$this->biblist[] = $bib;

			$holding = new Holding();
			$holding['library_code'] = $this->library_code;
			$holding['location_code'] = $this->location_code;
			$holding['shelving_scheme'] = $this->shelving_scheme;
			$holding['mms_id'] = $bib['mms_id'];
			$holding->setCallNumberFromBib();
			$holding->addToAlmaBib($bib);
			$this->holdinglist[] = $holding;

			$item = new Item();
			# set properties
			$this->itemlist[] = $item;
		}

		# set splatvars bibs, holdings, items

	}
}

StickSlipWilcox::RunIt();
