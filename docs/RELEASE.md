# Release process

The intended audience for this document are Grima developers. While Grima is
basically a bunch of quick scripts to help get work done, we do try to make
reasonable releases for new users to get started. Since Grima is just meant
to make things easier, how well it works depends heavily on how the user's
own workflow matches using grima.

Each type of release aims at a different type of workflow we've seen in our
users.

The releases are built on a unix computer. You don't need unix to use the
releases, but you'll need unix (including Linux, Mac, Windows Subsystem for
Linux) to create new releases yourself.

## Dist directory

This directory is more or less what you would put in an apache/php website. It
is available on the github page as a `tgz` file. This is only recommended if
you personally use a UNIX based webserver.  (Todo: cPanel integration)

However the files are used in the other stages, so you must make them.

1. Create the files in the dist directory.

`make build-dist-with-docker`

Alternatively, if you have `ruby`, `gem`, `bundler`, `node`, `npm`, and
`doxygen`, you can build it with your own tools. (Unix and Mac and Windows
Subsystem for Linux only)

`make build-dist-locally`

## Docker images

1. Build docker images:

`make build-docker-apache`

2. Test docker images:

`APIKEY=testkeyhere SERVER=api-na.hosted.exlibrisgroup.com ./docker/docker.sh`

3. Publish docker images:

`docker push zemkat/grima:apache`

## Github release

Go to the github website and type into the release. Include `docker/grima.bat`
and `docker/grima.sh` as release artifacts too.


## The clean commit technique

The github repository contains one commit per release. The actual develop
process is quite uneven and uneducational. Work must be done, a rush cataloging
request for 50,000 e-books, and a grima is born. Some feature of Alma is needed
for the first time, so more is added to `grimas/grima-lib.php`. It works, but
it is not pretty.

Before release, all the changes are examined and the good ones are kept. Hopefully
there is some extra polishing.

This is actually the first step of the real release.

### From a clean git checkout

`rsync` or `cp` all changed files into the clean checkout.

`git diff` to check that all changes are intended.

`git add .` to stage the changes.

`git commit` to commit the changes to the local version.

`git tag -a` to create a release announcement more or less.

`git push --follow-tags` to push the commit and the tag to github.

Then go to github and edit the release announcement again.
