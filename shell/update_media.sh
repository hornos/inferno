#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_media.sh"
lockname="update_media"
video_gen="${INFERNO_PYTHON}/gen_video.py"


now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- media update will be skipped"
  exit 1
fi

/etc/rc.d/smbfs restart
if ! test $? -eq 0; then
  echo "${scname}: Samba FAILED"
  delete_lock ${lockname}
  exit 1
fi
echo "${scname}: Samba OK"


${video_gen}
if ! test $? -eq 0; then
  echo "${scname}: Video Generation FAILED"
  delete_lock ${lockname}
  exit 1
fi
echo "${scname}: Video Generation OK"


# suse specific
/etc/rc.d/smbfs stop
if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "update_dhcpd.sh: Finished at ${now}"

delete_lock ${lockname}
exit 0
