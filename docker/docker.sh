#!/bin/sh

# Store the APIKEY directly in this file, no login needed
apikey=EDITME
server=https://api-na.hosted.exlibrisgroup.com

# Or use a database to store those two strings
persist=$(pwd)/persist
DATABASE_URL=sqlite:/tmp/grima/grima.sqlite

# No need to change this unless something else is already running on ZK
port=19290

# No need to change this unless your institution has a different set of grimas
image=zemkat/grima:apache

if [ "$apikey" = "EDITME" ]
then echo "Please edit this file to provide an apikey, or delete the EDITME"
else
  docker pull $image
  docker stop grima
  if [ -z "$apikey" ]
  then
	echo Using persistance database
	mkdir -m 1777 -p "$persist" &&
	docker run \
		--detach \
		--rm \
		--name grima \
		--publish $port:19290 \
		--user "$(id -u)" \
		--volume "$persist":/tmp/grima \
		--env DATABASE_URL=$DATABASE_URL \
		$image
  else
	echo Using environment variables
	docker run \
		--detach \
		--rm \
		--name grima \
		--publish $port:19290 \
		--user "$(id -u)" \
		--env apikey=$apikey \
		--env server=$server \
		$image
  fi
  echo Opening your browser to "http://localhost:$port"
  which xdg-open > /dev/null && xdg-open http://localhost:$port/ || open http://localhost:$port/
fi
