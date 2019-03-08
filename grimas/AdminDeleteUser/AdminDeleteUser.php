<?php

require_once("../grima-lib.php");

class AdminDeleteUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$username = $this['username'];
			$institution = $this['institution'] or $user['institution'];
			$newuser = GrimaUser::LookupUser($username,$institution);
			if( $newuser === False ) {
				throw new Exception( "User '$username' at $institution does not exist.");
			}
			$newuser->deleteFromDB();
			$this->addMessage('success',"User $username at $institution has been deleted.");
		} else {
			throw new Exception("User {$user['username']} (you) is not admin.");
		}
	}
}

AdminDeleteUser::RunIt();
