<?php

class MySql_To_Json
{

    private $connection;
    public $errors = array();

    /**
     * @param $db_server
     * @param $db_username
     * @param $db_password
     * @param $db_name
     */

    public function __construct($db_server, $db_username, $db_password, $db_name)
    {
        $this->connection = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    }

    /**
     * @param $query
     * @param bool $indented
     * @return bool|string
     */
    public function MySQLtoJSON($query, $indented = false)
    {
        $query = $this->connection->query($query) or die("Unable to execute the query");
        if (!$numFields = $query->columnCount()) {
            $this->errors[] = "Unable to get the number of fields";
            return false;
        }

        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        $json = json_encode($result);
        if ($indented == false)
            return $json;

        $result = '';
        $pos = 0;
        $previous = '';
        $outQuotes = true;
        for ($i = 0; $i <= strlen($json); $i++) {

            // Next char
            $char = substr($json, $i, 1);

            // Inside quote?
            if ($char == '"' && $previous != '\\') {
                $outQuotes = !$outQuotes;

                // End of element? New line and indent
            } elseif (($char == '}' || $char == ']') && $outQuotes) {
                $result .= "\n";
                $pos--;
                for ($j = 0; $j < $pos; $j++)
                    $result .= '	';
            }

            // Add the character to the result string.
            $result .= $char;

            // Beginning of element? New line and indent
            if (($char == ',' || $char == '{' || $char == '[') && $outQuotes) {
                $result .= "\n";
                if ($char == '{' || $char == '[')
                    $pos++;

                for ($j = 0; $j < $pos; $j++)
                    $result .= '	';
            }

            $previous = $char;
        }

        return $result;
    }

}

// Test


$db = new MySql_To_Json('localhost', 'root', '12345', 'testdb');
$res = $db->MySQLtoJSON('Select * from users', true);
var_dump($res);

?>
