<?php

require_once("../grima-lib.php");

class RecycleBinEmpty extends GrimaTask {

	function do_task() {

		$job = new Job();
		$job['id'] = 'M28';
		$job->addParameter('HANDLE_RELATED_BIBS_isSelected','false');
		$job->addParameter('HANDLE_INVENTORY_BIBS_isSelected','true');
		$job->addParameter('set_id','9543638640002636');
		$job->addParameter('job_name','Delete Bibliographic records - via API - RECYCLE BIN');
		$job->runInAlma();

		$this->addMessage('success',"Job Scheduled");
	}
}

RecycleBinEmpty::RunIt();
