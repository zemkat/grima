# Installation guide for Grima

## Requirements

Grima is a collection of php scripts with minimal dependencies. A reasonably
modern version of php is required (one that is still supported by the PHP
developers for example), 5.6 and 7.x work fine. Older versions mostly work, but
are not as thoroughly tested.

The web server hosting Grima must support https, and Grima must be accessed
over https for authentication to work properly.

Grima must be run with Chrome, Firefox, or Safari; authentication will 
not work properly with Edge or Internet Explorer.

Grima requires libxml to parse Alma's replies, libcurl to make its API queries,
and PDO to make some very minimal database queries (for users and apikeys). The
default database provider is an sqlite3 file in `/tmp/grima`. Grima has been
tested with free PostGreSQL instances provided by Heroku as well.

On a Ubuntu server, you would use:

    apt-get install php-xml php-sqlite3 php-curl

If your user has their own linux or mac computer, you might consider whether
a [local install](LOCAL.md) might be simpler. If you have multiple users,
then grima should be hosted on a web-server. 

## Install

Unpack the latest release file (`grima-release.tgz`) into a web-accessible
directory:

`tar xzvf grima-release.tgz`

and then visit that site with your web browser, making sure to use https.

Follow the [setup](SETUP.md) instructions to specify an API key for Alma
and create a default administrative user.
