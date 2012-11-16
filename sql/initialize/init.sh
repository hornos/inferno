#!/bin/bash

. ./common.conf
. ../shell/common.conf


lockname="webapp.lock"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- bind update will be skipped"
  exit 1
fi

print_in_lock ${lockname} " INIT LOCK"

err="./init.error"
date > $err

### MAIN
echo "INIT DATABASE"
echo
echo -n "STAGE 1: "
./init_room.sh		2>> $err

echo -n "STAGE 2: "
./init_address.sh	2>> $err

echo -n "STAGE 3: "
./init_koli.sh		2>> $err

echo -n "STAGE 4: "
./init_webapp.sh	2>> $err

echo -n "STAGE 5: "
./init_host.sh		2>> $err

delete_lock ${lockname}

echo
echo "For errors type: cat $err"
# cat $err
