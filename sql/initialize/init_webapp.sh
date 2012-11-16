#!/bin/bash

. ./common.conf

function reinit_webapp() {
  sqlfile ./sql-webapp.sql
}

function reload_webapp() {

  sql_insert module_tbl "moduleid,descr" "'add_user', 'Felhasználó felvétele'"  
  sql_insert module_tbl "moduleid,descr" "'del_user', 'Felhasználó törlése'"  
  sql_insert module_tbl "moduleid,descr" "'mfy_user', 'Felhasználó módosítása'"  
  sql_insert module_tbl "moduleid,descr" "'lst_user', 'Felhasználók Listázása'"  
  sql_insert module_tbl "moduleid,descr" "'admin_db', 'Adatbázis adminisztráció'"  
  sql_insert module_tbl "moduleid,descr" "'sel_user', 'Host felvétele 1'"  
  sql_insert module_tbl "moduleid,descr" "'sel_vlan', 'Host felvétele 2'"  
  sql_insert module_tbl "moduleid,descr" "'net_info', 'Hálózati információk'"  
  sql_insert module_tbl "moduleid,descr" "'lst_host', 'Host information'"
  sql_insert module_tbl "moduleid,descr" "'del_host', 'Host information'"
  sql_insert module_tbl "moduleid,descr" "'mfy_host', 'Host information'"  
  sql_insert module_tbl "moduleid,descr" "'req_updt', 'Frissítés kérése'"  
  sql_insert module_tbl "moduleid,descr" "'lst_updt', 'Frissítés ellenőrzése'"  
  sql_insert module_tbl "moduleid,descr" "'lst_clog', 'Change log'"  


  sql_insert group_tbl "groupid,descr" "'admin', 'Administrators'"
  sql_insert group_tbl "groupid,descr" "'users', 'Normal users'"  

  sql_insert user_tbl "userid,groupid,passwd" "'admin', 'admin', 'not set'"  

  # Administrators
  sql_insert mauth_tbl "moduleid,groupid" "'add_user', 'admin'"    
  sql_insert mauth_tbl "moduleid,groupid" "'del_user', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'mfy_user', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'lst_user', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'admin_db', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'sel_user', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'sel_vlan', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'net_info', 'admin'"
  sql_insert mauth_tbl "moduleid,groupid" "'lst_host', 'admin'"  
  sql_insert mauth_tbl "moduleid,groupid" "'req_updt', 'admin'"  
  sql_insert mauth_tbl "moduleid,groupid" "'lst_updt', 'admin'"  
  sql_insert mauth_tbl "moduleid,groupid" "'del_host', 'admin'"  
  sql_insert mauth_tbl "moduleid,groupid" "'mfy_host', 'admin'"  
  sql_insert mauth_tbl "moduleid,groupid" "'lst_clog', 'admin'"  
  # Other
  sql_insert mauth_tbl "moduleid,groupid" "'lst_user', 'users'"
}

### MAIN

echo " Reloading webapp"
echo
reinit_webapp 1> /dev/null
reload_webapp 1> /dev/null
