# docker.io/zemkat/grima-cloud

## Contents

This container includes the official Docker image `php:apache` with:

* core `debian` OS
* `apache` web-server
* `mod_php` to serve php inefficiently (sorry, its default)

Some compiled in extensions for PHP:

* `redis` for server-side session storage
* `mysql` for user databases
* `postgres` alternative for user databases

Some adjustments to the basic config:

* [ports.conf](ports.conf) which makes the apache port runtime configurable
* [vhost.conf](vhost.conf) which is a very simple host

And the grima code itself.

## Customization

See the [documentation](../../docs/CLOUD.md) for meaningful configurations
of the environment variables.

## Multi-arch

Just for fun, this image is built for `amd64` (standard desktop and server
hardware) as well as `arm64v8` (higher end arm servers from $50 and up) and
`arm32v7` (such as the raspberry pi2,3,4 around $20). In the future it will
also contain `arm32v6` (for the raspberry pi0 for $5).

Docker should pull the correct image.
