#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_bind.sh"
lockname="update_dns"
python_gen="${INFERNO_PYTHON}/gen_dns.py"
shell_gen="${INFERNO_SHELL}/gen_bind_conf.sh"
dns_dir="${INFERNO_DIR}/dns"

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"

dns_files=("")


create_lock ${lockname}
if test $? -eq 0; then
  echo "${scname}: UNDER LOCK -- bind update will be skipped"
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
    echo "${scname}: Build bind configuration FAILED"
    delete_lock ${lockname}
    exit 1
  fi
  echo "${scname}: Build bind configuration OK"

  purgatorio_pings=$(ping purgatorio -c 3 -q | grep transmitted | cut -d" " -f4)
  if [[ $purgatorio_pings > "2" ]]; then
    echo "PURGATORIO ONLINE"
    rsync -avz "${dns_dir}/" "root@purgatorio:${dns_dir}"
    ssh "root@purgatorio" touch "${INFERNO_LOCK}/update_request_dns"
  else
    echo "PURGATORIO OFFLINE"
  fi

fi
### dual modified section end




etc_dir="/var/lib/named"
backup_dir="${BACKUP_DNS}/${now}"

echo -n "${scname}: "
mkdir -v "${backup_dir}"
if ! test $? -eq 0; then
  echo "${scname}: Backup ERROR"
else
  for i in ${dns_files[*]}; do
	zone="${INFERNO_DIR}/dns/${i}"
	
	if test -r ${zone}; then
  	  echo -n "${scname}: "	  
	  cp -fv "${etc_dir}/${i}" "${backup_dir}"
  	  echo -n "${scname}: "	  
	  cp -fv "${zone}" "${etc_dir}"
	else
	  echo "${scname}: "
	  delete_lock ${lockname}
	  exit 1
	fi
  done
  echo "${scname}: Backup and Configure the DNS server OK"
fi

### dual modified section start
if [[ "$(hostname)" == "purgatorio" ]]; then
  echo "SECONDARY INFERNO - PURGATORIO - $(hostname)"
else
  echo "PRIMARY INFERNO - PARADISO - $(hostname)"

  echo "${scname}: Restarting the bind server"
  # SuSE
  # /etc/rc.d/named restart

  # Ubuntu
  /etc/init.d/bind9 restart

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
