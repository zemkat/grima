<?php

require_once("../grima-lib.php");

class CombineHoldings extends GrimaTask {

	function do_task() {
		$keep_holding = new Holding();
		$keep_holding->loadFromAlmaX($this['holding_id']);
		$others = preg_split('/\r\n|\r|\n/',$this['others']);

		$bib = new Bib();
		$bib->loadFromAlma($keep_holding['mms_id']);
		$all_holdings = $bib->getHoldings();

		if (strtoupper($others[0]) == "ALL") {
			$others = array();
			foreach ($bib->getHoldings() as $lh) {
				if ($lh['holding_id'] != $keep_holding['holding_id']) {
					$others[] = $lh['holding_id'];
					$adds[] = $lh;
				}
			}
		} else {
			$adds = array();
			foreach ($others as $other) {
				$add = new Holding();
				$add->loadFromAlmaX($other);
				if ($add['mms_id'] == $keep_holding['mms_id']) {
					$adds[] = $add;
				} else {
					throw new Exception("Holding $other not on same bib.");
				}
			}
		}

		$do_first = array();
		foreach ($all_holdings as $lh) {
			if ($lh['holding_id'] == $keep_holding['holding_id']) {
				continue;
			}
			if (($lh['library_code'] == $keep_holding['library_code'])
					and ($lh['location_code'] == $keep_holding['location_code'])) {
				if (in_array($lh['holding_id'],$others)) {
					$do_first[] = $lh;
				} else {
					throw new Exception("All holdings from location must be combined.");
				}
			}
		}


		if (! empty($do_first)) {
			# build array 
			$otherthan = array();
			foreach ($do_first as $do) {
				$otherthan[] = array($do['library_code'],$do['location_code']);
			}
			foreach ($do_first as $do) {
				$loc = Library::getOneLibraryLocation($otherthan);
				# re-set location to some other one
				$do['library_code'] = $loc['library_code'];
				$do['location_code'] = $loc['code'];
				$do->updateAlma();
				$otherthan[] = array($loc['library_code'],$loc['code']);
			}
		}

		foreach ($adds as $add) {
			foreach ($add->getItems() as $item) {
				$item['library_code'] = $keep_holding['library_code'];
				$item['location_code'] = $keep_holding['location_code'];
				$item->updateAlma();
			}
		}

		$this->addMessage('success',"combined holdings onto {$keep_holding['holding_id']}");
	}
}

CombineHoldings::RunIt();
