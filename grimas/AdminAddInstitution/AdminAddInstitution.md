# AdminAddInstitution - add a new institution

Add a new institution to the grima login database. If this
is your first time running this grima, the database will
be created for you first.

At the same time you will be prompted to create the first user
account in that database. If you are the only user of this
grima install, that's you! This account will be able to create
[new user](../AdminAddUser/AdminAddUser.html)
accounts, and 
[assign them to be admins](../AdminAdminUser/AdminAdminUser.html)
(who can make accounts) or not.

This grima will be called automatically if you try to run any
grima without a database being set up.

Everyone in a grima institution will be using the same API key,
so if you want people to be able to run grima with different
keys (say, to have different permissions) you may want to create
and maintain multiple institutions with different keys.

## Input
* Institution: like *University of Kentucky* or *UK*
* API Key: from Ex Libris (if you don't have one, follow the [instructions here](SETUP.html))
* Admin Username: username for the admin of this grima install
* Admin Password: password for the admin of this grima install
* Server: choose between North America and Europe

After this first user is set up, you can add more users
using [AdminAddUser](../AdminAddUser/AdminAddUser.html).

## API requirements
* (none)
