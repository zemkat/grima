<?php

require_once("../grima-lib.php");

class AdminDeleteUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$newuser = GrimaUser::LookupUser($this['username']);
			if( $newuser === False ) {
				$this->addMessage('warning',"User {$this['username']} does not exist.");
				return;
			}
			$newuser->deleteFromDB();
			$this->addMessage('success',"User {$newuser['username']} has been deleted.");
		} else {
			throw new Exception('error',"User {$user['username']} is not admin.");
		}
	}
}

AdminDeleteUser::RunIt();
