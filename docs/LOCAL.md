# Local installation of grima

If you are just setting up grima for yourself and are using linux or mac
(10.12+), then you likely already have all of the software grima depends on
installed. You can just install grima itself.

Just extract the grima-release.tar.gz whereever you want, and run the
start script [`standalone.sh`](../standalone.sh).

Open a web-browser to http://127.0.0.1:32900/ and it'll guide you through the 
[setup](SETUP.md).

In Ubuntu, you may need to:

    apt-get install php-xml php-sqlite3 php-curl

If you are using windows, you may need to install Docker to have a reasonable
local grima experience. This is not as well tested (and was difficult for the
developers to use). You'll probably want to install grima using:

    git clone https://github.com/zemkat/grima

rather than using a release. The [Makefile](../Makefile) in the git repository
contains recipes for running grima inside various Docker provided PHP
instances.
