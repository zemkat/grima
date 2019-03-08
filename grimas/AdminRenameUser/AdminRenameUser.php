<?php

require_once("../grima-lib.php");

class AdminRenameUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$username = $this['username'];
			$newusername = $this['newusername'];
			$institution = $this['institution'] or $user['institution'];
			GrimaUser::RenameUser($username, $institution, $newusername);
			$this->addMessage('success',"Username for $username at $institution successfully changed to $newusername.");
		} else {
			throw new Exception("User {$user['username']} (you) is not admin.");
		}
	}
}

AdminRenameUser::RunIt();
