<?php
// Include required files
require 'config.php';
require '../../myjson.class.php';

// Create MySQL connection
$con = mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die ('Something went wrong =/');
mysql_select_db($mysql_db) or die ('Something went wrong =/');

// Make new instance
$json = new MyJSON;
?>

<title>Example SQL -> JSON</title>
<center>
	<strong>Hello, this example will run this query:</strong><br />
	SELECT * FROM example<br />
	<strong>And then display the JSON result non-indented and then indented! :D</strong><br /><br />
	<?php
		// Do the proccess for non-indented
		$ni = $json->SQLtoJSON("SELECT * FROM example");
		
		// Indented proccess
		$i = $json->SQLtoJSON("SELECT * FROM example", true);
		
		// Check errors
		if(count($json->errors) > 0) {
			echo 'Something went wrong:<br /><br />';
			echo '<pre>'.print_r($json->errors, true).'</pre>';
			die();
		}
		
	?>
	<strong>Result:</strong>
	</center><?php echo $ni ?><center>
	<hr>
	</center><?php echo $i ?><center>
	<hr>
	<strong>Enjoy!</strong>
</center>