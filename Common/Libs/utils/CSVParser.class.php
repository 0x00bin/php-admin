<?php
// csv file parser
class CSVParser
{
    static private $_out_charset = 'UTF-8';
    static private function error_handle($msg, $throw_exception) {
        if ($throw_exception) {
            throw_exception($msg);
        }
       // Log::write($msg);
        return array();
    }
    static public function parser($file, $params = array(), $throw_exception=false) {
        if ( !file_exists($file)) {
            return self::error_handle("$file not exists.", $throw_exception);
        }
        self::_default_params($params);

        $content = file_get_contents($file);
        if (isset($params['charset']) && $params['charset'] != self::$_out_charset ){
            $content = @iconv($params['charset'], self::$_out_charset, $content);
        }
        $rows = explode($params['line_separator'], $content);
        $datas = array();
        $names = array(); // ×Ö¶ÎÃûË÷Òý names => array(0 => 'colname1', 1 => 'colname2',...)
        if (isset($params['names'])) {
            $names = $params['names'];
        } else if ($params['has_header']) {
            $names = str_getcsv($rows[0], $params['col_separator']);
            foreach($names as $i => $name) {
                $name = self::_trim($name);
                if (empty($name)) {
                    return self::error_handle("A col is empty:".var_export($names, true), $throw_exception);
                }
                if (isset($params['name_maps']) && isset($params['name_maps'][$name])) {
                    $names[$i] = $params['name_maps'][$name];
                } else {
                    $names[$i] = $name;
                }
            }
            unset($rows[0]);
        }
        foreach( $rows as $row ) {
            $row = trim($row);
            if (empty($row)) continue;
            $values = str_getcsv($row, $params['col_separator']);
            $data = array();
            foreach($values as $i => $value) {
                $key = isset($names[$i])? $names[$i]:$i;
                $data[$key] = self::_trim($value);
            }
            $datas[] = $data;
        }
        return $datas;
    }

    static private function _default_params(&$params) {
        if ( !isset($params['line_separator']) ) {
            $params['line_separator'] = "\n";
        }

        if ( !isset($params['col_separator']) ) {
            $params['col_separator'] = "\t";
        }

        if ( !isset($params['has_header']) ) {
            $params['has_header'] = false;
        }
    }
    static private function _trim($value) {
        if ($value === "") {
            return "";
        }
        $value = trim($value); // chr 239.187.191 is utf8 BOM
        $value = str_replace(chr(239).chr(187).chr(191), '', $value);
        if ($value === "") return "";
        if ($value[0] == '"' && $value[strlen($value)-1] == '"') {
            return substr($value, 1, strlen($value)-2);
        }
        return $value;
    }
}
