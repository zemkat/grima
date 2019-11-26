<?php

require_once("../grima-lib.php");

class RenameElecColl extends GrimaTask {

	function do_task() {
		$collection = new ElectronicCollection();
		$collection->loadFromAlma($this['collection_id']);
		$collection['public_name'] = $this['public_name']; # XXX clean?
		$collection->updateAlma();
		$this->addMessage('success',"bib duplicated. new record {$this['collection_id']} renamed to '{$this['public_name']}'");
	}
}

RenameElecColl::RunIt();
