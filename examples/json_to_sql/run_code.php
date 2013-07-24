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

<title>Example JSON -> SQL</title>
<center>
	<strong>Hello, this example will open the json.txt file and then convert it to MySQL, inserting all in the example2 table</strong><br /><br /><br />
	<?php
		// Get the json.txt content
		$j = file_get_contents('json.txt');
		
		// Do the proccess!
		$json->JSONtoSQL($j, 'example2');
		
		// Check errors
		if(count($json->errors) > 0) {
			echo 'Something went wrong:<br /><br />';
			echo '<pre>'.print_r($json->errors, true).'</pre>';
			die();
		}
		
	?>
	<br /><br />
	<strong>All inserted successfully! Please see now the example2 table! All the json.txt content will be there</strong>
</center>