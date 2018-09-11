<?php

require_once("../grima-lib.php");

class ViewXmlItem extends GrimaTask {

	function do_task() {
		$this->item = new Item();
		$this->item->loadFromAlmaX($this['item_pid']);
	}

	function print_success() {
		XMLtoWeb($this->item->xml);
	}

}

ViewXmlItem::RunIt();
