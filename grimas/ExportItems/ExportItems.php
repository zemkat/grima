<?php

require_once("../grima-lib.php");

class ExportItems extends GrimaTask {

	function do_task() {

		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->getMembers();

		$items = array();

		foreach ($set->members as $member) {
			$item = new Item();
			$item->loadFromAlmaX($member->id);
			$items[] = $item;
		}

		$this->splatVars['items'] = $items;

	}
}

ExportItems::RunIt();
