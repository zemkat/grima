########################################################################
##
##  Grima developer tools
##
##  "make run-doxygen" will translate comments in the code into webpages in
##  docs/dev
##
##  "make run-jekyll" will translate documentation in .md (markdown) format
##  to html in releases/built/
##
##  "make tgz" will create a tgz archive of releases/built/
##
##  "make containers" will create all the containers based on releases/built/
##

all:	build-docs

releases/built: build-docs

build-docs:
	if command -v ruby > /dev/null && command -v jekyll >/dev/null && command -v doxygen >/dev/null ; then make build-docs-locally ; else make build-docs-with-docker ; fi

build-docs-with-docker:
	docker run --rm -u $$(id -u):$$(id -g) -v $$(pwd):/work -w /work --name grima-docs-builder zemkat/grima-docs-builder make build-docs-locally

build-docs-locally: run-doxygen run-jekyll

run-doxygen: docs/dev/index.html

docs/dev/index.html: grimas/grima-lib.php docs/build/Doxyfile
	rm -rf docs/dev
	mkdir -p docs/dev
	doxygen docs/build/Doxyfile > docs/dev/doxygen.log 2>&1
	advpng -z -4 $$(find docs/dev -iname '*.png') || true # optionally compress doxygen images

run-jekyll:
	mv docs/build/assets docs/build/_layout docs/build/_sass .
	mv docs/README-*.md .
	cd docs/build && jekyll b
	mv assets _layout _sass docs/build/
	mv README-*.md docs
	cd releases/built/ && ruby ../../docs/build/fix_urls.rb
	chmod og+rX -R releases/built/
	rm -f releases/built/.htaccess
	mv releases/built/index.html releases/built/README-github.html
	mv releases/built/README.md  releases/built/README-github.md
	mv releases/built/README-dist.html releases/built/index.html
	mv releases/built/README-dist.md releases/built/README.md

tgz:
	TAGNAME=$$(git describe --dirty --tags) ; \
	rm -rf releases/built/doc/private ; \
	mv releases/built "grima-$$TAGNAME" && \
	tar -zcf "releases/grima-$$TAGNAME.tgz" "grima-$$TAGNAME" && \
	mv "grima-$$TAGNAME" releases/built

TAG=latest
build-containers: | releases/built
	mv releases/built containers/cloud/dist
	docker build -t zemkat/grima-cloud:$(TAG)-amd64 	./containers/cloud
	docker build -t zemkat/grima-cloud:$(TAG)-arm64v8 	./containers/cloud --build-arg 'phpImg=arm64v8/php:apache'
	docker build -t zemkat/grima-cloud:$(TAG)-arm32v7 	./containers/cloud --build-arg 'phpImg=arm32v7/php:apache'
	#docker build -t zemkat/grima-cloud:$(TAG)-arm32v6 	./containers/cloud --build-arg 'phpImg=arm32v6/php:fpm-alpine'
	mv containers/cloud/dist containers/desktop/
	docker build -t zemkat/grima-desktop:$(TAG)     	./containers/desktop
	mv containers/desktop/dist releases/built
	docker build -t zemkat/grima-docs-builder:$(TAG)       	./containers/docs-builder

push-containers:
	docker push zemkat/grima-cloud:$(TAG)-amd64
	docker push zemkat/grima-cloud:$(TAG)-arm64v8
	docker push zemkat/grima-cloud:$(TAG)-arm32v7
	#docker push zemkat/grima-cloud:$(TAG)-arm32v6
	docker manifest create zemkat/grima-cloud:$(TAG) zemkat/grima-cloud:$(TAG)-amd64 zemkat/grima-cloud:$(TAG)-arm64v8 zemkat/grima-cloud:$(TAG)-arm32v7 # zemkat/grima-cloud:$(TAG)-arm32v6
	docker manifest push --purge zemkat/grima-cloud:$(TAG)
	docker push zemkat/grima-desktop:$(TAG)
	docker push zemkat/grima-docs-builder:$(TAG)
