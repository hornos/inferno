<?php
  require_once( '/home/inferno/webui/Packages/WebApp/0.3/config.php' );
  if( ! WA_Session::start() ) exit;

  if( ! WA_Session::checklogin() ) exit;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Network Management</title>
<?php
  WA_session::include_css( 'common.css' );
  WA_session::include_css( 'inferno.css' );
  WA_session::include_js( 'codelibrary.js' );
?>

</head>
 
<body onload="check();">
  <table border="0" cellspacing="0" cellpadding="0" width='100%' height='100%'>
    <tr>		
     <td valign="top" class="module" height='100%'>
       <?php WA_Session::get_module();?>
     </td>
   </tr>
 </table>
</body>
</html>

