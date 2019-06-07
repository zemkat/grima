<?php

require_once("../grima-lib.php");

class MoreItems extends GrimaTask {

	function do_task() {

		$this->item = new Item();
		$this->item->loadFromAlmaBarcode($this['barcode_model']);

		if (isset($this['adding']) and ($this['adding'] == "true")) {
			$newItem = clone $this->item;
			unset($newItem['item_pid']);
			$itemdata_elements = array(
				'barcode',
				'enumeration_a',
				'enumeration_b',
				'chronology_i',
				'chronology_j',
				'description'
			);
			foreach ($itemdata_elements as $element) {
				if (isset($this[$element])) {
					$newItem[$element] = $this[$element];
				}
			}
			$ret = $newItem->addToAlmaHolding($this->item['mms_id'],$this->item['holding_id']);
			$this->item = new Item();
			$this->item->xml = $ret;
		}
		$this->splatVars['item'] = $this->item;
	}

}

MoreItems::RunIt();
