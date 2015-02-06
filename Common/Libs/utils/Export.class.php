<?php

function _fputcsv($ostream, $values, $delimiter, $enclosure) {
    $str = "";
    foreach ($values as $value) {
        $value = str_replace(array($enclosure, "\n"), array($enclosure.$enclosure, ""), $value);
        $str .= $enclosure .$value . $enclosure.$delimiter;
    }

    fputs($ostream, substr($str, 0, -1)."\n");

    return strlen($str) - 1;
}

class Export
{
    protected $_in_charset  = 'UTF-8';
    protected $_out_charset = 'UTF-8';

    protected $_delimiter   = ',';
    protected $_enclosure   = '"';
    public function __construct($in_charset='UTF-8', $out_charset='GB2312') {
        $this->_in_charset  = $in_charset;
        $this->_out_charset = $out_charset;
    }

    protected function _output_csv(&$values, $key, $ostream, $need_enclosure=false) {
        @array_walk($values, array(&$this, "_translate"), $key !== '');
        if ($need_enclosure) {
            _fputcsv($ostream, $values, $this->_delimiter, $this->_enclosure);
        } else {
            @fputcsv($ostream, $values, $this->_delimiter, $this->_enclosure);
        }
    }

    private $_dictionaries = array();

    protected function _is_ascii($ch) {
        return $ch >= ord("a") && $ch <= ord("z") ||
               $ch >= ord("A") && $ch <= ord("Z") ||
               $ch >= ord("0") && $ch <= ord("9") ||
               $ch == "_";
    }

    protected function _is_chinese($value) {
        return preg_match("/^[\x7f-\xff]+$/", $value);
    }

    protected function _translate(&$value, $key, $translate=true) {
       // echo "translate: ".var_export($translate,true).", key: $key, value: $value \n<br />";
        //$translate ? parent::_translate($value, $key):'';
        if ($translate && isset($this->_dictionaries[$key])) {
            if (isset($this->_dictionaries[$key][$value])) {
                $value = $this->_dictionaries[$key][$value];
            }
        }
        if (($this->_in_charset != $this->_out_charset) && !empty($value)) {
            if (is_string($value) && $value != 'null') {
                if ( !$this->_is_ascii(substr($value, 0, 1)) ||  $this->_is_chinese($value) ) {
                    $value = @iconv($this->_in_charset, $this->_out_charset, $value);
                }
            }
        }
        //echo "key: $key, value: $value \n<br />";
    }

    private function _send_header($type, $name) {
        header('Pragma: public');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Encoding: none');

        header('Content-type: '.$type);
        header("Content-Disposition: attachment; filename={$name}");
        //header("Pragma: no-cache"); // IE 无法使用 The file could not be written to the cache
       // header('Content-length: '.$filesize);
    }

    // 获取配置的表头，从表格定义table中
    // 解析参数
    // @params $params(array) $params
    // @params $data0 (array) 数据列表的第0行索引数据
    protected function _parser($params, $data0) {
        $thead = array();

        import('libs.Dictionary');
        include_once LIBS_PATH . '/utils/Dictionary.class.php';

        foreach($params['columns'] as $key => $column) {
            $thead[$key] = $column['label'];
            // 读入可以对应的字典名
            if (isset($column['dictionary'])) {
                $this->_dictionaries[$key] = Dictionary::Get($column['dictionary']);
            }
        }

        return $thead;
    }

    // 获取配置的表头，从客户端浏览器POST表头和字段名
    // 解析参数
    // @params $params(array) $params
    // @params $data0 (array) 数据列表的第0行索引数据
    protected function _parser_post($params, $data0) {
        $output = array();
        if ( is_string($params) ) {
            $data = urldecode($params);
            parse_str($data, $output);
        } else {
             $output = $params;
        }

        $thead = array();

        import('libs.Dictionary');

        foreach($data0 as $key => $value) {
            $i = array_search($key, $output['cols']);
            if ($i === false)  {
                $thead[$key] = "$key";
            } else {
                $thead[$key] = $output['labs'][$i];
            }

            // 读入可以对应的字典名
            if (isset($output['dname_'.$key])) {
                if ( !Dictionary::IsV2V($output['dname_'.$key]) )
                    $this->_dict_names[$key] = $output['dname_'.$key];
            }
        }

        return $thead;
    }

    public function csv($filename, $params, $datas) {
        if (!isset($_GET['_debug']) ) {
            $this->_send_header('text/csv', $filename.'.csv');
        } else {
            header('Content-type: text/html; charset=gbk');
        }
        if (empty($datas)) {
            echo "";
            exit;
        }
        $ostream = fopen("php://output", 'w');

        // 写表头
        if (isset($_POST['data'])) {
            $thead = $this->_parser_post($params, current($datas));
        } else {
            $thead = $this->_parser($params, current($datas));
        }
        // 读入字典
        //$this->_load_dictionaries();
        $this->_output_csv($thead, '', $ostream, true);

        array_walk($datas, array(&$this, '_output_csv'), $ostream);
        fclose($ostream);
    }

    public function downtpl($filename, $data) {
        if (!isset($_GET['_debug']) )
            $this->_send_header('text/csv', $filename.'.csv');
        else {
            header('Content-type: text/html; charset=gbk');
        }
        if (empty($data)) {
            echo "";
            exit;
        }
        $ostream = fopen("php://output", 'w');
        $this->_output_csv($data, '', $ostream, true);
        fclose($ostream);
    }
}

?>
