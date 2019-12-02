# Cloud examples

This directory contains configurations for various container orchestrations and
cloud environment deployments. The developer mostly uses a single unix server
running apache with an sqlite database for users and session files stored in
`/tmp`, but we have endeavored to test that these cloud configurations work.

Note that grima itself is very lightweight. The distribution is about 10MB, and
most of that space is screenshots for the documentation. The session and user
database for 5 to 10 librarians and staff is less than 1MB. Network traffic is
in response to human activity and is text based. Processing is fairly minimal.
Database I/O only happens on login/logout, Session I/O is a few hundred bytes
per page request, and there is no other disk I/O (assuming the source code is
kept in cache). One could easily handle 20 simultaneous librarians on a single
core and 512MB of ram.

This changes a little when using cloud technologies: the disk usage is about 50
times greater (450MB for docker image, 50MB for database volume). Memory usage
is not much higher. I/O is mostly the same (few hundred bytes per page request)
with the caveat that session I/O (once per page request) is now over the
internal network instead of (cached) disk I/O.

If you already use containers for web service deployment, then these example
configuration files should indicate how to install grima into your environment.

If you already have an apache server running, consider just untarring grima
in a directory where php hasn't been disabled. It should just work (link to
apache docs).

## Docker-Compose

These are meant to be short, simple, and usable. The first three succeed. The
fourth shouldn't be used as anything other than proof of concept (hopefully it
will be replaced by docker swarm and kubernetes configs).

To test them, run `docker-compose up` from within their directory, and
then browse to [your port 19290](http://127.0.0.1:19290).

* [Single User, no database, no
sessions](DockerComposeSingleUser/docker-compose.yml) -- **recommended**
configuration for a single local user or a private network. No authentication
required or available. A single APIKEY is stored in the docker via environment
variable, and anyone using this grima will use that APIKEY.

* [MySQL for Users, encrypted sessions](DockerComposeMysql/docker-compose.yml) --
A user database manages initial authentication, which sets an encrypted cookie
with the APIKEY and server. The PHP decrypts the cookie on each request
(minimal cpu cost) so that no session data is stored on the server.

* [PostgreSQL for Users, encrypted sessions](DockerComposePostgres/docker-compose.yml) --
Same but with postgres.

* [Mysql for Users, Redis for Sessions](DockerComposeMysqlRedis/docker-compose.yml) --
basic cloud native setup: sessions are stored in redis, users are store in
mysql. The environment variables in the `mysql` container create an empty
database and database user. Grima itself will create the tables. If no apikey
is available, all grimas will redirect to an AdminSetup page.

* [PostgreSQL for Users, Redis for Sessions](DockerComposePostgresRedis/docker-compose.yml) --
Same, but postgres.

* [Demonstrate Load Balancing](DockerComposeTraefik/docker-compose.yml) --
same, but including a load balancer (Traefik). Use `docker-compose up --scale
grima=5` if you have hundreds of librarians using grima simultaenously. Except,
docker-compose only scales on a single computer, so the plain container would
have already worked just as well.

## Docker Swarm

Should be nearly the same as compose. Make sure to test having the swarm over a
few different computers.

## Kubernetes

The docker compose files work with [Compose on
Kubernetes](https://github.com/docker/compose-on-kubernetes) but I'd like to
have some direct configuation examples.
