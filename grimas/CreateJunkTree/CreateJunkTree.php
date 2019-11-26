<?php

require_once("../grima-lib.php");

class CreateJunkTree extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib['title'] = "ZTEST TEST TREE";
		$bib->addToAlma();
		
#		$holding = new Holding();
#		$holding['library_code'] = "science";
#		$holding['location_code'] = "sci";
#		$holding->addToAlmaBib($bib['mms_id']);
#
#		$item = new Item();
##		$item['barcode'] = "TESTTEST";
#		$item->addToAlmaHolding($bib['mms_id'],$holding['holding_id']);
#
#		$holding2 = new Holding();
#		$holding2['library_code'] = "young";
#		$holding2['location_code'] = "yl4";
#		$holding2->addToAlmaBib($bib['mms_id']);

#		$item2 = new Item();
#	#	$item2['barcode'] = "HOWDYCHOWDER";
#		$item2->addToAlmaHolding($bib['mms_id'],$holding2['holding_id']);

#		$item3 = new Item();
#		$item3['barcode'] = "HOWDYYYY";
#		$item3->addToAlmaHolding($bib['mms_id'],$holding2['holding_id']);
#
		#error_log('adding one');
		$port = new ElectronicPortfolio();
		$port->addToAlmaBib($bib['mms_id']);
#
#		sleep(3);
#		error_log('adding two');
		$port2 = new ElectronicPortfolio();
		$port2['public_note'] = 'hoowdy';
		$port2->addToAlmaBib($bib['mms_id']);
		# american civil war letters and diaries local collection
		# $port2->addToAlmaService("61313858780002636","62313858770002636");

		# CZ collection?
		#$port2->addToAlmaService("61258115880002636","62258118290002636");

		$this->addMessage('success',"Added tree starting at ${bib['mms_id']}");
	}

}

CreateJunkTree::RunIt();
