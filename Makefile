########################################################################
##
##  Grima developer tools
##
##  "make doxygen" will translate comments in the code into webpages in
##  docs/dev
##
##  "make jekyll" will translate documentation in .md (markdown) format
##  to html in dist/
##
##  "make tgz" will create a tgz archive of dist/ suitable for releasing on
##  github.
##

all: release

release: doxygen jekyll tgz

tgz: doxygen jekyll
	mkdir -p releases
	TAGNAME=$$(git describe --dirty --tags) ; \
	mv dist "grima-$$TAGNAME" && \
	tar -zcf "releases/grima-$$TAGNAME.tgz" "grima-$$TAGNAME" && \
	mv "grima-$$TAGNAME" dist

phar:
	cd dist && php -d phar.readonly=0 CreatePhar.php

.bundle/vendor/bundle:
	bundle install --path .bundle/vendor/bundle

node_modules:
	npm install

jekyll: | .bundle/vendor/bundle node_modules
	bundle exec jekyll b
	rm -f dist/.htaccess
	chmod og+rX -R dist
	node fix_urls.js 2>/dev/null
	mv dist/index.html dist/README-github.html
	mv dist/README.md  dist/README-github.md
	mv dist/README-site.html dist/index.html
	mv dist/README-site.md   dist/README.md

doxygen:
	rm -rf docs/dev
	mkdir -p docs/dev
	doxygen > docs/dev/doxygen.log 2>&1


########################################################################
##
##  Local testing
##
##  "make local" will run a small php server (on unix like systems)
##  Connect to http://127.0.0.1:32900/ to see it.
##
##  If you have docker installed, 
##  "make php72" will run php 7.2 on http://127.0.0.1:32972/
##  "make php56" will run php 5.6 on http://127.0.0.1:32956/
##

export DATABASE_URL=sqlite:$(PWD)/standalone-config.sql
HOST=127.0.0.1

php72:IMAGE=php:7.2
php71:IMAGE=php:7.1
php70:IMAGE=php:7.0
php56:IMAGE=php:5.6
php55:IMAGE=php:5.5
php54:IMAGE=php:5.4

local:PORT=32900
local:
	php -S $(HOST):$(PORT)

php%:PORT=329$*
php%:
	docker run \
		--user $$(id -u) \
		--workdir /work \
		--volume $(PWD):/work \
		--publish $(PORT):$(PORT) \
		--expose $(PORT) \
		-it \
		--rm \
		$(IMAGE) \
		php -S $(HOST):$(PORT)
