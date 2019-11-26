<?php

require_once("../grima-lib.php");

class FixTechProJob extends GrimaTask {
# jobs to process
#
# 13311951850002636
# 13311962270002636

	function do_task() {

		$set = new Set();
		$set->createFromImport($this['job_id'],"MATCHES_FOUND");
		sleep(2);
		$set->getMembers();

		$size = count($set->members);
		foreach ($set->members as $member) {

        	$bib = new Bib();
			$bib->loadFromAlma($member->id);
			$bib->getHoldings();

			if (count($bib->holdings) > 1) {
				addMessage('warn', "More than one holding on bib {$bib['mms_id']}");
				continue;
			} else {
				$holding = $bib->holdings[0];
				$holding->deleteSubfieldMatching('852','x','/tech ?pro/i');

				$arr = $bib->getLCCallNumber();
				$holding->replaceOrAddSubfield('852','h',$arr[0]);
				$holding->replaceOrAddSubfield('852','i',$arr[1]);

				$holding->updateAlma();

				# fix the yl whatever
			}
		}
		$set->deleteFromAlma();
		$this->addMessage('success',"Updated all records for {$this['job_id']}");

	}
}

FixTechProJob::RunIt();
