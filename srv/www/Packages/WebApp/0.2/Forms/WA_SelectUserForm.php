<?php

class WA_SelectUserForm extends WA_ListUserForm {
    public function __construct( $id = 'sel_user', $title = 'List User', $module = 'sel_user', $opts = array() ) {
	  $this->faculty_arr = array( array( '.*', 'GTK', 'TTK', 'VIK', 'ÉPK', 'VBK', 'GPK', 'ÉMK', 'KSK', 'INT', 'EXT' ) );

	  parent::__construct( $id, $title, $module, $opts );
  
  	  $descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 </div>';
  
	  // FORM
	  $this->setDescription( $descr );
  }


  public function genQuery( $ptype, $ext = '' ) {
  	$table = $_SESSION['PERSON_TABLE'];
	
	$this->ptype = $ptype;

	$this->limit = 1000;
	if( isset( $this->opts['limit'] ) ) {
	  $this->limit = $this->opts['limit'];
	}

	// construct query
	$first = true;
	$first_order = true;
	if( $ptype == 'student' ) {
	  $query_header = WA_Session::select_string( array( 'pid', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'faculty', 'wing', 'floor', 'number', 
							'class', 'enrollyear', 'email', 'msn', 'skype'), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' INNER JOIN '.$_SESSION['ROOM_TABLE'].' USING (roomid)';
	  $query_footer .= ' WHERE ptype ~ '.WA_String::sqlfmt( 'student' ).' AND';
	}
	else if( $ptype == 'guest' ) {
	  $query_header = WA_Session::select_string( array( 'pid', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'wing', 'floor', 'number', 
							'email', 'msn', 'skype'), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' INNER JOIN '.$_SESSION['ROOM_TABLE'].' USING (roomid) WHERE';
	  $query_footer .= ' ptype ~ '.WA_String::sqlfmt( 'guest' );	//.' AND'; // cseka
	}
	else {
	  $query_header = WA_Session::select_string( array( 'pid', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'email', 'msn', 'skype'), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' WHERE';		
	  $query_footer .= ' ptype !~ '.WA_String::sqlfmt( 'student' ).' AND';
	  $query_footer .= ' ptype !~ '.WA_String::sqlfmt( 'guest' ); //.' AND'; // hornos
	}

	if( WA_String::nzstr( $ext ) ) {
	  $query_footer .= $ext.' AND';
	}

	// name	
	$o = $this->getChildObject( 'name_cont', 'name' );
	if( WA_String::nzstr( $o->getForname() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " (name).forname ~ ".WA_String::sqlfmt( $o->getForname() );
	}

	if( WA_String::nzstr( $o->getSurname() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " (name).surname ~ ".WA_String::sqlfmt( $o->getSurname() );	  
	}
	// name end

	$o = $this->getChildObject( 'sex_cont', 'sort_sex' );
	if( $o->checked ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;

	  $o = $this->getChildObject( 'sex_cont', 'sex' );
	  $query_footer .= " sex ~ ".WA_String::sqlfmt( $o->selected );	  
	}

	if( $ptype == 'student' ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;

	  $o = $this->getChildObject( 'faculty_cont', 'faculty' );
	  $query_footer .= " faculty ~ ".WA_String::sqlfmt( $o->get_val() );	  
	
	  $o = $this->getChildObject( 'class_cont', 'class' );
	  $query_footer .= " AND class ~ ".WA_String::sqlfmt( $o->get_val() );	  
	}
	
	if( $ptype == 'student' || $ptype == 'guest' ) {
	  $o = $this->getChildObject( 'roomid_cont', 'roomid' );
	  $query_footer .= " AND roomid ~ ".WA_String::sqlfmt( $o->get_val() );	  
	}

	if( $ptype == 'student' ) {
	  $o = $this->getChildObject( 'faculty_cont', 'sort_faculty' );
	  if( $o->checked ) {
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
	  if( $o->checked ) {
		if( $first_order ) {
		  $query_footer .= " ORDER BY";
		  $first_order = false;
		}
		else {
		  $query_footer .= ",";
		}
		$query_footer .= " class";
	  }
	}

	if( $ptype == 'student' || $ptype == 'guest' ) {
	  $o = $this->getChildObject( 'roomid_cont', 'sort_roomid' );
	  if( $o->checked ) {
		if( $first_order ) {
		  $query_footer .= " ORDER BY";
		  $first_order = false;
		}
		else {
		  $query_footer .= ",";
		}
		$query_footer .= " roomid";
	  }
	}

	$o = $this->getChildObject( 'name_cont', 'sort_name' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " (name).forname, (name).surname";
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


  public function showresults( $rs ) {
	$isdet = $this->getChildObject( 'opts_cont', 'opts_details' );
	$iscol = $this->getChildObject( 'opts_cont', 'opts_colors' );

	if( $this->ptype == 'student' ) {
	  if( $isdet->isChecked() ) {
		$titles = array( 'Kiválaszt' => array( 'action' => 'sel_vlan', 'action_title' => 'Kiválaszt', 
											   'action_icon' => 'select.png' ),
						 'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 
						 'Kar' => 'faculty', 'Évfolyam' => 'class', 
						 'Szárny' => 'wing', 'Emelet' => 'floor', 
						 'Szoba' => 'number', 'Beiratkozás' => 'enrollyear',
						 'Beköltözés' => 'occupdate', 'Kiköltözés' => 'leavedate' );

		$sizes  = array( '70px', '100px', '100px', '50px',
					     '100px','50px', '50px', 
						 '50px', '80px', '100px', '100px' );	  
	  }
	  else {
		$titles = array( 'Kiválaszt' => array( 'action' => 'sel_vlan', 'action_title' => 'Kiválaszt', 
											   'action_icon' => 'select.png' ),
					   'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 
					   'Kar' => 'faculty', 'Évfolyam' => 'class', 'Szobaszám' => 'roomid');

		$sizes  = array( '70px' ,'100px', '100px', '100px','100px','100px' );
	  }
	}  
	else if( $this->ptype == 'guest' ) {
	  if( $isdet->isChecked() ) {
		$titles = array( 'Kiválaszt' => array( 'action' => 'sel_vlan', 'action_title' => 'Kiválaszt', 
											   'action_icon' => 'select.png' ),
					   'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 
					   'Szobaszám' => 'roomid', 'Beköltözés' => 'occupdate', 'Kiköltözés' => 'leavedate');

		$sizes  = array( '70px' , '100px', '100px', '100px', '100px', '100px' );
	  }
	  else {
		$titles = array( 'Kiválaszt' => array( 'action' => 'sel_vlan', 'action_title' => 'Kiválaszt', 
											   'action_icon' => 'select.png' ),
					   'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 
					   'Szobaszám' => 'roomid');

		$sizes  = array( '70px' , '100px', '100px', '100px' );	  
	  }
	}
	else {
		$titles = array( 'Kiválaszt' => array( 'action' => 'sel_vlan', 'action_title' => 'Kiválaszt', 
											   'action_icon' => 'select.png' ),
					   'Vezetéknév' => 'forname', 'Keresztnév' => 'surname' );

		$sizes  = array( '70px' , '100px', '100px' );	
	}
	$rstable = new WA_ModifyTable( 'results', $titles, $sizes , $rs, 
					  array( 'colors' => $iscol->isChecked(), 'mf' => 'pid', 'ptype' => $this->ptype, 'form_opts' => $this->opts ) );
	$rstable->genHTML();
  }


  public function query( $db, $pid = false ) {
	$types = array( 'student' => 'Hallgatók' , 'guest' => 'Vendégek', 'wifi' => 'Wifi' );
	foreach( $types as $tptype => $desc ) {
	  if( $pid == false ) {
		$this->genQuery( $tptype );
	  }
	  else {
		$this->genQuery( $tptype, ' pid = '.WA_String::sqlfmt( $pid ) );
	  }
	  // echo $this->query;
	
	  // User Check
	  try {
		$rs = $db->Execute( $this->query );
	  }
	  catch( exception $e ) {
		print_error( 'SQL hiba történt, fordulj a rendszergazdához segítségért!' );
		echo $this->query;
		print_query_error( 'Error: ', $e );
		return;
	  }
	
	  print_info( $desc );
	  if( $rs->RecordCount() < 1 ) {
		print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  }
	  else {
		$this->showresults( $rs );
	  }
	}
  }

}
?>
