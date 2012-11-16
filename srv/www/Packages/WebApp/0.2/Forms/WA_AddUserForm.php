<?php

class WA_AddUserForm extends WA_FormObject {
    public function __construct( $id = 'add_user', $title = 'Add User', $module = 'add_user', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 <br><br>
				 <span style="font-weight: bold">Jelmagyarázat:</span>
				 <span class="border_error">Hibásan kitöltött mező</span>
				 <span class="border_ok">Jól kitöltött mező</span>
				 <span class="border_needed">Kötelező mező</span>
				 </div>';

  
		// FORM
		$this->setDescription( $descr );
		$this->faculty_arr = array( array( 'GTK', 'TTK', 'VIK', 'ÉPK', 'VBK', 'GPK', 'ÉMK', 'KSK', 'INT', 'EXT' ) );

		// OBJECTS
		// sql bind: name
		$name = new WA_NameInput( 'name', 'Név:', true );
		
		$birthplace = new WA_InputObject( 'birthplace', 'Születési Hely:', array( 'alpha' ), array( true ), array( 30 ) );
		$birthdate  = new WA_DateInput( 'birthdate', 'Születési Dátum:', true );
		$sex 	    = new WA_GenderSelect( 'sex', 'M' );
		$sex->setFieldLabelAlign( 'right' );
		$sex->setFieldLabelClass( 'radio' );	
		
		$mother  = new WA_NameInput( 'mothername', 'Anyja (leánykori) Neve:', false );
		$father  = new WA_NameInput( 'fathername', 'Apja / Eltartója Neve:', false );

		$faculty = new WA_ComboObject( 'faculty', 'Kar:', $this->faculty_arr, array( 1 ) );
		$class   = new WA_ComboObject( 'class', 'Évfolyam:', array( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'Phd 1', 'Phd 2', 'Phd 3' ) ), array( 1 ) );

		$enrollyear = new WA_InputObject( 'enrollyear', 'Beiratkozás Éve:', array( 'year' ), array( true ), array( 4 )  );

		$id = new WA_InputObject( 'id', 'Szem. ig. sz.:', array( 'alnum' ), array( true ) , array( 25 )  );
		$id->set_conv( 0, array( 'strtoupper' ) );
		$etrid = new WA_InputObject( 'etrid', 'Neptun Kód:', array( 'alnum' ), array( true ) , array( 25 ) );
		$etrid->set_conv( 0, array( 'strtoupper' ) );
  
		$roomid = new WA_ComboObject( 'roomid', 'Szobaszám:', array( array( 'A', 'B', 'C' ), 
			  array( 0, 1, 2, 3, 4 ), array( '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14' ) ), array( 1, 1, 1 ) );
		$roomid->sql_merge = true;
		
		if( $this->isopt( 'ptype', 'wifi' ) ) {
		  $occuptitle = 'Érvényesség kezdete:';
		  $leavetitle = 'Érvényesség vége:';
		}		  
		else {
		  $occuptitle = 'Beköltözés Dátuma:';
		  $leavetitle = 'Kiköltözés Dátuma:';
		}

		$occupdate = new WA_DateInput( 'occupdate', $occuptitle, true );
		$occupdate->set_init_val( time() );
		
		$leavedate = new WA_DateInput( 'leavedate', $leavetitle, false );
		$leavedate->set_init_val( time() + ( 183 * 24 * 60 * 60 ) );

		$isvalid  = new WA_CheckboxObject( 'isvalid', "Aktív?", true );
		
		// TODO: email validation for empty field
		$tel = new WA_TelInput( 'tel', array( 'Tel (Otthon):' => 'athome', 
												    'Tel (Munkahely):' => 'atwork',
													'Tel (Mobil):' => 'atmobile' ) );
		$tel->fields_cont->css_class = 'form_cont';
		
		$email = new WA_EmailInput( 'email', 'E-mail:', true );
		$msn   = new WA_EmailInput( 'msn', 'MSN:', false );
		$skype = new WA_EmailInput( 'skype', 'Skype:', false );
		$skype->fields[0]->validate_type = 'general';
  
