#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_dhcpd.sh"
lockname="update_dhcp"
python_gen="${INFERNO_PYTHON}/gen_dhcp.py"
shell_gen="${INFERNO_SHELL}/gen_dhcpd.conf.sh"
dhcp_dir="${INFERNO_DIR}/dhcp"


now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- dhcpd update will be skipped"
  exit 1
fi


### dual modified section start
if [[ "$(hostname)" == "purgatorio" ]]; then
  echo "SECONDARY INFERNO - PURGATORIO - $(hostname)"
else
  echo "PRIMARY INFERNO - PARADISO - $(hostname)"

  ${python_gen}
  if ! test $? -eq 0; then
    echo "${scname}: SQL FAILED"
    delete_lock ${lockname}
    exit 1
  fi
  echo "${scname}: SQL OK"

  ${shell_gen}
  if ! test $? -eq 0; then
    echo "${scname}: Build dhcpd.conf FAILED"
    delete_lock ${lockname}
    exit 1
  fi
  echo "${scname}: Build dhcpd.conf OK"

  purgatorio_pings=$(ping purgatorio -c 3 -q | grep transmitted | cut -d" " -f4)
  if [[ $purgatorio_pings > "2" ]]; then
    echo "PURGATORIO ONLINE"
    rsync -avz "${dhcp_dir}/dhcpd.conf" "root@purgatorio:${dhcp_dir}/dhcpd.conf"
    ssh "root@purgatorio" touch "${INFERNO_LOCK}/update_request_dhcp"
  else
    echo "PURGATORIO OFFLINE"
  fi

fi
### dual modified section end


if ! test -r "${dhcp_dir}/dhcpd.conf"; then
  echo "${scname}: Backup and Restart dhcpd server FAILED"
  delete_lock ${lockname}
  exit 1
fi
echo "${scname}: Backup and Restart dhcpd server OK"


echo -n "${scname}: "
cp -fv /etc/dhcpd.conf "${BACKUP_DHCP}/dhcpd.conf.${now}"
if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi


echo -n "${scname}: "
cp -fv "${dhcp_dir}/dhcpd.conf" /etc/dhcpd.conf
if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi

### dual modified section start
if [[ "$(hostname)" == "purgatorio" ]]; then
  echo "SECONDARY INFERNO - PURGATORIO - $(hostname)"
else
  echo "PRIMARY INFERNO - PARADISO - $(hostname)"

  # SuSe specific
  # echo "${scname}: Restarting the dhcpd server"
  # /etc/rc.d/dhcpd restart

  # Ubuntu specific
  echo "${scname}: Restarting the dhcpd server"
  /etc/init.d/dhcp3-server restart

fi
### dual modified section stop

if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi



now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "update_dhcpd.sh: Finished at ${now}"

delete_lock ${lockname}
exit 0