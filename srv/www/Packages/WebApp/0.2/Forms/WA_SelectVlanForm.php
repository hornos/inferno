<?php

class WA_SelectVlanForm extends WA_FormObject {
    public function __construct( $id = 'sel_vlan', $title = 'List User', $module = 'sel_vlan', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title"></div>
				 <div class="help_text">
				 </div>';
  
		// FORM
		$this->setDescription( $descr );
		
		// Arguments
		if( isset( $this->opts['db'] ) ) {
		  $db = $this->opts['db'];
		}
		else {
		  print_info( 'Adatbázishiba!' );
		  return;
		}

		if( isset( $this->opts['ptype'] ) ) {
		  $ptype = $this->opts['ptype'];
		  $this->ptype = $ptype;
		}
		else {
		  return;
		}


		if( $ptype != 'nopid' ) {
		  if( isset( $this->opts['pid'] ) ) {
			$pid = $this->opts['pid'];
		  }
		  else {
			return;
		  }
		
		  $roomid = "";
		  if( isset( $this->opts['roomid'] ) ) {
			$roomid = $this->opts['roomid'];
		  }
		}

		if( isset( $this->opts['action'] ) ) {
		  $action = $this->opts['action'];
		}
		else {
		  return;
		}

		$vlanid = 112;
		if( isset( $this->opts['vlan'] ) ) {
		  $vlanid = $this->opts['vlan'];
		}

		if( $action == 'sel_ip' ) {
		  $portid = 1;
		  if( isset( $this->opts['port'] ) ) {
			$portid = $this->opts['port'];
		  }
		}

		$isupd = false;
		if( isset( $this->opts['mid'] ) ) {
		  $mid = $this->opts['mid'];
		  if( is_numeric( $mid ) ) {
			$uquery  = 'SELECT port, vl_id, mid, ';
			$uquery .= $_SESSION['HOST_TABLE'].'.hostname, ip4, mac, ';
			$uquery .= 'rr_hinfo_os, rr_hinfo_cpu, rr_hinfo_txt, comment, ';
			$uquery .= 'mtype, pid, valid, hidden, dns, dhcp, wifi, eap, expires, ';
			$uquery .= 'vl_name, vl_net, ndev, uport from '.$_SESSION['HOST_TABLE'];
			$uquery .= ' INNER JOIN '.$_SESSION['VLAN_TABLE'].' USING(vl_id)';
			$uquery .= ' INNER JOIN '.$_SESSION['PORT_TABLE'].' USING(port)';
			$uquery .= ' INNER JOIN '.$_SESSION['DEVICE_TABLE'].' ON ';
			$uquery .= $_SESSION['PORT_TABLE'].'.ndev = '.$_SESSION['DEVICE_TABLE'].'.name AND ';
			$uquery .= $_SESSION['DEVICE_TABLE'].'.name = '.$_SESSION['PORT_TABLE'].'.ndev';
			$uquery .= ' WHERE mid = '.WA_String::sqlfmt( $mid );
		
			try {
			  $updrs = $db->Execute( $uquery );
			}
			catch( exception $e ) {
			  echo $query;
			  print_query_error( 'Error: ', $e );
			  return;
			}
			if( ! $updrs->RecordCount() < 1 ) {
			  $updrow = $updrs->FetchRow();
			  // print_r( $updrow );
			  $isupd  = true;
			}
		  }
		}

		if( $ptype != 'nopid' ) {
		  // Get user info
		  $query = 'SELECT (name).forname, (name).surname, roomid from '.$_SESSION['PERSON_TABLE'].' WHERE pid = '.WA_String::sqlfmt( $pid );
		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print_query_error( 'Error: ', $e );
			return;
		  }
		  if( $rs->RecordCount() < 1 ) {
			print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
			return;
		  }
		  $row = $rs->FetchRow();
		  $name = new WA_NameInput( 'name', 'Név:', false );
		  $name->set_init_val( 0, $row['forname'] );
		  $name->set_init_val( 1, $row['surname'] );
		  $name->fields[0]->is_readonly = true;		
		  $name->fields[1]->is_readonly = true;
		  if( $isupd ) {
			$name->suffx->setLabel( 'TODO: old name' );
			$name->suffx->disable();	
		  }
		  // Get user info end
		}
		
