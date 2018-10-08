<?php

require_once("../grima-lib.php");

class AdminResetPassword extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$newuser = new GrimaUser();
			$newuser['username'] = $this['username'];
			$newuser['password'] = $this['password'];
			$newuser['institution'] = $this['institution'];
			GrimaUser::ResetPassword($newuser['username'], $newuser['institution'],
				$newuser['password']);
			$this->addMessage('success',"Password for {$newuser['username']} successfully changed.");
		} else {
			throw new Exception("User {$user['username']} is not admin.");
		}
	}
}

AdminResetPassword::RunIt();