		$permaddr = new WA_AddressInput( 'permaddr', 'Állandó Lakcím', true );
		$tempaddr = new WA_AddressInput( 'tempaddr', 'Ideiglenes (Levelezési) Lakcím', false );
		$tempaddr->islett->setChecked( true );
		$tempaddr->islett->setReadonly( true );

		$permaddr->fields_cont->css_class = 'form_cont';
		$tempaddr->fields_cont->css_class = 'form_cont';
		
/*		$this->lettaddr->setParentAddr( $this->permaddr );
//		$this->address = new WA_AddressInputObject( 'address', true );
*/		
		$button  = new WA_ButtonObject( 'button', 'Elküld', 'Ellenőriz' );
  
		// BUILD FORM
		$l1 = new WA_LabelObject( 'l1', 'Személyes adatok' );
		$l1->css_class = 'cat_label';
		
		$l2 = new WA_LabelObject( 'l2', 'Hallgatói adatok' );
		$l2->css_class = 'cat_label';
	
		$l3 = new WA_LabelObject( 'l3', 'Elérhetőség' );
		$l3->css_class = 'cat_label';

		
		$objarr = array( $l1, $name );

		if( $this->isopt( 'ptype', 'student' ) ) {		
		  $objarr = array_merge( $objarr, array( $birthplace, $birthdate ) );
		}
		$objarr = array_merge( $objarr, array( $sex ) );

		if( $this->isopt( 'ptype', 'student' ) ) {		
		  $objarr = array_merge( $objarr, array( $mother, $father, $l2, $faculty, $class, $enrollyear ) );
		}
		
		$objarr = array_merge( $objarr, array( $id ) );

		if( $this->isopt( 'ptype', 'student' ) ) {		
		  $objarr = array_merge( $objarr, array( $etrid ) );
		}

		if( $this->isopt( 'ptype', 'student' ) or $this->isopt( 'ptype', 'guest' ) ) {
		  $objarr = array_merge( $objarr, array( $roomid ) );
		}
		
		$objarr = array_merge( $objarr, array( $occupdate, $leavedate, $isvalid ) );
		
		$objarr = array_merge( $objarr, array( $l3, $tel, $email, $msn, $skype ) );		
	
//		$objarr = array_merge( $objarr, array( $l4, $l5 ) );
		$objarr = array_merge( $objarr, array( $permaddr , $tempaddr) );
	
/*		if( $this->isopt( 'action', 'del' ) ) {
		  $cascade = new WA_CheckboxObject( 'cascade', 'KASZKÁD TÖRLÉS?', true );
		  $cascade->css_class = 'cascade';
		  $cascade->chbox->not_static = true;
		  $objarr = array_merge( $objarr, array( $cascade ) );
		}
*/
		$objarr = array_merge( $objarr, array( $button ) );

		$this->putContentObjectArray( $objarr );

		// SET GLOBAL STYLE
		$this->setLabelWidth( '160px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$sex->setFieldLabelWidth( '50px' );
		$this->cont->css_class = 'form_cont';
  }
  
