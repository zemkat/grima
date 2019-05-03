#!/bin/sh

# store the keys plainly in this script
apikey=EDITME
server=https://api-na.hosted.exlibrisgroup.com

# or store them in a database
persist=$(pwd)/persist
DATABASE_URL=sqlite:/home/grima/perist/grima.sqlite

# Only need to change this if you have something else on port ZK
port=19290

if [ "$apikey" = "EDITME" ]
then echo "Please edit this script to provide an apikey, or delete the EDITME"
exit -1
elif [ -n "$apikey" ]
then
mkdir -m 1777 -p "$persist" &&
docker run \
  --detach \
  --rm \
  --name grima \
  --publish $port:19290 \
  --user "$(id -u)" \
  --volume "$persist":/home/grima/persist \
  --env DATABASE_URL=$DATABASE_URL \
  zemkat/grima
else
docker run \
  --detach \
  --rm \
  --name grima \
  --publish $port:19290 \
  --user "$(id -u)" \
  --env apikey=$apikey \
  --env server=$server \
  zemkat/grima
fi

which xdg-open > /dev/null && xdg-open http://localhost:$port/ || open http://localhost:$port/
