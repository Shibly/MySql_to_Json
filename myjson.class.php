<?php

class MyJSON
{
    /**
     * @var array
     *
     */
    public $errors = array();


    /**
     * public SQLtoJSON
     * Converts from MySQL Query to JSON string.
     * If $indented is false (default), the result will not be indented
     *
     * @param string query
     * @param bool indented
     *
     * @return bool|string
     */
    public function SQLtoJSON($query, $indented = false)
    {
        $query = mysql_query($query) or die ('MyJSON - SQLtoJSON - Cannot make query');

        if (!$numFields = mysql_num_fields($query)) {
            $this->errors[] = 'SQLtoJSON - Cannot get number of MySQL fields';
            return false;
        }

        $fields = array();
        for ($i = 0; $i < $numFields; $i++)
            $fields[$i] = mysql_field_name($query, $i);

        if (!$numRows = mysql_num_rows($query)) {
            $this->errors[] = 'SQLtoJSON - Cannot get number of MySQL rows';
            return false;
        }

        $res = array();
        for ($i = 0; $i < $numRows; $i++) {
            $res[$i] = array();
            for ($j = 0; $j < count($fields); $j++)
                $res[$i][$fields[$j]] = mysql_result($query, $i, $j);
        }

        $json = json_encode($res);
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


    /**
     * public JSONtoSQL
     * Converts from JSON to some MySQL queries
     *
     * @param string json
     * @param string table
     *
     * @return bool
     */
    public function JSONtoSQL($json, $table)
    {
        $tmpjson = json_decode($json);
        $json = array();
        foreach ($tmpjson as $index => $value) {
            $json[$index] = (array)$value;
        }

        $json_fields = array();
        foreach ($json[0] as $field => $value) {
            $json_fields[] = $field;
        }


        // Get MySQL rows
        $sel = mysql_query("SELECT * FROM $table") or die ('MyJSON - JSONtoSQL - Cannot get MySQL rows');

        $rows = array();
        for ($i = 0; $i < mysql_num_fields($sel); $i++)
            $rows[$i] = mysql_field_name($sel, $i);

        // Test recived data....
        for ($i = 0; $i < count($rows); $i++) {
            if ($rows[$i] != $json_fields[$i]) {
                $this->errors[] = 'MySQL table fields are not the same as the JSON or are not in the same order';
                return false;
            }
        }

        // All ok, make query....
        $qry = "INSERT INTO $table(";

        foreach ($rows as $row)
            $qry .= "`$row`, ";

        $qry = substr($qry, 0, strlen($qry) - 2) . ') VALUES (';

        foreach ($json as $field => $value) {
            $values = null;

            foreach ($value as $n_field => $n_value) {
                if (empty($n_value))
                    $values .= 'NULL, ';
                elseif (is_numeric($n_value))
                    $values .= "$n_value, "; else
                    $values .= "'$n_value', ";
            }

            $queries[] = $qry . substr($values, 0, strlen($values) - 2) . ')';
        }

        foreach ($queries as $query)
            mysql_query($query) or die ('MyJSON - JSONtoSQL - Query failed: ' . mysql_error());

        return true;
    }
}