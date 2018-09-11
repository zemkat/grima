<?php

require_once("../grima-lib.php");

class CreateBriefBib extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib['title'] = $this['title'];
		$bib->addToAlma();
		$this->addMessage('success',"Added as record ${bib['mms_id']}");
	}

}

CreateBriefBib::RunIt();
