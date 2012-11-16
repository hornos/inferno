#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_ping.sh"
lockname="update_ping"
ping_gen="${INFERNO_PYTHON}/gen_pingtest.py"


now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- ping update will be skipped"
  exit 1
fi

${ping_gen}
if ! test $? -eq 0; then
  echo "${scname}: Ping Generation FAILED"
  delete_lock ${lockname}
  exit 1
fi
echo "${scname}: Ping Generation OK"


now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "update_ping.sh: Finished at ${now}"

delete_lock ${lockname}
exit 0
