#!/bin/sh
useradd --password $1 $2
enter="\n"
passes=$1$enter$1
echo $passes   | passwd $2
