# Containerization and docker support files
 
Grima can be deployed on desktop computers and on cloud servers using
containers. Its documentation is built inside a container. We use docker to
create the containers.

Most people using containers will just use the published containers:
`docker.io/zemkat/grima-desktop` and `docker.io/zemkat/grima-cloud` should just
work.

Windows and MacOS Desktop users should install Docker Desktop (requires
creating a free dockerhub account as it is still targeted at developers rather
than end users), and then run [grima-desktop.bat](desktop/grima-desktop.bat)
for Windows or [grima-desktop.sh](desktop/grima-desktop.sh) for MacOS. Linux
Desktop users install docker easily following [the official
instructions](https://docs.docker.com/install/) and using
[grima-desktop.sh](desktop/grima-desktop.sh).

Cloud / containerized deployments should check out the [example
configurations](cloud) to adapt it to your needs.

## How are the containers built?

### [Desktop dockerfile](desktop/Dockerfile)

This dockerfile takes the contents of dist and treats it like the main website
of an apache install (grima doesn't need to be the main website, but container
philosophy is that each container has 1 thing in it, and that 1 thing is this
website).

```Dockerfile
ARG phpImg=php:apache
FROM $phpImg
ENV APACHE_PORT=19290 DATABASE_URL=sqlite:/tmp/grima/grima.sql SESSION_MODULE=files SESSION_NAME=grima SESSION_PATH=/tmp/grima apikey= server=
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY ports.conf /etc/apache2/ports.conf
EXPOSE $APACHE_PORT
COPY dist /var/www/html
```

The environment variables can be adjusted in the Dockerfile, or more usefully,
in the script that runs the image, such as [grima-desktop.bat](desktop/grima-desktop.bat)
or [grima-desktop.sh](desktop/grima-desktop.sh).

* `apikey` and `server` -- grima requires this information somewhere. The simplest
configuration is a single APIKEY and Alma server specified in environment variables.
The "desktop" version of grima recommends this setup. An internal network accessible
only by authorized employees could also use this setup easily. There is no login,
no sessions, no users. Very simple.
* `SESSION_MODULE`, `SESSION_NAME`, `SESSION_PATH`, and `DATABASE_URL` -- the
more complicated way. In this setup, users will have to login to grima. 
* `APACHE_PORT` -- default is unprivileged to allow "rootless" containers. You
might change it to 80 if your orchestrator gives each container lots of
privileges and expects it to use standard ports.
* `phpImg` -- this can only be set at build-time. If you make your own images, and
want the php version locked down, you could change this. The PHP code has mostly
been version agnostic, so we weren't worried.

### [Cloud dockerfile](cloud/Dockerfile)

This dockerfile assumes you'll store users in a persistent database, possibly
storing sessions in a redis cluster. It is nearly the same as the desktop
version, except that it compiles in redis, mysql, and postgresql drivers. Its
environment variables still must be set before calling it.

```Dockerfile
ARG phpImg=php:apache
FROM $phpImg
ENV APACHE_PORT=19290 DATABASE_URL=mysql://grimauser:grimapass@grima-users/grimadb SESSION_MODULE=redis SESSION_NAME=grima SESSION_PATH=tcp://grima-sessions apikey= server=
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && apt-get update && apt-get install -y libpq-dev \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
  && rm -rf /var/lib/apt/lists/* && apt-get clean \
  && true
COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY ports.conf /etc/apache2/ports.conf
EXPOSE $APACHE_PORT
COPY dist /var/www/html
```

### [Documentation builder](docs-builder/Dockerfile)

This dockerfile tries to mimic github-pages, while also containing doxygen and
advancecomp.

```Dockerfile
FROM ubuntu:19.10

# basic packages
RUN apt-get update && apt-get install -y --no-install-recommends \
  advancecomp \
  build-essential \
  curl \
  doxygen \
  git \
  ruby-full \
  zlib1g-dev \
  && true && \
  gem install github-pages bundler \
  && rm -rf /var/lib/apt/lists/* && apt-get clean
```
