<?php

require_once("../grima-lib.php");

class PrintBib extends GrimaTask {

	function do_task() {
		$this->bib = new Bib();
		$this->bib->loadFromAlma($this['mms_id']);
		$this->splatVars['marc'] = $this->bib;
		$this->splatVars['body'] = array( 'marc', 'messages' );
		$this->splatVars['title'] = "Alma Bib #${this['mms_id']}: " . $this->bib->get_title_proper();
	}

}

PrintBib::RunIt();
