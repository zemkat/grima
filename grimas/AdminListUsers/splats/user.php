<?php 
$mote = $isAdmin ? "Demote" : "Promote";
$part = $isAdmin ? "Demote" : "Admin";
$query	    = $e(http_build_query(array(
	"username" => $username,
	"institution"  => $institution,
	"redirect_url" => "../AdminListUsers/AdminListUsers.php",
	)));
$hrefReset  = "../AdminResetPassword/AdminResetPassword.php?$query";
$hrefMote   = "../Admin{$part}User/Admin{$part}User.php?$query";
$hrefRename = "../AdminRenameUser/AdminRenameUser.php?$query";
$hrefDelete = "../AdminDeleteUser/AdminDeleteUser.php?$query";
?><tr class="user d-flex">
  <td class="username col-3"><?= $e($username) ?><?php if ( ($username === $currentUser) && ($institution === $currentInst)):?>
    <span class="badge badge-primary">You</span>
  <?php endif ?></td>
  <td class="institution col-2"><?= $e($institution) ?></td>
  <td class="is_admin col-2"><?= $isAdmin > 1 ? "Site" : ( $isAdmin ? "True" : "False" ) ?></td>
  <td class="buttons col-5">
    <a class="btn btn-outline-primary"   href="<?= $hrefReset ?>">Reset PW</a>
    <a class="btn btn-outline-secondary" href="<?= $hrefMote ?>"><?= $mote ?></a>
    <a class="btn btn-outline-secondary" href="<?= $hrefRename ?>">Rename</a>
    <a class="btn btn-outline-danger"    href="<?= $hrefDelete ?>">Delete</a>
  </td>
</tr>
