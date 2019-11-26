
@rem Store the APIKEY directly in this file, no login needed
set apikey=EDITME
set server=https://api-na.hosted.exlibrisgroup.com

@rem Or use a database to store those two strings
set persist=%cd%\persist
set DATABASE_URL=sqlite:/tmp/grima/grima.sqlite

@rem No need to change this unless something else is already running on ZK
set port=19290

@rem No need to change this unless your institution has a different set of grimas
set image=zemkat/grima:apache

@if "%apikey%" == "EDITME" (
  echo "Please edit this file to provide an apikey, or delete the EDITME"
) else (
  docker pull %image%
  docker stop grima
  if "%apikey%" == "" (
	echo Using persistance database
	if not exist %persist% md %persist%
	docker run --detach --rm --name grima --publish %port%:19290 --volume %persist%:/tmp/grima --env DATABASE_URL=%DATABASE_URL% %image%
  ) else (
	echo Using environment variables
	docker run --detach --rm --name grima --publish %port%:19290 --env apikey=%apikey% --env server=%server% %image%
  )
  echo Opening your browser to "http://localhost:%port%"
  start http://localhost:%port%
)
pause