  public function sqlinit( $db ) {
//	$query = $this->genSelectQueryString( $table );
	
	$tops = WA_Session::get_tops( $this );
	$table = $tops['table'];
	$table_ptype = $tops['table_ptype'];
	$ptype = $tops['ptype'];

	// echo '<br>SQL Table: '.$table_ptype;
	$pid = $this->getObject( 'pid' )->get_val();

	// gen query start
	if( $table_ptype == 'student' ) {
	  $query_header = WA_Session::select_string( array( 'pid', 'id', 'etrid', 
							array( '(mothername).forname' => 'mforname' ), 
							array( '(mothername).surname' => 'msurname' ), 
							array( '(fathername).forname' => 'fforname' ),
							array( '(fathername).surname' => 'fsurname' ),
							'(name).forname', '(name).surname', 
							'birthplace', 'birthdate', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'faculty', 'wing', 'floor', 'number', 
							'class', 'enrollyear',
							array( '('.$table.'.tel).athome' => 'athome' ), 
							array( '('.$table.'.tel).atwork' => 'atwork' ), 
							array( '('.$table.'.tel).atmobile' => 'atmobile' ),
							'street', 'city', 'state', 'zip', 'country', 'islett',
							'email', 'msn', 'skype', 'permaddr', 'tempaddr' ), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' INNER JOIN '.$_SESSION['ADDRESS_TABLE'].' ON '.$table.'.permaddr = '.$_SESSION['ADDRESS_TABLE'].'.addrid';
	  $query_footer .= ' INNER JOIN '.$_SESSION['ROOM_TABLE'].' USING (roomid) WHERE';
	  $query_footer .= ' ptype ~ '.WA_String::sqlfmt( 'student' ).' AND';
	}
	else if( $table_ptype == 'guest' ) {
	  $query_header = WA_Session::select_string( array( 'pid', 'id', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'wing', 'floor', 'number', 
							'street', 'city', 'state', 'zip', 'country', 'islett',
							array( '('.$table.'.tel).athome' => 'athome' ), 
							array( '('.$table.'.tel).atwork' => 'atwork' ), 
							array( '('.$table.'.tel).atmobile' => 'atmobile' ), 
							'email', 'msn', 'skype', 'permaddr', 'tempaddr' ), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' INNER JOIN '.$_SESSION['ADDRESS_TABLE'].' ON '.$table.'.permaddr = '.$_SESSION['ADDRESS_TABLE'].'addrid';
	  $query_footer .= ' INNER JOIN '.$_SESSION['ROOM_TABLE'].' USING (roomid) WHERE';
	  $query_footer .= ' ptype ~ '.WA_String::sqlfmt( 'guest' ).' AND';
	}
	else {
	  $query_header = WA_Session::select_string( array( 'pid', 'id', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'street', 'occupdate', 'leavedate', 'city', 'state', 'zip', 'country', 'islett',
							array( '('.$table.'.tel).athome' => 'athome' ), 
							array( '('.$table.'.tel).atwork' => 'atwork' ), 
							array( '('.$table.'.tel).atmobile' => 'atmobile' ),
							'email', 'msn', 'skype', 'permaddr', 'tempaddr' ), $table );

	  // $query_footer  = ' INNER JOIN T_person_tbl USING (ptype)';
	  $query_footer  = ' INNER JOIN '.$_SESSION['ADDRESS_TABLE'].' ON '.$table.'.permaddr = '.$_SESSION['ADDRESS_TABLE'].'.addrid';
	  $query_footer .= ' WHERE';		
	  $query_footer .= ' ptype !~ '.WA_String::sqlfmt( 'student' ).' AND';
	  $query_footer .= ' ptype !~ '.WA_String::sqlfmt( 'guest' ). 'AND';
	}
	$query_footer .= ' pid = '.WA_String::sqlfmt( $pid );
	$query = $query_header.$query_footer;
	// echo '<br>Query: '.$query;
	// gen query end

	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error:', $e );
	}
	
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  return;
	}
	else {
	  $this->init_from_sql( $db, $rs, array() );
	}
  }

  public function Delete( $db ) {
  	$pid = $this->getObject( 'pid' )->get_val();

	$query  = 'DELETE FROM '.$_SESSION['PERSON_TABLE'];
	$query .= ' WHERE pid = '.WA_String::sqlfmt( $pid );

	echo '<br><span class="info">Törlés: </span>';
	try {
	  $db->Execute( $query );
// TODO: delete check
//	  if( $rs->RecordCount() == 0 ) {
//		echo '<span class="info_red">Hiba!</span>';
//	  }
//	  else {
	
	  echo '<span class="info_green">Sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'DELETE USER', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'DELETE USER', $query );

//	  }
	}
	catch( exception $e ) {
	  echo '<span class="info_red">Hibás! A felhasználótól más adatok is függenek!</span>';
	  print_query_error( "Error: ".$query, $e );
	}
  }
  

