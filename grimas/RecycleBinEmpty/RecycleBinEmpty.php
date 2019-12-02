<?php

require_once("../grima-lib.php");

class RecycleBinEmpty extends GrimaTask {

	function do_task() {

		$ds = new GrimaDataStore();

		if (isset($ds['recycle bin'])) {
			$set_id = $ds['recycle bin'];
		} else {
			throw Exception("no recycle bin; recycle something to set one up");
		}

		$job = new Job();
		$job['id'] = 'M28';
		$job->addParameter('HANDLE_RELATED_BIBS_isSelected','false');
		$job->addParameter('HANDLE_INVENTORY_BIBS_isSelected','true');
		$job->addParameter('set_id',$set_id);
		$job->addParameter('job_name','Delete Bibliographic records - via API - RECYCLE BIN');
		$job->runInAlma();

		$this->addMessage('success',"Job Scheduled");
	}
}

RecycleBinEmpty::RunIt();
