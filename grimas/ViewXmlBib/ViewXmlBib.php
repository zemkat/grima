<?php

require_once("../grima-lib.php");

class ViewXmlBib extends GrimaTask {

	function do_task() {
		$this->bib = new Bib();
		$this->bib->loadFromAlma($this['mms_id']);
	}

	function print_success() {
		XMLtoWeb($this->bib->xml);
	}

}

ViewXmlBib::RunIt();
