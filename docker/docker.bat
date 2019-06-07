@rem Store stuff directly
set apikey=EDITME
set server=https://api-na.hosted.exlibrisgroup.com

@rem Or use a database to store those two strings
set persist=%cd%\persist
set DATABASE_URL=sqlite:/home/grima/persist/grima.sqlite

@rem No need change this unless something else is already running on ZK
set port=19290

@if "%apikey%" == "EDITME" (
  echo "Please edit this bat file to provide an apikey or delete the EDITME"
) else (
  if "%apikey%" == "" (
	echo Using persistance database
	if not exist %persist% md %persist%
	docker.exe run --detach --rm --name grima --publish %port%:19290 --volume %persist%:/home/grima/persist --env DATABASE_URL=%DATABASE_URL% "zemkat/grima"
  ) else (
	echo Using environment variables
	docker.exe run --detach --rm --name grima --publish %port%:19290 --env apikey=%apikey% --env server=%server% "zemkat/grima"
  )
  echo Opening your browser to "http://localhost:%port%"
  start http://localhost:%port%
)