		// Get vlan list
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
		$vlan = new WA_HashComboObject( 'vlan', 'VLAN:', array( $clist ), array( $vlanid ) );
		if( $isupd ) {
		  $oldvlan = sprintf( "&nbsp;%4d -- %-20.20s -- %20.20s", $updrow['vl_id'], $updrow['vl_net'], $updrow['vl_name'] );
		  
		  $vlan->suffx->setLabel( WA_String::suffx( $oldvlan ) );
		  $vlan->suffx->enable();
		}
		$link = 'javascript: document.'.$this->id.'.state.value = 1; document.'.$this->id.'.submit()';
		$vlan->setOnChange( 0, $link );			
		// Get vlan list end
		
		// Build ip list
		if( $action == 'sel_ip' ) {
		  // $ipaddr  = new WA_IPInput( 'ipaddr' );
		  // echo '<br> vlanid: '.$vlanid;
		  $query = 'SELECT * FROM '.$_SESSION['VLAN_TABLE'].' WHERE vl_id = '.WA_String::sqlfmt( $vlanid ). ' ORDER BY vl_id';
		
		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print_query_error( 'Error: ', $e );
			return;
		  }
		  if( $rs->RecordCount() < 1 ) {
			print_info( '3 Nincs a keresésnek megfelelő találat az adatbázisban!' );
			return;
		  }
		  $row = $rs->FetchRow();
		  
		  // build ex list
		  $vl_hfrom = $row['vl_hfrom'];
		  $vl_hto = $row['vl_hto'];
		  $vl_gw = $row['vl_gw'];
		  
