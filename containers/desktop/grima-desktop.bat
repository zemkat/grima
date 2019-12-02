
@rem Store the APIKEY directly in this file, no login needed
set apikey=EDITME
set server=https://api-na.hosted.exlibrisgroup.com

@rem No need to change this unless something else is already running on ZK
set port=19290

@rem No need to change this unless your institution has a different set of grimas
set image=zemkat/grima-desktop

@if "%apikey%" == "EDITME" (
  @echo "Please edit this file to provide an apikey
) else (
  @WHERE docker
  @if %ERRORLEVEL% NEQ 0 (
    @echo Please install docker first.  
    start https://docs.docker.com/docker-for-windows/install/
  ) else (
    docker pull %image%
    docker stop grima
    docker run --detach --rm --name grima --publish %port%:19290 --env apikey=%apikey% --env server=%server% %image%
    @echo Opening your browser to "http://localhost:%port%"
    start http://localhost:%port%
  )
)
pause
