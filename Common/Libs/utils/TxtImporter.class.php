<?php

class ColSeparator {
    const Tab   = 0;
    const Comma = 1;
};

class TxtImporter
{
    private $_error = '';

    public function getError() {
        return $this->_error;
    }

    private function error($error) {
        $this->_error = $error;
        return false;
    }

    public function import(&$params = array()) {
        $file = $this->upload();
        if ($file === false ) {
            return false;
        }

        $params['file']          = $file;
        $params['charset']       = $this->_get_param($params, 'charset', 'UTF-8');
        $params['col_separator'] = $this->_get_param($params, 'col_separator', ColSeparator::Comma);
        return $this->parse($params);
    }

    private function _get_param(&$params, $name, $default) {
        return isset($params[$name])? $params[$name] :
            (isset($_REQUEST[$name])? $_REQUEST[$name] : $default);
    }

    public function upload() {
        if ( empty($_FILES) ) {
            return $this->error("No file upload.");
        }

        $upload = new Org\Util\UploadFile();
        $upload->autoSub    = true;
        $upload->subType    = 'date';
        $upload->dateFormat = 'Y_m_d';
        $upload->saveRule   = 'time';
        $upload->maxSize    = 3145728;
        $upload->allowExts  = array('txt', 'csv');

        if ( !$upload->upload(RUNTIME_PATH."/upload/") ) {
            return $this->error($upload->getErrorMsg());
        } else {
            $info =  $upload->getUploadFileInfo();
            return $info[0]['savepath'] . $info[0]['savename'];
        }
    }

    public function parse(&$params)
    {
        $col_separator = isset($params['col_separator'])? $params['col_separator']:1;
        $col_separator = ($col_separator == 1)? ",":"\t";

        $charset = isset($params['charset'])? $params['charset']:'UTF-8';

        include_once LIBS_PATH . "utils/CSVParser.class.php";
        try {
            $datas = \CSVParser::parser($params['file'], array (
                'has_header'    => true,
                'col_separator' => $col_separator,
                'charset'       => $charset,
            ), true); // true  throw exception

        } catch(\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $datas;
    }
}

