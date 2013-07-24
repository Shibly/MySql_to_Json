<?php

class MySql_To_Json
{

    private $connection;
    public $errors = array();

    public function __construct($db_server, $db_username, $db_password, $db_name)
    {
        $this->connection = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    }

    public function MySQLtoJSON($query)
    {
        $query = $this->connection->query($query) or die("Unable to execute the query");
        if (!$numFields = $query->columnCount()) {
            $this->errors[] = "Unable to get the number of fields";
            return false;
        }

        $fields = array();
        $colNames = array();

        for ($i = 0; $i < $numFields; $i++) {
            $fields[$i] = $query->getColumnMeta($i);
            foreach ($fields as $field) {
                $colNames[] = $field['name'];
            }
        }

        if (!$numRows = $query->rowCount()) {
            $this->errors[] = "Unable to get the number of rows";
            return false;
        }

        return $numRows;


        /*
           $fields = array();
           $colName = array();
           for ($i = 0; $i < $numFields; $i++) {
               $fields[$i] = $query->getColumnMeta($i);
               foreach ($fields as $field) {
                   $colName[] = $field['name'];
               }
           }
           if (!$numRows = $query->rowCount()) {
               $this->errors[] = "Can not get the number of rows";
               return false;
           }

           $res = array();
           for ($i = 0; $i < $numRows; $i++) {
               for ($j = 0; $j < count($colName); $j++) {
                   $res[$i][$colName[$j]] = $query->fetch();
               }
           }
           $json = json_encode($res);
           return $json;
        */
    }

}

// Test


$db = new MySql_To_Json('localhost', 'root', 'rivergod', 'drupal');
$res = $db->MySQLtoJSON('Select * from users');
var_dump($res);

?>
