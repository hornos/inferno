function ltrim(s) {
  sub(/^ */, "", s);
  return s
}
		  
function rtrim(s) {
  sub(/ *$/, "", s);
  return s
}
			  
function trim(s) {
  return rtrim(ltrim(s));
}

{
#  q1 = "./sql_insert.sh vlan_tbl \\\"vl_id,vl_name,vl_net,vl_mask,vl_bcast,vl_gw,vl_hfrom,vl_hto\\\"";
#  q2 = q1" \\\"\\'"trim($1)"\\',\\'"trim($2)"\\',\\'"trim($3)"\\',\\'"trim($4)"\\',\\'"trim($5)"\\',\\'"trim($6)"\\',\\'"trim($7)"\\',\\'"trim($8)"\\'\\\"";
#  print q2;

#  q1 = "./sql_insert.sh port_tbl \"name,device,port\"";
#  q2 = q1" \"'"trim($1)"','"trim($2)"','"trim($3)"'\"";
#  print q2;

# for .old
#  q1 = "./sql_insert.sh port_tbl \"ndev,port\"";
#  q2 = q1" \"'"trim($1)"','"trim($3)"'\"";

# new
  q1 = "./sql_insert.sh port_tbl \"ndev,port,id\"";
  q2 = q1" \"'"trim($1)"','"trim($2)"','"trim($3)"'\"";

  system( q2 );
}
