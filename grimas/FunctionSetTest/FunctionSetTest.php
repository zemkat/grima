<?php

require_once("../grima-lib.php");

function myfunc($element_id, $args = array()) {
	$bib = new Bib();
	$bib->loadFromAlma($element_id);
	error_log($bib['mms_id']);
}

function myfilter($element_id, $args = array()) {
	return true;
}

class FunctionSetTest extends GrimaTask {

	function do_task() {
		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->runOnElements('myfunc',array(),'myfilter',array());
		$this->addMessage('success',"ran function on {$this['set_id']}");
	}
}

FunctionSetTest::RunIt();
