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

all:	build-dist

build-dist:
	if command -v bundle >/dev/null && command -v npm >/dev/null && command -v doxygen >/dev/null ; then make build-dist-locally ; else make build-dist-with-docker ; fi

build-dist-with-docker:
	docker run --rm -u $$(id -u):$$(id -g) -v $$(pwd):/work -w /work --name grima-build zemkat/grima-build make build-dist-locally

build-dist-locally: doxygen jekyll

build-docker-apache:
	rsync -a --delete dist/ docker/dist/
	rm -f dist/BUGS.* dist/TODO.* dist/grimas-* dist/standalone.sh
	cd docker && docker build -t zemkat/grima:apache -f ./Dockerfile-apache .
	cd docker && docker build -t zemkat/grima:kubernetes -f ./Dockerfile-kubernetes .
	rm -rf docker/dist

build-docker-builder:
	cd docker && docker build -t zemkat/grima-build -f ./Dockerfile-build .

jekyll:
	jekyll b
	cd dist ; ruby ../fix_urls.rb
	chmod og+rX -R dist
	mv dist/index.html dist/README-github.html
	mv dist/README.md  dist/README-github.md
	mv dist/README-site.html dist/index.html
	mv dist/README-site.md   dist/README.md

doxygen: docs/dev/index.html

docs/dev/index.html: grimas/grima-lib.php
	rm -rf docs/dev
	mkdir -p docs/dev
	doxygen > docs/dev/doxygen.log 2>&1
	advpng -z -4 $$(find docs/dev -iname '*.png')

tgz:
	mkdir -p releases
	TAGNAME=$$(git describe --dirty --tags) ; \
	mv dist "grima-$$TAGNAME" && \
	tar -zcf "releases/grima-$$TAGNAME.tgz" "grima-$$TAGNAME" && \
	mv "grima-$$TAGNAME" dist
