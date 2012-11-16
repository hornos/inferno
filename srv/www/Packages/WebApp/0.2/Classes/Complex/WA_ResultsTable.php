<?php

// implement as an interface !!!
class WA_ResultsTable extends WA_ContainerObject {
  public function __construct( $id = 'WA_ResultsTable', $titles, $sizes, $rs, $opts = array() ) {  
	parent::__construct( $id, 'vertical' );
	
	$this->rs = $rs;
	$this->titles = $titles;
	$this->sizes  = $sizes;
	
	// flags
	$this->is_colors = false;
	if( isset( $opts['colors'] ) ) {
	  $this->is_colors = $opts['colors'];
	}

	$this->is_numbering = false;
	if( isset( $opts['numbering'] ) ) {
	  $this->is_colors = $opts['numbering'];
	}

	$this->is_divs = true;
	if( isset( $opts['divs'] ) ) {
	  $this->is_divs = $opts['divs'];
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
	  $cc = 0;

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

	  foreach( $titles as $k => $v ) {
		if( is_array( $v ) ) {
		  $type = $v['type'];
		  if( $type = 'img' ) {
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
			$rs_cont->putObject( $r );
		  }
		}
		else {
		  if( $this->is_divs ) {
			$r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, $row[$v] );
			$r->css_class = $class;
			$rs_cont->putObject( $r );
			$rs_cont->tdwidth[$cc] = $sizes[$cc];
		  }
		  else {
		    $r = new WA_LabelObject( $this->id.'_rs_value_'.$c.$cc, $row[$v] );
			$r->css_class = $class;
			$rs_cont->putObject( $r );
			$rs_cont->tdwidth[$cc] = $sizes[$cc];	
		  }
		  ++$cc;
		}
	  }
	  ++$c;
	  $this->putObject( $rs_cont );
	}	
  }
}

?>
