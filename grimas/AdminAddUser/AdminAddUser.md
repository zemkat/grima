# AdminAddUser - add a new user

Add a new user to the grima login database. If you are logged in as an admin,
user accounts you create will be added to that institution and use its API key.

Each person using grima should have their own account.

## Input
* Username: username for the account you are adding
* Password: password for the account you are adding

After adding a user, you can 
[promote them to admin](../AdminAdminUser/AdminAdminUser.html)
if you want them to also be able to make accounts (or 
[remove the privilege](../AdminDemoteUser/AdminDemoteUser.html)
if you change your mind).

When a user no longer needs access to grima, you can
[delete the account](../AdminDeleteUser/AdminDeleteUser.html).

You can [list all current users](../AdminListUsers/AdminListUsers.html)
as well.

## API requirements
* (none)
