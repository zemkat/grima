# Building your own grima releases

This document is for how to send new grimas out to your colleagues.
If the grima is generally useful, feel free to ask Kathryn if it
can be included in the (approximately quarterly) release of Grima.

If you just want to write a grima for yourself, see the [new author
instructions](NEWAUTHOR.md).

From now on, this document will assume you have written some grimas
for several people to use, and you want them to have access to these
grimas.

For now, just store the grimas in the standard place,
`grimas/SpecificGrima/SpecificGrima.php` etc. Perhaps later, a better packaging
system will be devised.

This means you want to package up the whole grima release. Your next
steps depend on how your users access grima. Each type of deployment
is described in turn. At the end is a description of `make build-docs`.

## Simple 20th century web-server

If your deploy to your users using the `tgz` file from github, then easy-peasy:

### Pre-requisities

Command line access to `docker` and `tar`

### Build

`make tgz` from the top of the grima directory will create a `tgz` file in
`releases`.

### Deploy

Just unpack the directory as usual in some place that is web-accessible and
with php activated.

## Desktop

The desktop version is fairly complicated (it packages a web-server and an
internet along with grima) but the build instructions are mostly easy.

### Pre-requisites

You'll need a Docker Hub account, and command line access to the docker
command, including `docker push`. If you want to keep your stuff private,
then you'll need to setup a private docker registry.

You'll need to edit the [Makefile](../Makefile) and change the name of
the image from `zemkat/grima-desktop` to `YourDockerHubUsername/grima-desktop`
or `YourPrivateRepositoryURL/YourDockerHubUsername/grima-desktop`. You
probably want to delete all the `zemkat/grima-cloud` lines.

You'll need to edit the
[grima-desktop.bat](../containers/desktop/grima-desktop.bat) or
[grima-desktop.sh](../containers/desktop/grima-desktop.sh) scripts and change
the name of the image, just like above.

### Build

```
make build-docs
make build-containers
```

If you removed the `grima-cloud` lines, it should complete in a minute or
two. Future builds only take a second.

### Deploy

`make push-dockers` and then ask the users to run
[grima-desktop.bat](../containers/desktop/grima-desktop.bat) or
[grima-desktop.sh](../containers/desktop/grima-desktop.sh) again.

This will automatically pull the image, stop the old grima server, and start
the new.

## Cloud server

In case you deploy using `docker-compose`, `docker swarm` (`docker stack`),
`kubernetes` or similar technologies, you'll need to create new images,
and cause your servers to update those images.

### Pre-requisities

An account on a docker registry, such as [the docker.io
registry](https://hub.docker.com/) or a private registry.

You'll need command line access to `docker push` and if you want multi-arch
support, `docker manifest`.

You'll need to edit the makefile to update the image name,
including registry.

You'll need to edit your config files to specify the new image name
(don't `apply` until you've finished building, of course).

### Build

```
make build-docs
make build-containers
```

The first time, it'll take about 15 minutes per arch (5 minutes for native) to
build the redis, mysql, and postgres plugins. Future builds take under a minute.

### Deploy

```
make push-containers
```

which you'll need to edit to fix the image name. You should probably include
any `kubectl apply` or `docker stack deploy` commands as well.

## make build-docs

This command runs `make run-doxygen` in order to pull comments from the main
library into web-pages. This is only useful for authors of new grimas and
developers. However, doxygen is pretty easy to install, and is included in the
docker image `zemkat/grima-docs-builder` which you shouldn't need to change.

It then runs `make run-jekyll` to convert markdown files (`.md`) into html.
This is need for the help buttons to work, so is recommended. This command
has the most prerequisities: `ruby`, `gem`, `bundle`, and `jekyll` (along
with all of its dependencies). It seems like many distributions have outdated
copies, and github gets upset if you use versions with security holes (which
seem irrelevant, as you are running this to build your own documentation).

To make this easier, unless the makefile detects you have everything installed,
it just uses a docker image to build the docs.

This docker image doesn't really need to be updated or built by you, but
it is a simple one-liner in the makefile if you want to change something.
I'll mention that bundler is required by jekyll, even in a global install.
