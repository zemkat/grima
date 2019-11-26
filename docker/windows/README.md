It may be more reasonable to have the grima launcher as .exe

This is basically the translation of docker.bat into in C# with the added
feature / complication that it tries to remember the API key without modifying
itself (so we could get a Windows developer key, and sign the file).

I think it's just too much trouble.

The makefile should run in Windows Subsystem for Linux (Ubuntu running in
Windows command prompt). Comment out the "RUN=" after "#Windows" to make it run
in Linux/Mac (runs mono in docker).

It has some additional features I wasn't sure how to add to the docker.bat
file:

* Checks if grima is already running
* Checks if docker is installed
* Allows specifying which docker image
* Remembers APIKEY, docker image, etc.
