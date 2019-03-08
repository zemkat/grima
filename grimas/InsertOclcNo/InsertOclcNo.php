<?php

require_once("../grima-lib.php");

class InsertOclcNo extends GrimaTask {
	
	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		$bib->deleteField("019");
		$bib->deleteField("035");

		$new_oclcnum = "(OCoLC)" . $this['oclcnum'];
		$bib->appendField("035","","",array('a' => $new_oclcnum));
		$bib->updateAlma();

	}

	function print_success() {
		GrimaTask::call('PrintBib', array('mms_id' => $this['mms_id']));
	}
		
}

InsertOclcNo::RunIt();
