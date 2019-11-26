<?php

require_once("../grima-lib.php");

class PortfolioNoteFrom856 extends GrimaTask {

	function do_task() {
		$bib = new Bib();
		$bib->loadFromAlma($this['mms_id']);
		$note = '<ul><b>Individual volumes:</b><br /><br />';
		# get all 856 to build note
        foreach ($bib->getFields("856") as $field) {
			if ($field->indicators != "40") {
				continue;
			}
			$labels = $field->getSubfields("3");
			if (sizeof($labels) < 1) {
				$label = "view full text";
				$this->addMessage('warning','No label; using "view full text"');
			} else {
				$label = $labels[0]->data;
				if (sizeof($labels) > 1) {
					$this->addMessage('warning','More than one label; using first');
				}
			}
			$urls = $field->getSubfields("u");
			if (sizeof($urls) < 1) {
				$this->addMessage('warning','No URL; skipping field');
				continue;
			} else {
				$url = htmlspecialchars($urls[0]->data);
				if (sizeof($urls) > 1) {
					$this->addMessage('warning','More than one URL; using first');
				}
			}
			$note .= "<li><a target=\"_blank\" href=\"$url\">$label</a></li><br />";
		}
		$note .= "</ul>";

		$port = new ElectronicPortfolio();
		if (isset($this['portfolio_id'])) {
			$port->loadFromAlma($this['portfolio_id']);
		} else {
			if ($ports = $bib->getPortfolios()) {
				$port = $ports[0];
				$port->loadFromAlma($port['portfolio_id']);
				if (sizeof ($ports) > 1) {
					$this->addMessage('warning', "More than one portfolio, using {$port['portfolio_id']}");
				}
			} else {
				throw new Exception("No portfolio found on {$this['mms_id']}");
			}
		}
		$port['public_note'] = $note;
		$port->updateAlma();
		$this->addMessage('success', "updated note on {$port['portfolio_id']}");
	}
}

PortfolioNoteFrom856::RunIt();