  public function Update( $db ) {
	// echo 'SQL RECORD';
	echo "TODO: affected rows!";
	
	// Address Record
	$permaddr_obj = $this->getContentObject( 'permaddr' );
	$query    = $permaddr_obj->genStoreQueryString( $_SESSION['ADDRESS_TABLE'] );
	$permaddr = $permaddr_obj->checksum( $_SESSION['ADDRESS_TABLE'] );
	$tempaddr = $permaddr;
	
	// echo '<br>Perm Query: ' . $query;
	echo '<br><span class="info">Állandó lakcím rögzítése: </span>';
	try {
	  $db->Execute( $query );
	  echo '<span class="info_green">az új cím létrehozása sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE PERMANENT ADDRESS', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE PERMANENT ADDRESS', $query );

	}
	catch( exception $e ) {
	  echo '<span class="info_orange">ez a cím már létezik, felhasználom!</span>';
	}

	if( ! $permaddr_obj->islett->isChecked() ) {
	  $tempaddr_obj = $this->getContentObject( 'tempaddr' );
	  $query    = $tempaddr_obj->genStoreQueryString( $_SESSION['ADDRESS_TABLE'] );
	  $tempaddr = $tempaddr_obj->checksum( $_SESSION['ADDRESS_TABLE'] );

	  // echo '<br>Lett Query: ' . $this->getContentObject( 'tempaddr' )->genStoreQueryString( 'test' );	  
	  echo '<br><span class="info">Ideiglenes lacím rögzítése: </span>';  
	  try {
		$db->Execute( $query );
		echo '<span class="info_green">az új cím létrehozása sikerült!</span>';
		// WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE TEMPORARY ADDRESS', $query );
		WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE TEMPORARY ADDRESS', $query );
	  }
	  catch( exception $e ) {
		echo '<span class="info_orange">ez a cím már létezik, felhasználom!</span>';
	  }
	}

	$this->genQueryArray( array( 'permaddr', 'tempaddr', 'button' ), true );	
	$this->putQueryArray( array( 'permaddr' => WA_String::sqlfmt( $permaddr ), 'tempaddr' => WA_String::sqlfmt( $tempaddr ) ) );
	
	$ptype = 'guest';
	if( isset( $this->opts['ptype'] ) ) {
	  $ptype = $this->opts['ptype'];
	}
	$this->putQueryArray( array( 'ptype' => WA_String::sqlfmt( $ptype ) ) );

	$id = $this->getContentObject( 'id' );
	
	$pid = $this->getObject( 'pid' )->get_val();
	// $this->putQueryArray( array( 'pid' => $pid ) );
	$this->putQueryArray( array( 'exprydate' => $this->getContentObject( 'leavedate' )->genQueryFooter() ) );
	// $this->putQueryArray( array( 'isvalid' => WA_String::sqlfmt( 't' ) ) );
	$this->putQueryArray( array( 'lastmtime' => WA_String::sqlfmt( WA_String::timenow() ) ) );


	// Final Query
	$table = $_SESSION['PERSON_TABLE'];

	$query = $this->genUpdateQueryString( $table, $pid );
	echo '<br><span class="info">Felhasználó aktualizálása: </span>';
	// echo '<br>'.$query;

	try {
	  $db->Execute( $query );
	  echo '<span class="info_green">sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'UPDATE USER', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'UPDATE USER', $query );
	}
	catch( exception $e ) {
	  echo '<span class="info_red">hibás, már létezik!</span>';
	  print_query_error( "Error: ".$query, $e );  
	}		

  }
  // Update end
  
  
  public function Record( $db ) {
	// echo 'SQL RECORD';
	
	// Address Record
	$permaddr_obj = $this->getContentObject( 'permaddr' );
	$query    = $permaddr_obj->genStoreQueryString( $_SESSION['ADDRESS_TABLE'] );
	$permaddr = $permaddr_obj->checksum( $_SESSION['ADDRESS_TABLE'] );
	$tempaddr = $permaddr;
	
	// echo '<br>Perm Query: ' . $query;
	echo '<br><span class="info">Állandó lakcím rögzítése: </span>';
	try {
	  $db->Execute( $query );
	  echo '<span class="info_green">az új cím létrehozása sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE PERMANENT ADDRESS', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE PERMANENT ADDRESS', $query );
	}
	catch( exception $e ) {
	  echo '<span class="info_orange">ez a cím már létezik, felhasználom!</span>';
	}

	if( ! $permaddr_obj->islett->isChecked() ) {
	  $tempaddr_obj = $this->getContentObject( 'tempaddr' );
	  $query    = $tempaddr_obj->genStoreQueryString( $_SESSION['ADDRESS_TABLE'] );
	  $tempaddr = $tempaddr_obj->checksum( $_SESSION['ADDRESS_TABLE'] );

	  // echo '<br>Lett Query: ' . $this->getContentObject( 'tempaddr' )->genStoreQueryString( 'test' );	  
	  echo '<br><span class="info">Ideiglenes lacím rögzítése: </span>';  
	  try {
		$db->Execute( $query );
		echo '<span class="info_green">az új cím létrehozása sikerült!</span>';
		// WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE TEMPORARY ADDRESS', $query );
		WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE TEMPORARY ADDRESS', $query );
	  }
	  catch( exception $e ) {
		echo '<span class="info_orange">ez a cím már létezik, felhasználom!</span>';
	  }
	}

	$this->genQueryArray( array( 'permaddr', 'tempaddr' ), false );
	$this->putQueryArray( array( 'permaddr' => WA_String::sqlfmt( $permaddr ), 'tempaddr' => WA_String::sqlfmt( $tempaddr ) ) );
	
	$ptype = 'guest';
	if( isset( $this->opts['ptype'] ) ) {
	  $ptype = $this->opts['ptype'];
	}
	$this->putQueryArray( array( 'ptype' => WA_String::sqlfmt( $ptype ) ) );

	$id = $this->getContentObject( 'id' );
	
	// $this->putQueryArray( array( 'pid' => WA_String::sqlfmt( md5( $id->get_val() ) ) ) );
	$this->putQueryArray( array( 'exprydate' => $this->getContentObject( 'leavedate' )->genQueryFooter() ) );
	// $this->putQueryArray( array( 'isvalid' => WA_String::sqlfmt( 't' ) ) );
	$this->putQueryArray( array( 'lastmtime' => WA_String::sqlfmt( WA_String::timenow() ) ) );

	// Final Query
	$table = $_SESSION['PERSON_TABLE'];
//	$table = $_SESSION['GUEST_TABLE'];
//	if( $this->isopt( 'ptype', 'student' ) ) {
//	  $table = $_SESSION['STUDENT_TABLE'];
//	}

	$query = $this->genStoreQueryString( $table );

	echo '<br><span class="info">Felhasználó rögzítése: </span>';
	try {
	  $db->Execute( $query );
	  echo '<span class="info_green">létrehozás sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE USER', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE USER', $query );

	  if( $ptype == 'student' ) {
	  
		$etrid = $this->getContentObject( 'etrid' );
		$id = $this->getContentObject( 'id' );
	  
		$etrval = strtolower( $etrid->get_val() );
		$idval  = strtoupper( $id->get_val() );

		// back search
		$query  = 'SELECT pid,etrid,id FROM '.$table;
		$query .= ' WHERE etrid = '.WA_String::sqlfmt( strtoupper( $etrval ) );
		$query .= ' AND id = '.WA_string::sqlfmt( strtoupper( $idval ) );
		
		try {
		  $rs = $db->Execute( $query );
		}
		catch( exception $e ) {
		  print_query_error( 'Error: ', $e );
		}

		if( $rs->RecordCount() < 1 ) {
		  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
		}
		else {
		  $row = $rs->FetchRow();
		  $pid = $row['pid'];

		  $wid = 'wid-'.$etrval;
	  
		  $password   = strtolower( md5( strtoupper( $etrval ) ) );
		  $identpass  = substr( $password, 0, 6 );
		  $wpass = md5( $idval );
		  $idents = "printer:".$identpass."|samba:".$identpass;
	  
		  $table = $_SESSION['WID_TABLE'];
		  $query  = 'INSERT INTO '.$table.' (wid,wpass,pid,idents)';
		  $query .= ' VALUES('.WA_String::sqlfmt($wid).','.WA_String::sqlfmt($wpass);
		  $query .= ','.WA_String::sqlfmt($pid).','.WA_String::sqlfmt($idents).')';

		  echo '<br><span class="info">WID létrehozása: </span>';
		  // echo $query;

		  try {
			$db->Execute( $query );
			echo '<span class="info_green">létrehozás sikerült!</span>';
			// WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'CREATE USER', $query );
			WA_Session::log_this( $_SESSION['LOGIN_USER'], 'CREATE USER', $query );
		  }
		  catch( exception $e ) {
			echo '<span class="info_red">hibás, már létezik!</span>';
			print_query_error( "Error: ".$query, $e );  
		  }	
		}
	  }
	  //
	}
	catch( exception $e ) {
	  echo '<span class="info_red">hibás, már létezik!</span>';
	  print_query_error( "Error: ".$query, $e );  
	}

  }
  // Record end
  
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

