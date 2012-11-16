<?php

// implement as an interface !!!
class WA_ModifyTable extends WA_ContainerObject {
  public function __construct( $id = 'WA_ModifyTable', $titles, $sizes, $rs, $opts = array() ) {  
	parent::__construct( $id, 'vertical' );
	
	$this->rs = $rs;
	$this->titles = $titles;
	$this->sizes  = $sizes;
	
	// flags
	$this->is_colors = false;
	if( isset( $opts['colors'] ) ) {
	  $this->is_colors = $opts['colors'];
	}
	
	$this->mf = 'pid';
	if( isset( $opts['mf'] ) ) {
	  $this->mf = $opts['mf'];
	}

	$this->ptype = 'student';
	if( isset( $opts['ptype'] ) ) {
	  $this->ptype = $opts['ptype'];
	}

	$header = new WA_ContainerObject( $this->id.'_header', 'horizontal' );
	$header->css_class = 'table_header';
	$c = 0;
	foreach( $titles as $k => $v ) {
  	  $l = new WA_LabelObject( $this->id.'_header_title_'.$c, $k );
	  $l->css_class = 'table_title';
	  $header->putObject( $l );
	  $header->tdwidth[$c] = $sizes[$c];
	  ++$c;
	}
	$this->putObject( $header );

	
	$c = 0;
	foreach( $rs as $row ) {
	  $rs_cont = new WA_ContainerObject( $this->id.'_rs_'.$c, 'horizontal' );
	  $rs_cont->css_class = 'row'.($c%2);  

	  if( $this->is_colors ) {
		if( isset( $row['sex'] ) ) {
		  if( $row['sex'] == 'F' ) {
			$class = 'pink';
		  }
		  else {
			$class = 'blue';
		  }
		}
		
		if( isset( $row['valid'] ) ) {
		  if( $row['valid'] == 't' ) {
			$class = 'green';
		  }
		  else {
			$class = 'orange';
		  }
		}
		
		if( isset( $row['expires'] ) ) {
		  $expry = strtotime( $row['expires'] );
		  $today = strtotime( "now" );
		  if( $expry < $today ) {
			if( $class == 'orange' ) {
			  $class = 'purple';
			}
			else {
			  $class = 'red';
			}
		  }
		}

	  }
	  else {
		$class = 'black';
	  }


	  $cc = 0;
	  foreach( $titles as $k => $v ) {
		if( is_array( $v ) ) {
		  if( isset( $v['type'] ) ) {
			$type = $v['type'];
			if( $type == 'img' ) {
			  $group = $v['group'];
			
			  $title = '';
			  foreach( $group as $k => $v ) {
				if( $row[$k] == 't' ) {
				  $title .= WA_Session::path_img( $v ).'&nbsp;';
				}
				else {
				  $title .= WA_Session::path_img( 'empty.png' ).'&nbsp;';			  
				}
			  }
			  // echo $str;
			  $r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, $title );
			  $r->css_class = 'noborder';
//			  $rs_cont->putObject( $r );
			}
			else if( $type == 'group' ) {
			  $group = $v['group'];
			
			  $title = '';
			  $first = true;
			  foreach( $group as $k ) {
				if( ! $first ) {
				  $title .= ' ';
				}
				$title .= $row[$k];
				$first = false;			  
			  }
			  // echo $str;
			  $r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, $title );
			  $r->css_class = $class;
			  // $r->css_class = 'noborder';			  
			}
		  }
		  else if( isset( $v['action'] ) ) {
			if( preg_match( "/del/", $v['action'] ) ) {
			  if( isset( $v['action_url'] ) ) {
				$url = $v['action_url'].'&mid='.$row['mid'];
			  }
			  else {
				$url = $_SESSION['INDEX'].'?m=add_user&action='.$v['action'].'&ptype='.$row['ptype'].'&pid='.$row[$this->mf];
			  }
			  // $url = $_SESSION['INDEX'].'?m=del_user&pid='.$row[$this->mf];
			  if( isset( $v['action_icon'] ) ) {
				$title = WA_Session::path_img( $v['action_icon'] );
			  }
			  else {
				$title = $v['action_title'];
			  }
			  $r = new WA_HrefObject( $this->id.'_rs_value_'.$c.$cc, $title , $url );
			  $r->css_class = 'noborder';			
			}
			else if( preg_match( "/upd/", $v['action'] ) ) {
			  if( isset( $v['action_icon'] ) ) {
				$title = WA_Session::path_img( $v['action_icon'] );
			  }
			  else {
				$title = $v['action_title'];
			  }
			  
			  if( isset( $v['action_url'] ) ) {
				if( $this->ptype != 'nopid' ) {
				  $url = $v['action_url'].'&mtype=pid&mid='.$row['mid'].'&pid='.$row['pid'].'&vlan='.$row['vl_id'];
				}
				else {
				  $url = $v['action_url'].'&mtype=nopid&mid='.$row['mid'].'&vlan='.$row['vl_id'];				
				}
			  }
			  else {		  
				$url = $_SESSION['INDEX'].'?m=add_user&action='.$v['action'].'&ptype='.$row['ptype'].'&pid='.$row[$this->mf];
			  }
			  $r = new WA_HrefObject( $this->id.'_rs_value_'.$c.$cc, $title, $url );
			  $r->css_class = 'noborder';
			}
			else if( preg_match( "/sel/", $v['action'] ) ) {
			  if( isset( $v['action_icon'] ) ) {
				$title = WA_Session::path_img( $v['action_icon'] );
			  }
			  else {
				$title = $v['action_title'];
			  }
			  if( $this->ptype == 'wifi' ) {
				$roomid = 'WIFI';
			  }
			  else {
				$roomid = $row['roomid'];
			  }

			  if( isset( $v['action_url'] ) ) {
				$url = $v['action_url'].'&mac='.$row['mac'];
			  }
			  else {
				$tail = '';
				if( isset( $opts['form_opts'] ) ) {
				  $fo = $opts['form_opts'];
				  if( isset( $fo['pid'] ) ) {
					$oldpid = $fo['pid'];
					$newpid = $row[$this->mf];
					if( isset( $fo['mid'] ) and isset( $fo['vlan'] ) ) {
					  if( $oldpid == $newpid ) {
						$tail = '&mid='.$fo['mid'].'&vlan='.$fo['vlan'];
					  }
					  else { // TODO: better intell.
						$tail = '&mid='.$fo['mid'].'&vlan='.$fo['vlan'];
					  }
					}
				  }
				}
				$url = $_SESSION['INDEX'].'?m=sel_vlan&action='.$v['action'].'&ptype='.$row['ptype'].'&roomid='.$roomid.'&pid='.$row[$this->mf].$tail;
			  }
			  $r = new WA_HrefObject( $this->id.'_rs_value_'.$c.$cc, $title, $url );
			  $r->css_class = 'noborder';
			}
		  }		  
		  else {
			$r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, 'Error' );
			$r->css_class = $class;
		  }
		}
		else {
		  $r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, $row[$v] );
		  $r->css_class = $class;
		}
		$rs_cont->putObject( $r );
		$rs_cont->tdwidth[$cc] = $sizes[$cc];
		++$cc;
	  }
	  ++$c;
	  $this->putObject( $rs_cont );
	}
	
  }
}

?>
