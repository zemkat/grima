<?php

require_once("../grima-lib.php");

class PrintItem extends GrimaTask {

	function do_task() {
		$this->item = new Item();
		$this->item->loadFromAlma($this['mms_id'],$this['holding_id'],$this['item_pid']);
		$this->splatVars['item'] = $this->item;
		$this->splatVars['body'] = array( 'item', 'item_debug', 'messages' );
		
		$this->splatVars['title'] = "Alma Bib #${this['mms_id']}: " . $this->item['title'] . $this->item['author'];
	}

}

PrintItem::RunIt();
