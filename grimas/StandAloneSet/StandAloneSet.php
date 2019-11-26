<?php

require_once("../grima-lib.php");

class StandAloneSet extends GrimaTask {

	function do_task() {

		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->getMembers();

		$size = count($set->members);

		foreach ($set->members as $member) {
			$port = new ElectronicPortfolio();
			$port->loadFromAlma($member->id);
			#$port['is_standalone'] = "true";
			#$port['collection_id'] = "61313858780002636";
			#$port['service_id'] = "62313858770002636";
			#$port['public_note'] = "update";
			#error_log($port->xml->saveXML());
			$port->updateAlma();
		}

		$this->addMessage('success',"Updated all records for {$this['set_id']}");

	}
}

StandAloneSet::RunIt();
