#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_auto.sh"
lockname="update_auto"

update_request="update_request"

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"
echo "Automatic update"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- auto update will be skipped"
  exit 1
fi


create_lock ${update_request}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- update request still active"
  exit 1
else
  print_in_lock ${update_request} "${now} auto REQUEST"
fi

create_lock "${update_request}_dhcp"
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- dhcp update request still active"
else
  print_in_lock "${update_request}_dhcp" "${now} auto"
fi


create_lock "${update_request}_dns"
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- dns update request still active"
else
  print_in_lock "${update_request}_dns" "${now} auto"
fi


create_lock "${update_request}_radius"
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- radius update request still active"
else
  print_in_lock "${update_request}_radius" "${now} auto"
fi


create_lock "${update_request}_c3550"
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- c3550 update request still active"
else
  print_in_lock "${update_request}_c3550" "${now} auto"
fi


now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Finished at ${now}"

delete_lock ${lockname}

exit 0
