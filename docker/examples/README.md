# Cloud examples

This directory contains configurations for various container orchestrations
and cloud environment deployments.

Note that grima is very lightweight (10MB disk, most of it documentation,
network traffic is small amounts of text based API calls, processing is
minimal; one could easily host 20 simultaneous librarians on a single core).

In other words, there is absolutely no need to use any of this unless you
already deploy all web-apps using something like this.

## Docker-Composer

These are probably unwise (docker-compose uses multiple cores on a single
computer, but apache already does that), but they give an idea of how
to use grima in a cloud environment.

To test them, run `docker-compose up` from within their directory, and
then browse to [your port 19290](http://127.0.0.1:19290).

* [plain](ComposerPlain/docker-compose.yml) -- **recommended** configuration
for a single local user or a private network. No authentication, APIKEY
stored in the docker.

* [mysql](ComposerMysql/docker-compose.yml) -- basic cloud native setup:
sessions are stored in redis, users are store in mysql.

* [postgres](ComposerPgsql/docker-compose.yml) -- same, but postgresql.

* [load balanced](ComposerTraefik/docker-compose.yml) -- same, but including a
load balancer (Traefik). Use `docker-compose up --scale grima=5` if you have hundreds
of librarians using grima simultaenously. Except, docker-compose only scales
on a single computer, so the plain container would have already worked just
as well.


