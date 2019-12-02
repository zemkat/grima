#!/bin/bash
#
#	make a grima draft, using another as a template
#

if [ "$#" -ne 2 ]; then
    echo "usage $0 <base-on-this-grima> <new-grima-name>"
	exit
fi

if [ ! -d $1 ]; then
	echo "No such grima: $1"
	exit
fi

oldgrima=`echo $1 | sed -e 's!/$!!'`

newgrima=$2

mkdir -p $newgrima

pushd $oldgrima > /dev/null
files=`ls -p | grep -v /`
dirs=`ls -p | grep /$`
popd > /dev/null

for j in $files; do
	newname=`echo $j | sed -e "s!$oldgrima!$2!"`
	cp $oldgrima/$j $newgrima/$newname
done

for dir in $dirs; do
	cp -r $oldgrima/$dir $newgrima/$dir
done

sed -e "s!class $oldgrima extends GrimaTask!class $newgrima extends GrimaTask!" $oldgrima/$oldgrima.php |
	sed -e "s!$oldgrima::RunIt!$newgrima::RunIt!" > $newgrima/$newgrima.php

edit_for_sure="
$newgrima.php
$newgrima.xml
$newgrima.md
"

for j in $edit_for_sure; do
	echo XXX >> $newgrima/$j
done
