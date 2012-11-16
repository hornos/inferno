#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_radius.sh"
lockname="update_radius"
python_gen="${INFERNO_PYTHON}/gen_radius.py"

radius_dir="${INFERNO_DIR}/radius"

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"

create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- radius update will be skipped"
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

  purgatorio_pings=$(ping purgatorio -c 3 -q | grep transmitted | cut -d" " -f4)
  if [[ $purgatorio_pings > "2" ]]; then
    echo "PURGATORIO ONLINE"
    rsync -avz "${radius_dir}/users" "root@purgatorio:${radius_dir}/users"
    ssh "root@purgatorio" touch "${INFERNO_LOCK}/update_request_radius"
  else
    echo "PURGATORIO OFFLINE"
  fi
fi
### dual modified section end

if ! test -r "${radius_dir}/users"; then
  echo "${scname}: no valid users file FAILED"
  delete_lock ${lockname}
  exit 1
fi

echo -n "${scname}: "
cp -fv /etc/raddb/users "${BACKUP_RADIUS}/users.${now}"
if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi


echo -n "${scname}: "
chmod 0660 "${radius_dir}/users"
chown root:radiusd "${radius_dir}/users"
mv -fv "${radius_dir}/users" /etc/raddb/users
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
  # echo "${scname}: Restarting the radius server"
  # /etc/rc.d/radiusd restart

  # Ubuntu specific
  # echo "${scname}: Restarting the radius server"
  # /etc/init.d/freeradius restart

  # Source specific
  echo "${scname}: Restarting the radius server"
  /etc/init.d/radiusd restart

fi
### dual modified section stop


if ! test $? -eq 0; then
  delete_lock ${lockname}
  exit 1
fi

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Finished at ${now}"

delete_lock ${lockname}

exit 0
