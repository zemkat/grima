<?php

require_once("../grima-lib.php");

class JEtoJF extends GrimaTask {

	function do_task() {
		$item = new Item();
		$item->loadFromAlmaBarcode($this['barcode']);

		# Add 082 if it doesn't exist

		$holding_id = $item['holding_id'];
		$mms_id = $item['mms_id'];

		$bib = new Bib();
		$bib->loadFromAlma($mms_id);
		$bib->deleteField("082");
		$bib->appendField("082","0","0",array('a' => '[Fic]','2' => '23'));
		$bib->updateAlma();

		$holding = new Holding();
		$holding->loadFromAlma($mms_id,$holding_id);

		$subfh = $holding->getSubfieldValues("852","h");
		$subfi = $holding->getSubfieldValues("852","i");
		$class = ltrim(rtrim($subfh[0]));
		if (sizeof($subfi) > 0) {
			$subi = $subfi[0];
		} else {
			$subi = null;
		}

		$class = preg_replace("/^JE/","JF",$class);
		$holding->setCallNumber($class,$subi,8);
		$holding->updateAlma();
		if (is_null($subi)) { $subi = ""; } 
		$this->addMessage('success',"updated with call number $class $subi");

	}

}

JEtoJF::RunIt();
