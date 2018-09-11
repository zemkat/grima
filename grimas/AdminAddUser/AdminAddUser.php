<?php

require_once("../grima-lib.php");

class AdminAddUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$newuser = new GrimaUser();
			$newuser['username'] = $this['username'];
			$newuser['password'] = $this['password'];
			$newuser['institution'] = $user['institution'];
			$newuser['isAdmin'] = false; # there can be only ONE for now XXX
			$newuser->addToDB();
			$this->addMessage('success',"User {$newuser['username']} successfully added.");
		} else {
			throw new Exception("User {$user['username']} is not admin.");
		}
	}
}

AdminAddUser::RunIt();
