<?php

class WA_ListHostForm extends WA_FormObject {
    public function __construct( $id = 'lst_host', $title = 'List Host', $module = 'lst_host', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat. 
				 A név mező <a href="http://www.regular-expressions.info/reference.html">szabályos kifejezéseket</a> (regular expressions) is elfogad. A bal oldali 
				 mezőkkel a találatok rendezésének módját határozhatod meg. 
				 </div>';

  
		// FORM
		// $this->setDescription( $descr );
		if( ! isset( $this->faculty_arr ) ) {
		  $this->faculty_arr = array( array( '.*', 'GTK', 'TTK', 'VIK', 'ÉPK', 'VBK', 'GPK', 'ÉMK', 'KSK' ) );
		}
		// OBJECTS
		// sql bind: name
		
		$name = new WA_NameInput( 'name', 'Név:', false );
		// $name->set_init_val( 0, '.*');
		// $name->set_init_val( 1, '.*');
		$name->set_init_val( 0, '');
		$name->set_init_val( 1, '');

		$name->fields[0]->validate_type = 'general';
		$name->fields[1]->validate_type = 'general';
		$name->setLabelWidth( '150px' );
		$name->setLabelAlign( 'right' );
		$name->setLabelClass( 'item_label' );		

		$sort_name = new WA_CheckboxFieldObject( 'sort_name', true );
		$name_cont = new WA_ContainerObject( 'name_cont', 'horizontal' );
		$name_cont->putObjectArray( array( $sort_name, $name ) );

				
		$sex = new WA_GenderSelect( 'sex', 'M' );
		$sex->setLabelWidth( '150px' );
		$sex->setLabelAlign( 'right' );
		$sex->setLabelClass( 'item_label' );		
		$sex->setFieldLabelAlign( 'right' );
		$sex->setFieldLabelClass( 'radio' );	

		$sort_sex = new WA_CheckboxFieldObject( 'sort_sex', false );
		$sex_cont = new WA_ContainerObject( 'sex_cont', 'horizontal' );
		$sex_cont->putObjectArray( array( $sort_sex, $sex ) );


		$faculty = new WA_ComboObject( 'faculty', 'Kar:', $this->faculty_arr , array( 1 ) );
		$faculty->setLabelWidth( '150px' );
		$faculty->setLabelAlign( 'right' );
		$faculty->setLabelClass( 'item_label' );		
		$sort_faculty = new WA_CheckboxFieldObject( 'sort_faculty', false );
		$faculty_cont = new WA_ContainerObject( 'faculty_cont', 'horizontal' );
		$faculty_cont->putObjectArray( array( $sort_faculty, $faculty ) );


		$class   = new WA_ComboObject( 'class', 'Évfolyam:', array( array( '.*', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'Phd 1', 'Phd 2', 'Phd 3' ) ), array( 1 ) );
		$class->setLabelWidth( '150px' );
		$class->setLabelAlign( 'right' );
		$class->setLabelClass( 'item_label' );		

		$sort_class = new WA_CheckboxFieldObject( 'sort_class', false );
		$class_cont = new WA_ContainerObject( 'class_cont', 'horizontal' );
		$class_cont->putObjectArray( array( $sort_class, $class ) );


		$roomid = new WA_ComboObject( 'roomid', 'Szobaszám:', array( array( '.', 'A', 'B', 'C' ), 
			  array( '.', 0, 1, 2, 3, 4 ), array( '..', '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14' ) ), array( 1, 1, 1 ) );
		$roomid->setLabelWidth( '150px' );
		$roomid->setLabelAlign( 'right' );
		$roomid->setLabelClass( 'item_label' );		
		$roomid->sql_merge = true;
		$sort_roomid = new WA_CheckboxFieldObject( 'sort_roomid', false );
		$roomid_cont = new WA_ContainerObject( 'roomid_cont', 'horizontal' );
		$roomid_cont->putObjectArray( array( $sort_roomid, $roomid ) );


		$hostname = new WA_InputObject( 'hostname', 'Gépnév:', array( 'general' ), array( false ), array( 30 ) );
		// $hostname->set_init_val( 0, '.*');
		$hostname->set_init_val( 0, '');

		$hostname->setLabelWidth( '150px' );
		$hostname->setLabelAlign( 'right' );
		$hostname->setLabelClass( 'item_label' );		

		$sort_hostname = new WA_CheckboxFieldObject( 'sort_hostname', true );
		$hostname_cont = new WA_ContainerObject( 'hostname_cont', 'horizontal' );
		$hostname_cont->putObjectArray( array( $sort_hostname, $hostname ) );


		$ip = new WA_IPInput( 'ip', 'IP:', false );
		$ip->setLabelWidth( '150px' );
		$ip->setLabelAlign( 'right' );
		$ip->setLabelClass( 'item_label' );	
		$ip->fields[0]->validate_type = 'general';
		$ip->set_init_val( 0, '' );
		$ip->fields[1]->validate_type = 'general';		
		$ip->set_init_val( 1, '' );
		$ip->fields[2]->validate_type = 'general';		
		// $ip->set_init_val( 2, '.*' );
		$ip->set_init_val( 2, '' );

		$ip->fields[3]->validate_type = 'general';		
		// $ip->set_init_val( 3, '.*' );
		$ip->set_init_val( 3, '' );
		
		$ip->sql_merge = true;
		$ip->sql_merge_sep = '.';
//		$ip->sql_prefix = '^';
//		$ip->sql_postfix = '...$';
		

		$sort_ip = new WA_CheckboxFieldObject( 'sort_ip', true );
		$ip_cont = new WA_ContainerObject( 'ip_cont', 'horizontal' );
		$ip_cont->putObjectArray( array( $sort_ip, $ip ) );

		$mac = new WA_MACInput( 'mac', 'MAC:', false );
		$mac->setLabelWidth( '150px' );
		$mac->setLabelAlign( 'right' );
		$mac->setLabelClass( 'item_label' );	
		$mac->set_sqlid( 'mac' );
		$mac->sql_merge = true;
		$mac->sql_merge_sep = ':';
		foreach( range( 0, 5 ) as $f ) {
		  $mac->set_conv( $f, array( 'strtolower' ) );
		  $mac->fields[$f]->validate_type = 'general';
		}
		$mac->set_init_val( '..:..:..:..:..:..' );


		$sort_mac = new WA_CheckboxFieldObject( 'sort_mac', false );
		$mac_cont = new WA_ContainerObject( 'mac_cont', 'horizontal' );
		$mac_cont->putObjectArray( array( $sort_mac, $mac ) );


		$port = new WA_InputObject( 'port', 'Port:', array( 'general' ), array( false ), array( 10 ) );
		// $port->set_init_val( 0, '.*');
		$port->set_init_val( 0, '');
		
		$port->setLabelWidth( '150px' );
		$port->setLabelAlign( 'right' );
		$port->setLabelClass( 'item_label' );		
		$sort_port = new WA_CheckboxFieldObject( 'sort_port', true );
		$port_cont = new WA_ContainerObject( 'port_cont', 'horizontal' );
		$port_cont->putObjectArray( array( $sort_port, $port ) );


		// Get vlan list
		$db = WA_Session::init_db();
		$query = 'SELECT * FROM '.$_SESSION['VLAN_TABLE'].' ORDER BY vl_id';
		try {
		  $rs = $db->Execute( $query );
		}
		catch( exception $e ) {
		  print_query_error( 'Error: ', $e );
		  return;
		}
		if( $rs->RecordCount() < 1 ) {
		  print_info( '2 Nincs a keresésnek megfelelő találat az adatbázisban!' );
		  return;
		}
		
		$clist = array();
		foreach( $rs as $row ) {
		  $item = sprintf( "%4d -- %-20.20s -- %20.20s", $row['vl_id'], $row['vl_net'], $row['vl_name'] );
		  $clist[$row['vl_id']] = $item;
		}
		$vlan = new WA_HashComboObject( 'vlan', 'VLAN:', array( $clist ), array( 112 ) );
		$db->close();
		// Get vlan list end
		$vlan->setLabelWidth( '150px' );
		$vlan->setLabelAlign( 'right' );
		$vlan->setLabelClass( 'item_label' );		
		$sort_vlan = new WA_CheckboxFieldObject( 'sort_vlan', false );
		$vlan_cont = new WA_ContainerObject( 'vlan_cont', 'horizontal' );
		$vlan_cont->putObjectArray( array( $sort_vlan, $vlan ) );


		$opts_colors  = new WA_CheckboxObject( 'opts_colors', 'Színek', true );
		$opts_colors->setLabelWidth( '150px' );
		$opts_colors->setLabelAlign( 'right' );
		$opts_colors->setLabelClass( 'item_label' );		

		$opts_expired = new WA_CheckboxObject( 'opts_expired', 'Lejárt', false );
		$opts_expired->setLabelWidth( '80px' );
		$opts_expired->setLabelAlign( 'right' );
		$opts_expired->setLabelClass( 'item_label' );		

		$opts_wifi = new WA_CheckboxObject( 'opts_wifi', WA_Session::path_img( 'wifi.png' ), false );
		$opts_wifi->setLabelWidth( '80px' );
		$opts_wifi->setLabelAlign( 'right' );
		$opts_wifi->setLabelClass( 'item_label' );		

		$opts_hidden = new WA_CheckboxObject( 'opts_hidden', WA_Session::path_img( 'hidden.png' ), false );
		$opts_hidden->setLabelWidth( '80px' );
		$opts_hidden->setLabelAlign( 'right' );
		$opts_hidden->setLabelClass( 'item_label' );		

		$opts_valid = new WA_CheckboxObject( 'opts_valid', 'Tiltott', false );
		$opts_valid->setLabelWidth( '80px' );
		$opts_valid->setLabelAlign( 'right' );
		$opts_valid->setLabelClass( 'item_label' );		

		$opts_cont   = new WA_ContainerObject( 'opts_cont', 'horizontal' );
		$opts_cont->putObjectArray( array( $opts_colors, $opts_expired, $opts_valid, $opts_wifi ) );
	
		$button  = new WA_ButtonObject( 'button', 'Keres', '' );
  
		// BUILD FORM

		
		$objarr = array( $name_cont, $sex_cont, $faculty_cont, 
						 $class_cont, $roomid_cont, $hostname_cont, 
						 $ip_cont, $mac_cont, $port_cont, $vlan_cont, new WA_LabelObject( 'gap1', '&nbsp;' ), 
						 $opts_cont, new WA_LabelObject( 'gap2', '&nbsp;' ), $button );

		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$sex->setFieldLabelWidth( '50px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }


  public function genQuery( $mtype ) {
	$ptype = 'guest';
	if( isset( $this->opts['ptype'] ) ) {
	  $ptype = $this->opts['ptype'];
	}
	$table = $_SESSION['PERSON_TABLE'];

	$this->limit = 1000;
	if( isset( $this->opts['limit'] ) ) {
	  $this->limit = $this->opts['limit'];
	}

	// construct query
	$first = false;
	$first_order = true;
	if( $mtype != 'nopid' ) {
	  $query_header = WA_Session::select_string( array( 'pid', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'faculty', 
							'class', 'enrollyear', 'email', 'msn', 'skype',
							'mid', 'hostname', 'ip4', 'mac', 'port', 'vl_id', 'eap', 'mtype',
							'valid', 'hidden', 'dhcp', 'dns', 'wifi', 'expires' ), $table );
	}
	else {
	  $table = $_SESSION['HOST_TABLE'];
	  $query_header = WA_Session::select_string( array( 'rr_hinfo_txt', 'mid', 'hostname', 'ip4', 'mac', 'port', 'vl_id', 'eap', 'mtype',
							'valid', 'hidden', 'dhcp', 'dns', 'wifi', 'expires' ), $table );	
	}
	
	if( $mtype != 'nopid' ) {
	  $query_footer  = ' INNER JOIN '.$_SESSION['HOST_TABLE'].' using (pid)';
	  $query_footer .= ' WHERE mtype = '.WA_String::sqlfmt( $mtype );
	// $query_footer .= ' WHERE ';
	}
	else {
	  $query_footer  = ' WHERE mtype = '.WA_String::sqlfmt( $mtype );	
	}

	if( $mtype != 'nopid' ) {	
	  // name	
	  $o = $this->getChildObject( 'name_cont', 'name' );
	  if( WA_String::nzstr( $o->getForname() ) ) {
		if( ! $first ) { $query_footer .= " AND"; }
		$first = false;
		$query_footer .= " (name).forname ~* ".WA_String::sqlfmt( $o->getForname() );
	  }

	  if( WA_String::nzstr( $o->getSurname() ) ) {
		if( ! $first ) { $query_footer .= " AND"; }
		$first = false;
		$query_footer .= " (name).surname ~* ".WA_String::sqlfmt( $o->getSurname() );	  
	  }
	  // name end

	  $o = $this->getChildObject( 'sex_cont', 'sort_sex' );
	  if( $o->checked ) {
		if( ! $first ) { $query_footer .= " AND"; }
		$first = false;

		$o = $this->getChildObject( 'sex_cont', 'sex' );
		$query_footer .= " sex ~ ".WA_String::sqlfmt( $o->selected );	  
	  }

	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	
	  $o = $this->getChildObject( 'faculty_cont', 'faculty' );
	  $query_footer .= " faculty ~ ".WA_String::sqlfmt( $o->get_val() );	  
	
	  $o = $this->getChildObject( 'class_cont', 'class' );
	  $query_footer .= " AND class ~ ".WA_String::sqlfmt( $o->get_val() );	  

	  $o = $this->getChildObject( 'roomid_cont', 'roomid' );
	  // $query_footer .= " AND roomid ~ ".WA_String::sqlfmt( $o->get_val() );
	  $query_footer .= " AND roomid ~ ".$o->genQueryFooter();	  

	}
	else {
	  // name	
	  $o = $this->getChildObject( 'name_cont', 'name' );
	  if( WA_String::nzstr( $o->getForname() ) ) {
		if( ! $first ) { $query_footer .= " AND"; }
		$first = false;
		$query_footer .= " ( rr_hinfo_txt ~ ".WA_String::sqlfmt( $o->getForname() );
		$query_footer .= " OR rr_hinfo_txt ISNULL )";
	  }	
	}
	
	$o = $this->getChildObject( 'hostname_cont', 'hostname' );
	if( WA_String::nzstr( $o->get_val() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " hostname ~* ".WA_String::sqlfmt( $o->get_val() );	  
	}

	$o = $this->getChildObject( 'ip_cont', 'ip' );
	if( WA_String::nzstr( $o->genQueryFooter() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " ip4::varchar ~ ".$o->genQueryFooter();	  
	}

	$o = $this->getChildObject( 'mac_cont', 'mac' );
	if( WA_String::nzstr( $o->genQueryFooter() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " mac::varchar ~ ".$o->genQueryFooter();	  
	}

	$o = $this->getChildObject( 'port_cont', 'port' );
	if( WA_String::nzstr( $o->get_val() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " port ~ ".WA_String::sqlfmt( $o->get_val() );	  
	}

	$o  = $this->getChildObject( 'vlan_cont', 'vlan' );
	$so = $this->getChildObject( 'vlan_cont', 'sort_vlan' );
	if( WA_String::nzstr( $o->get_val() ) and $so->checked ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " vl_id = ".WA_String::sqlfmt( $o->get_selected() );	  
	}

	$o = $this->getChildObject( 'opts_cont', 'opts_expired' );
	if( $o->isChecked() ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " expires <= ".WA_String::sqlfmt( date( "Y-m-d" ) );
	}

	$o = $this->getChildObject( 'opts_cont', 'opts_wifi' );
	if( $o->isChecked() ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " wifi = 't'";
	}

	$o = $this->getChildObject( 'opts_cont', 'opts_valid' );
	if( $o->isChecked() ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " valid = 'f'";
	}


	$o = $this->getChildObject( 'faculty_cont', 'sort_faculty' );
	if( $o->checked and $mtype != 'nopid' ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " faculty";
	}

	$o = $this->getChildObject( 'class_cont', 'sort_class' );
	if( $o->checked and $mtype != 'nopid'  ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " class";
	}

	$o = $this->getChildObject( 'roomid_cont', 'sort_roomid' );
	if( $o->checked and $mtype != 'nopid'  ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " roomid";
	}

	$o = $this->getChildObject( 'hostname_cont', 'sort_hostname' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " hostname";
	}

	$o = $this->getChildObject( 'ip_cont', 'sort_ip' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " ip4";
	}

	$o = $this->getChildObject( 'mac_cont', 'sort_mac' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " mac";
	}

	$o = $this->getChildObject( 'port_cont', 'sort_port' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " port";
	}

	$o = $this->getChildObject( 'vlan_cont', 'sort_vlan' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " vl_id";
	}

	$o = $this->getChildObject( 'name_cont', 'sort_name' );
	if( $o->checked and $mtype != 'nopid'  ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " (name).forname, (name).surname";
	}
	else {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " rr_hinfo_txt";
	}

	if( isset( $this->limit ) ) {
	  $query_footer .= ' LIMIT '.$this->limit;
	}
	else {
	  $query_footer .= ' LIMIT '.$_SESSION['QUERY_LIMIT'];	
	}

	
	$query = $query_header.$query_footer;

	$this->query_footer = $query_footer;
	$this->query_header = $query_header;
	$this->query = $query;
	// echo "<br>".$query;
  }



  public function showresults( $rs, $mtype = 'pid' ) {
	$iscol = $this->getChildObject( 'opts_cont', 'opts_colors' );

	if( $mtype != 'nopid' ) {
	  $rstable = new WA_ModifyTable( 'results', array( 'Töröl' => array( 'action' => 'del',
																		   'action_title' => 'Töröl',
																		   'action_icon' => 'delete.png',
																		   'action_url' => $_SESSION['INDEX'].'?m=del_host&ptype=pid' ),
														'Módosít' => array( 'action' => 'upd',
																		   'action_title' => 'Módosítás',
																		   'action_icon' => 'edit.png',
																		   'action_url' => $_SESSION['INDEX'].'?m=sel_user' ),				   
														'Gépnév' => 'hostname', 'IP' => 'ip4',
														'Név' => array( 'type' => 'group', 'group' => array( 'forname', 'surname' ) ),
														'Port' => 'port', 'VLAN' => 'vl_id', 'EAP' => 'eap',
														'Lejár' => 'expires',
														'Tulajdonságok' => array( 'type'  => 'img', 
																				  'group' => array( 'wifi' => 'wifi.png',
																									'dns'  => 'dns.png', 
																									'dhcp' => 'dhcp.png',
																									'hidden' => 'hidden.png' ) ) ),
																			   
												   array( '60px', '60px', '130px', '130px', '180px', 
												  		  '60px', '50px', '100px', '100px', '180px' ), $rs,
												   array( 'colors' => $iscol->isChecked(), 'ptype' => $mtype ) );
	}
	else {
	  $rstable = new WA_ModifyTable( 'results', array(  'Törl' => array( 'action' => 'del',
																		   'action_title' => 'Töröl',
																		   'action_icon' => 'delete.png',
																		   'action_url' => $_SESSION['INDEX'].'?m=del_host&ptype=nopid' ),
														'Módosít' => array( 'action' => 'upd',
																		   'action_title' => 'Módosítás',
																		   'action_icon' => 'edit.png',
																		   'action_url' => $_SESSION['INDEX'].'?m=sel_vlan&action=sel_vlan&ptype=nopid' ),
														'Gépnév' => 'hostname', 'IP' => 'ip4',
														'Info' => 'rr_hinfo_txt',
														'Port' => 'port', 'VLAN' => 'vl_id', 'EAP' => 'eap',
														'Lejár' => 'expires',
														'Tulajdonságok' => array( 'type'  => 'img', 
																				  'group' => array( 'wifi' => 'wifi.png',
																									'dns'  => 'dns.png', 
																									'dhcp' => 'dhcp.png',
																									'hidden' => 'hidden.png' ) ) ),
																			   
												   array( '60px', '60px', '130px', '130px', '180px', 
												  		  '60px', '50px', '100px', '100px', '180px' ), $rs,
												   array( 'colors' => $iscol->isChecked(), 'ptype' => $mtype ) );	
	}	  
	$rstable->genHTML();
  } 

  public function showcnames( $db ) {
	$query  = 'SELECT mid, hostname from '.$_SESSION['HOST_TABLE'];
	$query .= ' ORDER BY hostname';

	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  // print_error( 'SQL hiba történt, fordulj a rendszergazdához segítségért!' );
	  print_query_error( 'Error: ', $e );
	  return;
	}

	print_info( 'Cname-ek' );

	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  return;
	}

	echo '<table><tr>';
	echo '<td class="td_table_header"><div class="table_title">Gépnév</div></td>';
	echo '<td class="td_table_header"><div class="table_title">Cnames</div></td></tr>';
	
	foreach( $rs as $row ) {  
	  $hquery  = 'SELECT * FROM '.$_SESSION['RECORD_TABLE'];
	  $hquery .= ' WHERE mid = '.WA_String::sqlfmt($row['mid']);
	  $hquery .= ' AND rec_type = \'CNAME\'';
	  
	  try {
		$hrs = $db->Execute( $hquery );
	  }
	  catch( exception $e ) {
		print_query_error( 'Error: ', $e );
		return;
	  }

	  if( ! $hrs->RecordCount() < 1 ) {
		echo '<tr>';
		echo '<td class="cname_hn">'.$row['hostname'].'</td>';
		echo '<td class="cname_rec">';

		$first = true;
		foreach( $hrs as $hrow ) {
		  if( ! $first ) {
			echo ', ';
		  }
		  echo $hrow['rec_hostname'];
		  $first = false;
		}
	  }
	  echo '</td></tr>';
	}
	
	echo '</table>';
  }




  public function query( $db ) {
	$mtypes = array( 'pid' => 'Normál felhasználók', 'nopid' => 'Felhasználó nélküli gépek' );
	
	foreach( $mtypes as $k => $desc ) {
	  $this->genQuery( $k );
	
	  // User Check
	  try {
		$rs = $db->Execute( $this->query );
	  }
	  catch( exception $e ) {
		// print_error( 'SQL hiba történt, fordulj a rendszergazdához segítségért!' );
		print_query_error( 'Error: ', $e );
		return;
	  }
	
	  print_info( $desc );
	  if( $rs->RecordCount() < 1 ) {
		print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  }
	  else {
		$this->showresults( $rs, $k );
	  }
	}
	
	$this->showcnames( $db );
	
  }
}
?>
