<?php

require_once("../grima-lib.php");

class NormacTest extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$bib->appendFieldN('909','12',array('a' => 'howdy', 'b' => 'partner'));
		$bib->updateAlma();
		$this->addMessage('success',"added line to bib {$this['mms_id']}");
	}
}

NormacTest::RunIt();
