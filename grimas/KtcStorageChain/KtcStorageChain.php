<?php

require_once("../grima-lib.php");

class KtcStorageChain extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);


		$callno = $bib->getLCCallNumber();

		$holding = new Holding();
		$holding['library_code'] = 'storage';
		$holding['location_code'] = 'rs';
		$holding->setCallNumber($callno[0],$callno[1],0);
		$holding->addToAlmaBib($this['mms_id']);

		$bararr = $bib->getSubfieldValues("949","i");

		$item = new Item();
		$item['barcode'] = $bararr[0];
		$item->addToAlmaHolding($this['mms_id'],$holding['holding_id']);

		$bib->deleteField("852");
		$bib->deleteField("949");
		$bib->updateAlma();

		$this->addMessage('success',"Added tree: ${this['mms_id']} " . $bib->get_title_proper());
	}

        function print_success() {
			sleep(2);
            do_redirect('../Hierarchy/Hierarchy.php?mms_id=' . $this['mms_id']);
        }


}

KtcStorageChain::RunIt();
