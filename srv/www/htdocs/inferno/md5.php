<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>

<head>
  <title>Inferno Login</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF8">
  <link rel="stylesheet" type="text/css" href="./css/login.css" >
</head>

<body>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top">
	  
    <table class="login" width="420" height="600" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>
	<form action="./md5.php" method="POST">
	<table class="inner_login" width="100%" height="200" border="0" cellpadding="3" cellspacing="0">
	  <tr>
	    <td class="label" align="right">
	      Jelszó:
	    </td>
	    <td class="label" align="left">
	      <input type="password" size="20" name="pass">
	    </td>
	  
	    <td class="label" align="left">
	      <input type="submit" class="button" value="Generálás">
	    </td>
	  </tr>

	  <tr>
	    <td class="label" align="right" width="150">
	    Kód:
            </td>
	    <td class="label" align="left" style="font-size: 22px;" colspan="2">	    
	      <?php
	      if( isset( $_POST['pass'] ) ) {
	        echo md5( $_POST['pass'] );
	      }
	      ?>
	    </td>
	  </tr>

	</table>
	</form>
	
  	</td>
      </tr>	
    </table>

    </td>
  </tr>
</table>

</body>

</html>
