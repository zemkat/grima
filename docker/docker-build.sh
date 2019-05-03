docker build -t zemkat/grima -f ./Dockerfile ../dist
docker build -t zemkat/grima:php72 -f ./Dockerfile --build-arg phpImg=php:7.2 ../dist
docker build -t zemkat/grima:php71 -f ./Dockerfile --build-arg phpImg=php:7.1 ../dist
docker build -t zemkat/grima:php70 -f ./Dockerfile --build-arg phpImg=php:7.0 ../dist
