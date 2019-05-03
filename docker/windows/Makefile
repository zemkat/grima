all: grima-xml.exe
clean:
	rm -rf build

#linux
RUN=docker run \
	-it --rm \
	-v $$(pwd):/work \
	-w /work \
	-u $$(id -u) \
	-e HOME=/home \
	-v $$XAUTHORITY:/home/.Xauthority \
	-v /tmp/.X11-unix:/tmp/.X11-unix \
	-e DISPLAY=$$DISPLAY \
	mono mono
#windows
RUN=

CSC=./build/Microsoft.Net.Compilers.3.0.0/tools/csc.exe

$(CSC): | build/nuget.exe
	cd build && $(RUN) ./nuget.exe install Microsoft.Net.Compilers

build/nuget.exe: | build
	cd build && wget https://dist.nuget.org/win-x86-commandline/latest/nuget.exe && chmod 555 nuget.exe

build:
	mkdir -p build

grima-xml.exe: grima-xml.cs | $(CSC)
	$(RUN) $(CSC) -r:Microsoft.VisualBasic.dll $^

run: | grima-xml.exe
	$(RUN) grima-xml.exe
