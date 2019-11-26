<?php

require_once("../grima-lib.php");

class ChainFromBib extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);

		# maybe just do all subfields
		$hldarrb = $bib->getSubfieldValues("952","b");
		$hldarrc = $bib->getSubfieldValues("952","c");
		$hldarrh = $bib->getSubfieldValues("952","h");
		$hldarri = $bib->getSubfieldValues("952","i");

		$hldarr6 = $bib->getSubfieldValues("966","a");

		$holding = new Holding();
		$holding['library_code'] = $hldarrb[0];
		$holding['location_code'] = $hldarrc[0];
		if (isset($hldarri[0])) {
			$calli = $hldarri[0];
		} else {
			$calli = null;
		}

		# GET INDICATOR
		# GET ACQ METHOD

		$holding->setCallNumber($hldarrh[0],$calli,0);
		# SET INDICATOR
		# SET ACQ METHOD

		foreach($hldarr6 as $hldarr) {
			# set multipart holding
			$holding->appendField('866','4','1',array('a' => $hldarr));
		}

		$holding->addToAlmaBib($this['mms_id']);

		# need getfields
		$bararr = $bib->getSubfieldValues("949","i");
		$bararre = $bib->getSubfieldValues("949","e");

		for($j=0;$j<sizeof($bararr);$j++) {
			$item = new Item();
			$item['barcode'] = $bararr[$j];
			#$item['enumeration_a'] = $bararre[$j];
			#$item['description'] = $bararre[$j];
			$Type = $bib['Type']; $Blvl = $bib['BLvl'];
			$format = "$Type$Blvl";
	
			$phys = $bib->getSubfieldValues("300","a");
			if (preg_match('/(computer optical disc|cd(-?rom)?)/i',
					$phys[0])) {
				$item['physical_material_type_code'] = "CDROM";
				$item['policy'] = "cdrom";
			} else {
				if (preg_match('/videodisc/',$phys[0])) {
					$item['physical_material_type_code'] = "DVD";
					$item['policy'] = "video";
				} else {
					if ($format == "am") {
						$item['physical_material_type_code'] = "BOOK";
						$item['policy'] = "book";
					}
				}
			}
			$item->addToAlmaHolding($bib['mms_id'],$holding['holding_id']);
		}

		$bib->deleteField("952");
		$bib->deleteField("949");
		$bib->deleteField("966");
		$bib->updateAlma();

		$this->addMessage('success',"Added tree: ${bib['mms_id']} " . $bib->get_title_proper());
	}

        function print_success() {
			sleep(2);
			GrimaTask::call('Hierarchy', array('mms_id' => $this['mms_id']));
        }

}

ChainFromBib::RunIt();
