<?php

require_once("../grima-lib.php");

class AppendToNoteOnSet extends GrimaTask {

	function do_task() {

		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->getMembers();

		$size = count($set->members);

		foreach ($set->members as $member) {
			$item = new Item();
			$item->loadFromAlmaX($member->id);
			if ($item[$this['whichnote']]) {
				$item[$this['whichnote']] .= " ; " . $this['note'];
			} else {
				$item[$this['whichnote']] = $this['note'];
			}
			$item->updateAlma();

		}

		$this->addMessage('success',"Updated all items in {$this['set_id']}");

	}
}

AppendToNoteOnSet::RunIt();