		  $query  = 'SELECT hostname,ip4 FROM '.$_SESSION['HOST_TABLE'].' WHERE';
		  $query .= ' ip4 >= '.WA_String::sqlfmt( $vl_hfrom );
		  $query .= ' AND ip4 <= '.WA_String::sqlfmt( $vl_hto );
		  $query .= ' ORDER BY ip4';
		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print_query_error( 'Error: ', $e );
			return;
		  }
		  
		  $exlist = array();
		  if( $rs->RecordCount() > 0 ) {
			foreach( $rs as $row ) {
			  if( $isupd ) {
				if( $updrow['ip4'] != $row['ip4'] ) {
				  array_push( $exlist, $row['ip4'] );
				}
			  }
			  else {
				array_push( $exlist, $row['ip4'] );
			  }
			}
		  }
		  		  
		  array_push( $exlist, $vl_gw );
		  $iplist = WA_Session::gen_ipchkl( $vl_hfrom, $vl_hto, $exlist );
		  $fip = 1;
		  if( $isupd ) {
			$ipc = 1;
			foreach( $iplist as $ipl ) {
			  if( $ipl == $updrow['ip4'] ) {
				$fip = $ipc;
			  }
			  ++$ipc;
			}
			// echo '<br>old: '.$vlanid;
			// echo '<br>new: '.$updrow['vl_id'];
			if( $vlanid != $updrow['vl_id'] ) {
			  $fip = 1;
			}
		  }
		  // Build ip list end

		  // Build port list
		  $query  = 'SELECT port,ndev,uport FROM '.$_SESSION['PORT_TABLE'].' INNER JOIN '.$_SESSION['DEVICE_TABLE'];
		  $query .= ' ON '.$_SESSION['PORT_TABLE'].'.ndev = '.$_SESSION['DEVICE_TABLE'].'.name';
		  if( $ptype != 'nopid' ) {
			$query .= ' WHERE port ~ '.WA_String::sqlfmt( $roomid ).' OR port ~ \'ALL\' OR port ~ \'WIFI\'';
		  }
		  $query .= ' ORDER BY port';

		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print_query_error( 'Error: ', $e );
			return;
		  }
		  if( $rs->RecordCount() < 1 ) {
			print_info( '5 Nincs a keresésnek megfelelő találat az adatbázisban!' );
			return;
		  }
		  
		  $portlist = array();
		  $first = true;
		  $fport = '';
		  foreach( $rs as $row ) {
			if( $first ) {
			  $fport = $row['port'];
			  $first = false;
			}
			$pli = $row['port'].' '.$row['ndev'].' '.$row['uport'];
			// array_push( $portlist, $pli );
			$portlist = array_merge( $portlist, array( $row['port'] => $pli ) );
		  }
		  if( $ptype != 'nopid' ) {
			if( $roomid == 'WIFI' ) {
			  $fport = $roomid;
			}
		  }
		  if( $isupd ) {
			$fport = $updrow['port'];
		  }
		  // echo $fport;
		  $port = new WA_HashComboObject( 'port', 'Port:', array( $portlist ), array( $fport ) );
		  if( $isupd ) {
			$old_pli = '&nbsp;'.$updrow['port'].' '.$updrow['ndev'].' '.$updrow['uport'];
			$port->suffx->setLabel( WA_String::suffx( $old_pli ) );
			$port->suffx->enable();
		  }
		  // Build port list end
		
		  // Check free ips
		  // echo '<br>fip: '.$fip;
		  $ipaddr = new WA_ComboObject( 'ipaddr', 'IP:', array( $iplist ), array( $fip ) );
		  $ipaddr->set_sqlid( 'ip4' );
		  if( $isupd ) {
			$ipaddr->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['ip4'] ) );
			$ipaddr->suffx->enable();
		  }
		  
		  $macaddr = new WA_MACInput( 'macaddr' );
		  $macaddr->set_sqlid( 'mac' );
		  $macaddr->sql_merge = true;
		  $macaddr->sql_merge_sep = ':';		  
		  $macaddr->set_conv( 0, array( 'strtoupper' ) );
		  $macaddr->set_conv( 1, array( 'strtoupper' ) );
		  $macaddr->set_conv( 2, array( 'strtoupper' ) );
		  $macaddr->set_conv( 3, array( 'strtoupper' ) );
		  $macaddr->set_conv( 4, array( 'strtoupper' ) );
		  $macaddr->set_conv( 5, array( 'strtoupper' ) );
		  if( $isupd ) {
			$macaddr->set_init_val( $updrow['mac'] );
			$macaddr->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['mac'] ) );
			$macaddr->suffx->enable();			
		  }

		  
		  $hostname = new WA_InputObject( 'hostname', 'Hostname:', array( 'hostname' ), array( true ), array( 30 ) );
		  if( $isupd ) {
			$hostname->set_init_val( 0, $updrow['hostname'] );
			$hostname->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['hostname'] ) );
			$hostname->suffx->enable();			
		  }

		  $eap = new WA_InputObject( 'eap', 'EAP:', array( 'alnum' ), array( true ), array( 30 ) );
		  if( $isupd ) {
			$eap->set_init_val( 0, $updrow['eap'] );
			$eap->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['eap'] ) );
			$eap->suffx->enable();						
		  }

		  $expires = new WA_DateInput( 'expires', 'Expires:', true );
		  if( $isupd ) {
			$expires->set_init_date( $updrow['expires'] );
			$expires->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['expires'] ) );
			$expires->suffx->enable();									
		  }
		  
		  $rr_hinfo_os  = new WA_InputObject( 'rr_hinfo_os', 'OS:', array( 'general' ), array( false ), array( 30 ) );
		  if( $isupd ) {
			$rr_hinfo_os->set_init_val( 0, $updrow['rr_hinfo_os'] );
			$rr_hinfo_os->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['rr_hinfo_os'] ) );
			$rr_hinfo_os->suffx->enable();												
		  }
		  
		  $rr_hinfo_cpu = new WA_InputObject( 'rr_hinfo_cpu', 'CPU:', array( 'general' ), array( false ), array( 30 ) );
		  if( $isupd ) {
			$rr_hinfo_cpu->set_init_val( 0, $updrow['rr_hinfo_cpu'] );
			$rr_hinfo_cpu->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['rr_hinfo_cpu'] ) );
			$rr_hinfo_cpu->suffx->enable();															
		  }

		  $rr_hinfo_txt = new WA_InputObject( 'rr_hinfo_txt', 'TXT:', array( 'general' ), array( false ), array( 30 ) );
		  if( $isupd ) {
			$rr_hinfo_txt->set_init_val( 0, $updrow['rr_hinfo_txt'] );
			$rr_hinfo_txt->suffx->setLabel( WA_String::suffx( '&nbsp;'.$updrow['rr_hinfo_txt'] ) );
			$rr_hinfo_txt->suffx->enable();															
		  }

		  $cnames = new WA_CnamesObject( 'cnames', 'cnames:', '', 2 );
		  if( $isupd ) {
			$query  = 'SELECT * FROM '.$_SESSION['RECORD_TABLE'];
			$query .= ' WHERE mid = '.WA_String::sqlfmt( $mid );
			$query .= ' AND rec_type = \'CNAME\'';
			
			$cnames_txt = '';
			try {
			  $cnrs = $db->Execute( $query );
			}
			catch( exception $e ) {
			  print_query_error( 'Error: ', $e );
			  return;
			}
			if( $cnrs->RecordCount() < 1 ) {
			  $cnames_txt = '';
			}
			else {
			  $cnames_txt = '';
			  $first = true;
			  foreach( $cnrs as $cnrow ) {
				if( ! $first ) {
				  $cnames_txt .= ', ';
				}
				$cnames_txt .= $cnrow['rec_hostname'];
				$first = false;
			  }
			}
			
		  	$cnames->set_init_val( 0, $cnames_txt );
		  }		  

		  $comment = new WA_TextAreaObject( 'comment', 'Comment:' );
		  if( $isupd ) {
			$comment->set_init_val( 0, $updrow['comment'] );
		  }		  
		  
		  $opts_valid  = new WA_CheckboxObject( 'opts_valid', 'Valid', true );
		  $opts_valid->setLabelWidth( '150px' );
		  $opts_valid->setLabelAlign( 'right' );
		  $opts_valid->setLabelClass( 'item_label' );		
		  if( $isupd ) {
			if( $updrow['valid'] == 't' ) {
			  $opts_valid->suffx->setLabel( WA_String::suffx( '&nbsp;[×]' ) );
			}	
			else {
			  $opts_valid->suffx->setLabel( WA_String::suffx( '&nbsp;[ ]' ) );			
			}														
			$opts_valid->suffx->enable();
		  }

		  $opts_hidden = new WA_CheckboxObject( 'opts_hidden', 'Hidden', false );
		  $opts_hidden->setLabelWidth( '150px' );
		  $opts_hidden->setLabelAlign( 'right' );
		  $opts_hidden->setLabelClass( 'item_label' );		
		  if( $isupd ) {
			if( $updrow['hidden'] == 't' ) {
			  $opts_hidden->suffx->setLabel( WA_String::suffx( '&nbsp;[×]' ) );
			}	
			else {
			  $opts_hidden->suffx->setLabel( WA_String::suffx( '&nbsp;[ ]' ) );			
			}														
			$opts_hidden->suffx->enable();
		  }

		  $opts_dns  = new WA_CheckboxObject( 'opts_dns', 'DNS', true );
		  $opts_dns->setLabelWidth( '150px' );
		  $opts_dns->setLabelAlign( 'right' );
		  $opts_dns->setLabelClass( 'item_label' );		
		  if( $isupd ) {
			if( $updrow['dns'] == 't' ) {
			  $opts_dns->suffx->setLabel( WA_String::suffx( '&nbsp;[×]' ) );
			}	
			else {
			  $opts_dns->suffx->setLabel( WA_String::suffx( '&nbsp;[ ]' ) );			
			}														
			$opts_dns->suffx->enable();
		  }

		  $opts_dhcp = new WA_CheckboxObject( 'opts_dhcp', 'DHCP', true );
		  $opts_dhcp->setLabelWidth( '150px' );
		  $opts_dhcp->setLabelAlign( 'right' );
		  $opts_dhcp->setLabelClass( 'item_label' );		
		  if( $isupd ) {
			if( $updrow['dhcp'] == 't' ) {
			  $opts_dhcp->suffx->setLabel( WA_String::suffx( '&nbsp;[×]' ) );
			}	
			else {
			  $opts_dhcp->suffx->setLabel( WA_String::suffx( '&nbsp;[ ]' ) );			
			}														
			$opts_dhcp->suffx->enable();
		  }

		  $opts_wifi = new WA_CheckboxObject( 'opts_wifi', 'Wifi', false );
		  $opts_wifi->setLabelWidth( '150px' );
		  $opts_wifi->setLabelAlign( 'right' );
		  $opts_wifi->setLabelClass( 'item_label' );		
		  if( $isupd ) {
			if( $updrow['wifi'] == 't' ) {
			  $opts_wifi->suffx->setLabel( WA_String::suffx( '&nbsp;[×]' ) );
			}	
			else {
			  $opts_wifi->suffx->setLabel( WA_String::suffx( '&nbsp;[ ]' ) );			
			}														
			$opts_wifi->suffx->enable();
		  }

		  $opts_cont1   = new WA_ContainerObject( 'opts_cont1', 'horizontal' );
		  $opts_cont1->putObjectArray( array( $opts_valid, $opts_hidden, $opts_dns, $opts_dhcp, $opts_wifi ) );
		}
		else {
		  if( $isupd ) {
			$opts_obj = array();

			if( $updrow['valid'] == 't' ) {
			  $opts_valid = new WA_HiddenFieldObject( 'opts_valid_checkbox', 'on' );
			  $opts_obj = array_merge( $opts_obj, array( $opts_valid ) );
			}
			if( $updrow['hidden'] == 't' ) {
			  $opts_hidden = new WA_HiddenFieldObject( 'opts_hidden_checkbox', 'on' );
			  $opts_obj = array_merge( $opts_obj, array( $opts_hidden ) );
			}
			if( $updrow['dns'] == 't' ) {
			  $opts_dns = new WA_HiddenFieldObject( 'opts_dns_checkbox', 'on' );
			  $opts_obj = array_merge( $opts_obj, array( $opts_dns ) );
			}
			if( $updrow['dhcp'] == 't' ) {
			  $opts_dhcp = new WA_HiddenFieldObject( 'opts_dhcp_checkbox', 'on' );
			  $opts_obj  = array_merge( $opts_obj, array( $opts_dhcp ) );
			}
			if( $updrow['wifi'] == 't' ) {
			  $opts_wifi = new WA_HiddenFieldObject( 'opts_wifi_checkbox', 'on' );
			  $opts_obj  = array_merge( $opts_obj, array( $opts_wifi ) );
			}			
		  }		
		  else {
			$opts_valid = new WA_HiddenFieldObject( 'opts_valid_checkbox', 'on' );
			$opts_dns	= new WA_HiddenFieldObject( 'opts_dns_checkbox',   'on' );
			$opts_dhcp	= new WA_HiddenFieldObject( 'opts_dhcp_checkbox',  'on' );
			$opts_wifi	= new WA_HiddenFieldObject( 'opts_wifi_checkbox',  'on' );
	
			$opts_obj = array( $opts_valid, $opts_dns, $opts_dhcp );
			
			if( $ptype != 'nopid' ) {
			  if( $roomid == 'WIFI' ) {
				$opts_obj = array_merge( $opts_obj, array( $opts_wifi ) );
			  }
			}
		  }
		  $opts_cont1 = new WA_ContainerObject( 'opts_cont1', 'horizontal' );
		  $opts_cont1->putObjectArray( $opts_obj );
		  
		  $eap = new WA_HiddenFieldObject( 'eap_field_0', WA_String::geneap() );		  
		}
		
		$button  = new WA_ButtonObject( 'button', 'Tovább', '' );
  
		if( $ptype != 'nopid' and $action == 'sel_vlan' ) {
  		  //$link = 'javascript: document.'.$this->id.'.state.value = 1; document.'.$this->id.'.submit()';
		  $link = 'index.php?m=sel_user';
		  		  
		  // $button->suffx->setLink('<div class="correct">Vissza / Javít</div>', $link );;
		  // $button->suffx->enable();
		}
		
		$gap1 = new WA_LabelObject( 'gap1', '&nbsp;' );
		$gap2 = new WA_LabelObject( 'gap2', '&nbsp;' );
		
  
		// BUILD FORM
		if( $ptype != 'nopid' ) {
		  $objarr = array( $name, $vlan );
		}
		else {
		  $objarr = array( $vlan );		
		}

		if( $action == 'sel_ip' ) {
		  $objarr = array_merge( $objarr, array( $port, $ipaddr, $macaddr, $hostname, $eap, $expires, 
												 $rr_hinfo_os, $rr_hinfo_cpu, $rr_hinfo_txt, 
												 $cnames, $comment, $gap1, $opts_cont1, $gap2 ) );
		}		
		else {
		  $objarr = array_merge( $objarr, array( $eap, $opts_cont1 ) );		
		}

		$objarr = array_merge( $objarr, array( $button ) );

		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '100px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }


  public function genStaticHTML() {
	if( $this->is_disabled ) {
	  return;
	}

	// echo '<br> state: '. $this->state->get_val();	
	if( $this->isState( 'work' ) ) {
	  $link = 'javascript: document.'.$this->id.'.state.value = 1; document.'.$this->id.'.submit()';
	  $this->getContentObject('button')->suffx->setLink('<div class="correct">Vissza / Javít</div>', $link );;
	  $this->getContentObject('button')->suffx->enable();
	}
	parent::genStaticHTML();
  }


  public function Update( $db ) {
	$this->genQueryArray( array( 'name', 'vlan', 'port', 'opts_cont1', 'cnames' ), false );

	if( $this->ptype != 'nopid' ) {
	  $pid = $this->opts['pid'];
	  $this->putQueryArray( array( 'pid' => WA_String::sqlfmt( $pid ) ) );
	  $this->putQueryArray( array( 'mtype' => WA_String::sqlfmt( 'pid' ) ) );	  
	}
	else {
	  $this->putQueryArray( array( 'mtype' => WA_String::sqlfmt('nopid' ) ) );	
	}
	
	$portid = $this->opts['port'];
	$this->putQueryArray( array( 'port' => WA_String::sqlfmt( $portid ) ) );

	$vlanid = $this->opts['vlan'];
	$this->putQueryArray( array( 'vl_id' => WA_String::sqlfmt( $vlanid ) ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_valid' );
	$this->putQueryArray( array( 'valid' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_hidden' );
	$this->putQueryArray( array( 'hidden' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_dns' );
	$this->putQueryArray( array( 'dns' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_dhcp' );
	$this->putQueryArray( array( 'dhcp' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_wifi' );
	$this->putQueryArray( array( 'wifi' => $o->genQueryFooter() ) );

	$this->putQueryArray( array( 'cdate' => WA_String::sqlfmt( date( "Y-m-d" ) ) ) );
	$this->putQueryArray( array( 'lastmtime' => WA_String::sqlfmt( WA_String::timenow() ) ) );


	// cname check
	$o = $this->getContentObject( 'hostname' );
	$hostname = trim( $o->get_val() );

	$ccquery  = 'SELECT * FROM '.$_SESSION['RECORD_TABLE'];
	$ccquery .= ' WHERE rec_hostname = '.WA_String::sqlfmt( $hostname );
	
	try {
	  $ccrs = $db->Execute( $ccquery );
	}
	catch( exception $e ) {
	  print_query_error( 'Error:', $e );
	  return 1;
	}
	
	if( $ccrs->RecordCount() > 0 ) {
	  echo '<span class="info_red">Van ilyen cname!</span>';
	  return 1;
	}

	// update
	if( isset( $this->opts['mid'] ) ) {
	  $mid = $this->opts['mid'];
	  if( is_numeric( $mid ) ) {

  	    $query = $this->genUpdateQueryString( $_SESSION['HOST_TABLE'], $mid, 'mid' );
	    echo '<br><span class="info">Aktualizálás: </span>';
		try {
		  $db->Execute( $query );
		  echo '<span class="info_green">sikerült!</span>';
		  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'UPDATE HOST', $query );
		  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'UPDATE HOST', $query );
		}
		catch( exception $e ) {
		  echo '<span class="info_red">hibás, már létezik!</span>';
		  print_query_error( "Error: ".$query, $e );  
		}
		// echo '<br>'.$query;
	  }

	  $query  = 'DELETE FROM '.$_SESSION['RECORD_TABLE'];
	  $query .= ' WHERE mid = '.WA_String::sqlfmt( $mid );
	  try {
	    $rs = $db->Execute( $query );
	  	// WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'DELETE CNAME', $query );
	  	WA_Session::log_this( $_SESSION['LOGIN_USER'], 'DELETE CNAME', $query );
	  }
	  catch( exception $e ) {
	    echo '<span class="info_red"> cname törlés hibás</span>';
	    print_query_error( 'Error:', $e );
	    return 1;
	  }
	
	  $o = $this->getContentObject( 'cnames' );
	  $val = $o->get_val();
	  if( WA_String::nzstr( $val ) ) {
		$arr = explode( ",", $val );

		echo '<br><span class="info">Cname-ek létrehozása: </span>';
		
		foreach( $arr as $v ) {
		  $hn = trim( $v );
		  //if( WA_Validator::hostname( $hn ) ) {
		  if( WA_Validator::cname( $hn ) ) {
			$hcquery  = 'SELECT hostname FROM '.$_SESSION['HOST_TABLE'];
			$hcquery .= ' WHERE hostname = '.WA_String::sqlfmt( $hn );
			try {
			  $hcrs = $db->Execute( $hcquery );
			}
			catch( exception $e ) {
			  print_r( $hcrs );
			  print_query_error( 'Error:', $e );
			  return 1;
			}
			
			if( $hcrs->RecordCount() < 1 ) {
			  $cnquery  = 'INSERT INTO '.$_SESSION['RECORD_TABLE'];
			  $cnquery .= ' (mid,rec_type,rec_hostname)';
			  $cnquery .= ' VALUES ('.WA_String::sqlfmt($mid).',\'CNAME\','.WA_String::sqlfmt($hn).')';
			  echo ' '.$hn;
			  // echo '<br>'.$cnquery;

			  try {
				$cnrs = $db->Execute( $cnquery );
				// WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE CNAME', $cnquery );
				WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE CNAME', $cnquery );
			  }
			  catch( exception $e ) {
				echo ':duplikált ';
				// echo '<span class="info_red"> hibás felvétel: '.$hn.'</span>';
				// print_query_error( 'Error:', $e );
			  }
			}
			else {
			  echo ' '.$hn.':duplikált ';
			}
		  }
		}
	  }

	}
  }


  public function Record( $db ) {
	$this->genQueryArray( array( 'name', 'vlan', 'port', 'opts_cont1', 'cnames' ), false );

	if( $this->ptype != 'nopid' ) {
	  $pid = $this->opts['pid'];
	  $this->putQueryArray( array( 'pid' => WA_String::sqlfmt( $pid ) ) );
	  $this->putQueryArray( array( 'mtype' => WA_String::sqlfmt( 'pid' ) ) );	  
	}
	else {
	  $this->putQueryArray( array( 'mtype' => WA_String::sqlfmt('nopid' ) ) );	
	}
	
	$portid = $this->opts['port'];
	$this->putQueryArray( array( 'port' => WA_String::sqlfmt( $portid ) ) );

	$vlanid = $this->opts['vlan'];
	$this->putQueryArray( array( 'vl_id' => WA_String::sqlfmt( $vlanid ) ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_valid' );
	$this->putQueryArray( array( 'valid' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_hidden' );
	$this->putQueryArray( array( 'hidden' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_dns' );
	$this->putQueryArray( array( 'dns' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_dhcp' );
	$this->putQueryArray( array( 'dhcp' => $o->genQueryFooter() ) );

	$o = $this->getChildObject( 'opts_cont1', 'opts_wifi' );
	$this->putQueryArray( array( 'wifi' => $o->genQueryFooter() ) );

	$this->putQueryArray( array( 'cdate' => WA_String::sqlfmt( date( "Y-m-d" ) ) ) );
	$this->putQueryArray( array( 'lastmtime' => WA_String::sqlfmt( WA_String::timenow() ) ) );

  	$query = $this->genStoreQueryString( $_SESSION['HOST_TABLE'] );
	echo '<br><span class="info">Felvétel az adatbázisba: </span>';
	// echo '<br>'.$query;

	$o = $this->getContentObject( 'hostname' );
	$hostname = trim( $o->get_val() );
	
	$ccquery  = 'SELECT * FROM '.$_SESSION['RECORD_TABLE'];
	$ccquery .= ' WHERE rec_hostname = '.WA_String::sqlfmt( $hostname );
	
	try {
	  $ccrs = $db->Execute( $ccquery );
	}
	catch( exception $e ) {
	  print_query_error( 'Error:', $e );
	  return 1;
	}
	
	if( $ccrs->RecordCount() > 0 ) {
	  echo '<span class="info_red">Van ilyen cname!</span>';
	  return 1;
	}

	
	try {
	  $db->Execute( $query );
	  echo '<span class="info_green">sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE HOST', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE HOST', $query );
	}
	catch( exception $e ) {
	  echo '<span class="info_red">hibás!</span>';
	  print_query_error( 'Error:', $e );
	  return 1;
	}
	
	
	// backsearch	
	$query  = 'SELECT mid FROM '.$_SESSION['HOST_TABLE'];
	$query .= ' WHERE hostname = '.WA_String::sqlfmt( $hostname );
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  echo '<span class="info_red">Visszakeresés hibás!</span>';
	  print_query_error( 'Error:', $e );
	  return 1;
	}
	
	$row = $rs->FetchRow();
	$mid = $row['mid'];
	
	$o = $this->getContentObject( 'cnames' );
	$val = $o->get_val();
	if( WA_String::nzstr( $val ) ) {
	  $arr = explode( ",", $val );

	  echo '<br><span class="info">Cname-ek létrehozása: </span>';
	
	  print_r( $arr );
	  foreach( $arr as $v ) {
		$hn = trim( $v );
		//if( WA_Validator::hostname( $hn ) ) {
		if( WA_Validator::cname( $hn ) ) {
		  $hcquery  = 'SELECT hostname FROM '.$_SESSION['HOST_TABLE'];
		  $hcquery .= ' WHERE hostname = '.WA_String::sqlfmt( $hn );
		  try {
		    $hcrs = $db->Execute( $hcquery );
		  }
		  catch( exception $e ) {
		    print_query_error( 'Error:', $e );
		    return 1;
		  }

		  if( $hcrs->RecordCount() < 1 ) {
			$cnquery  = 'INSERT INTO '.$_SESSION['RECORD_TABLE'];
			$cnquery .= ' (mid,rec_type,rec_hostname)';
			$cnquery .= ' VALUES ('.WA_String::sqlfmt($mid).',\'CNAME\','.WA_String::sqlfmt($hn).')';
			echo ' '.$hn;

			try {
			  $cnrs = $db->Execute( $cnquery );
			  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE CNAME', $cnquery );
			  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE CNAME', $cnquery );
			}
			catch( exception $e ) {
			  // echo '<span class="info_red"> hibás felvétel: '.$hn.'</span>';
			  // print_query_error( 'Error:', $e );
			  echo ':duplikált ';
			}
		  }
		  else {
			echo ' '.$hn.':duplikált ';
		  }
		}
	  }
	}


  }
  
}
?>
