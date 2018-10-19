#!/bin/sh
useradd -m --password $1 $2
enter="\n"
passes=$1$enter$1
echo $passes   | passwd $2
sudo setquota $2 $3 $3 0 0 /

echo "@"$2 "hard" "nrproc" $4 | sudo tee --append /etc/security/limits.conf > /dev/null
