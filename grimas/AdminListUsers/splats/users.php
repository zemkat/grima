<table class="list-users table">
  <tr class="header d-flex">
    <th class="col-3">Username</th>
    <th class="col-2">Institution</th>
    <th class="col-2">Is Admin?</th>
    <th class="col-5">Buttons</th>
  </tr>
<?php foreach ($users as $user) { 
$t('user',$user);
} ?>
</table>
<a class="btn btn-success" href="../AdminAddUser/AdminAddUser.php?institution=<?=$e($currentInst)?>&redirect_url=../AdminListUsers/AdminListUsers.php">Add New User</a>
<?php if ($isSiteAdmin): ?>
<a class="btn btn-success" href="../AdminAddInstitution/AdminAddInstitution.php">Add New Institution</a>
<?php endif ?>
