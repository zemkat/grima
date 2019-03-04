<?php

require_once("../grima-lib.php");

class MyGrimaDb extends GrimaDb {
	static function getAllUsers($institution, $isSiteAdmin = false) {
		$where = $isSiteAdmin ? "( 1=1 or institution=:institution )" : "( institution = :institution )";
		$db = self::getDb();
		$query = $db->prepare("SELECT * FROM institutions NATURAL JOIN users WHERE $where");
		$success = $query->execute( array( 'institution' => $institution ) );
		$users = $query->fetchAll(PDO::FETCH_ASSOC);
		return $users;
	}
}

class AdminListUsers extends GrimaTask {
	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		$isSiteAdmin	= $user['isAdmin'] > 1;
		$username	= $user['username'];
		$institution	= $user['institution'];
		$users = MyGrimaDb::getAllUsers($institution,$isSiteAdmin);
		$this->splatVars['users']	= (array) $users;
		$this->splatVars['body']	= [ 'users', 'messages' ]; // which templates to splat
		$this->splatVars['isSiteAdmin'] = $isSiteAdmin;
		$this->splatVars['currentUser'] = $username;
		$this->splatVars['currentInst'] = $institution;
	}
}

AdminListUsers::RunIt();
