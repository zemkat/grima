<?php

require_once("../grima-lib.php");

class Boundwith extends GrimaTask {
	public $biblist = array();
	
	function do_task() {
		$this->bibs = preg_split('/\r\n|\r|\n/',$this['mms']);

		# BIBS
		foreach ($this->bibs as $mmsid) {
			$bib = new Bib();
			$bib->loadFromAlma($mmsid);
			$this->biblist[] = $bib;
		}

		# build array of unique titles
		$arrfor501 = array();
		foreach ($this->biblist as $k => $bib) {
			$title = $bib->get_title_proper();
			if ($k > 0) {
				$this->biblist[0]->appendField("774","1"," ",array(
					't' => $title,
					'w' => $bib['mms_id']
					)
				);
			} 
			if (!in_array($title,$arrfor501)) {
				$arrfor501[] = $title;
			}
		}

		foreach ($this->biblist as $bib) {
			$my501text = "Bound with: ";
			$skip = $bib->get_title_proper();
			foreach ($arrfor501 as $title) {
				if ($title != $skip) {
					$my501text .= $title . "; ";
				}
			}
			$my501text = preg_replace("/; $/",".",$my501text);
			$bib->appendField("501"," "," ",array('a' => $my501text));
			$bib->updateAlma();
		}

		## HOLDING
		$this->biblist[0]->getHoldings();
		$mfhd = $this->biblist[0]->holdings[0];

		foreach ($this->biblist as $k => $bib) {
			if ($k > 0) {
				$mfhd->appendField("014","1"," ",array('a' => $bib['mms_id']));
			}
		}
		$mfhd->updateAlma();

		$this->splatVars['width'] = 12;
		$this->splatVars['biblist'] = $this->biblist;
		$this->splatVars['body'] = array( 'list', 'messages' );
	}

}

Boundwith::RunIt();
