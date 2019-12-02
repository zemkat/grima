<?php

require_once("../grima-lib.php");

function myfunc($item_pid, $args) {
	$item = new Item();
	$item->loadFromAlmaX($item_pid);
	if ($item[$args['whichnote']]) {
		$item[$args['whichnote']] .= " ; " . $args['note'];
	} else {
		$item[$args['whichnote']] = $args['note'];
	}
	$item->updateAlma();
}

class AppendToNoteOnSet extends GrimaTask {

	function do_task() {

		$set = new Set();
		$set->loadFromAlma($this['set_id']);
		$set->runOnElements('myfunc', array(
			'whichnote' => $this['whichnote'],
			'note' => $this['note'],
		));

		$this->addMessage('success',"Updated all items in {$this['set_id']}");

	}
}

AppendToNoteOnSet::RunIt();