  // TODO: form automatism
  protected function init_from_sql( $db, $rs, $guide ) {
	$row = $rs->FetchRow();
	// echo '<br><br>'; print_r($row); echo '<br>';
	$ptype = $this->opts['ptype'];
	
	$this->getContentObject( 'name' )->init_from_sql( array( $row['forname'], $row['surname'] ) );
	
	if( $ptype == 'student' ) {
	  $this->getContentObject( 'birthplace' )->init_from_sql( array( $row['birthplace'] ) );
	  $this->getContentObject( 'birthdate' )->init_from_sql( array( $row['birthdate'] ) );
	}
	
	$this->getContentObject( 'sex' )->init_from_sql( array( $row['sex'] ) );

	if( $ptype == 'student' ) {
	  $this->getContentObject( 'mothername' )->init_from_sql( array( $row['mforname'], $row['msurname'] ) );
	  $this->getContentObject( 'fathername' )->init_from_sql( array( $row['fforname'], $row['fsurname'] ) );
	
	  $this->getContentObject( 'faculty' )->init_from_sql( array( $row['faculty'] ) );
	  $this->getContentObject( 'class' )->init_from_sql( array( $row['class'] ) );

	  $this->getContentObject( 'enrollyear' )->init_from_sql( array( $row['enrollyear'] ) );
	}

	$this->getContentObject( 'id' )->init_from_sql( array( $row['id'] ) );

	if( $ptype == 'student' ) {
	  $this->getContentObject( 'etrid' )->init_from_sql( array( $row['etrid'] ) );
	}
	
	if( $ptype == 'student' or $ptype == 'guest' ) {	
	  $this->getContentObject( 'roomid' )->init_from_sql( array( $row['wing'], $row['floor'], $row['number'] ) );
	}
		
	$this->getContentObject( 'occupdate' )->init_from_sql( array( $row['occupdate'] ) );
	$this->getContentObject( 'leavedate' )->init_from_sql( array( $row['leavedate'] ) );
	$this->getContentObject( 'isvalid' )->init_from_sql( array( $row['isvalid'] ) );

	$this->getContentObject( 'tel' )->init_from_sql( array( $row['athome'], $row['atwork'], $row['atmobile'] ) );
	$this->getContentObject( 'email' )->init_from_sql( array( $row['email'] ) );
	$this->getContentObject( 'msn' )->init_from_sql( array( $row['msn'] ) );
	$this->getContentObject( 'skype' )->init_from_sql( array( $row['skype'] ) );
	
	$this->getContentObject( 'permaddr' )->init_from_sql( array( $row['street'], $row['city'],
																 $row['state'], $row['zip'],
																 $row['country'], $row['islett'] ) );	
	
	if( $row['permaddr'] != $row['tempaddr'] ) {
	  $query = 'SELECT * from '.$_SESSION['ADDRESS_TABLE'].' WHERE addrid = '.WA_String::sqlfmt( $row['tempaddr'] );
	  
	  try {
		$rs2 = $db->Execute( $query );
	  }
	  catch( exception $e ) {
		print_query_error( 'Error:', $e );
	  }
	
	  if( $rs2->RecordCount() < 1 ) {
		print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
		return;
	  }
	  else {
		$row2 = $rs2->FetchRow();
		// echo '<br><br>'; print_r($row2); echo '<br>';

		$this->getContentObject( 'tempaddr' )->init_from_sql( array( $row2['street'], $row2['city'],
																 $row2['state'], $row2['zip'],
																 $row2['country'], $row2['islett'] ) );
	  }
	  
	}
  }
}

?>
