#!/bin/sh

# Store the APIKEY directly in this file, no login needed
apikey=${apikey:-EDITME}
server=${server:-https://api-na.hosted.exlibrisgroup.com}

# No need to change this unless something else is already running on ZK
port=${port:-19290}

# No need to change this unless your institution has a different set of grimas
image=${image:-zemkat/grima-desktop}

if [ "$apikey" = "EDITME" ]
then echo "Please edit this file to provide an apikey, or delete the EDITME"
else
  if which docker >/dev/null 2>/dev/null
  then
    docker pull $image
    docker stop grima
    docker run \
      --detach \
      --rm \
      --name grima \
      --publish $port:19290 \
      --user "$(id -u)" \
      --env apikey=$apikey \
      --env server=$server \
      $image
    echo Opening your browser to "http://localhost:$port"
    which xdg-open > /dev/null && xdg-open http://localhost:$port/ || open http://localhost:$port/
  else
    echo "Please install docker first."
  fi
fi
