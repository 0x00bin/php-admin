<?php
if ( !class_exists("RBAC") ) {
    import("libs.RBAC");
}

class NodeScanner
{
    private $_app = '';
    private $_name_tags = array (
        '@_modname' => 'mod',
        '@_actname' => 'act',
        'function' => 'func',
    );

    private $_default_acts = array(
        'add'    => '添加', // alies insert
        'edit'   => '编辑', // alies update
        'delete' => '删除',
        'index'  => '列表', // alies jqgrid
    );

    /* array(
        '@file' => array(
            'name'  => '', // mod name
            'title' => '', // for cn name
            'sub' => array(
                0 => array(
                    'name' => '',  // function name
                    'title' => '', //
                ),
            ),
        );
    );*/
    private $_result = array();
    private $_str_tags = '';

    private $_func_reg = '/function (.*)(\s|)\(/U'; // 匹配function 的正则

    private $_actname = ''; // 放入发现的actname  用于后面匹配到相应的function
    private $_ignores = array();
    private $_alias = array(
         'INSERT' => 'ADD',
         'UPDATE' => 'EDIT',
         'DOCMD'  => 'WEBSHELL',
         'SAVEACCESS'  => 'ASSIGNACCESS',
    );

    // 文件模式
    const MODE_FILE = 0;  // 只处理 本文件模块
    const MODE_DIR  = 1;  // 处理目录中文件及其app
    private $_mode = self::MODE_DIR;

    public function __construct($app='', $mode=NodeScanner::MODE_DIR, $ignores=array()) {
        $this->_app      = $app;
        $this->_mode     = $mode;
        $this->_ignores  = $ignores;
        $this->_str_tags = implode("|",array_keys($this->_name_tags));
    }
    public function setApp($app) {
        $this->_app = $app;
        return $this;
    }
    public function setMode($mode) {
        $this->_mode = $mode;
        return $this;
    }
    public function setIgnores($ignores) {
        $this->_ignores = $ignores;
        return $this;
    }
    public function clear() {
        $this->_app    = '';
        $this->_result = array();
    }

    function scanning($path, $files=array()) {
        if ($this->_mode == self::MODE_DIR) {
            $files = $this->_get_files($path);
        }

        foreach($files as $file) {
            if ( !in_array($file, $this->_ignores) ) {
                $this->_parser($path ."/". $file);
            }
        }

        if ($this->_mode == self::MODE_DIR) {
            $this->_result['app'] = array(
                'name'  => $this->_app,
               // 'title' => $this->_app,
            );
        }

        return ($this->_result);
    }

    private function _parser($file) {
        $this->_log("$file scanning ... ");
        $lines = file($file);
        $modname = $this->_get_mod_name($file);
        $this->_result[$file] = array(
            'name'  => $modname,
            'title' => $modname,
            'sub'   => array(),
        );
        $this->_add_default_acts($file);
        try {
            foreach($lines as $num => $line) {
                $tag = $this->_match($line, $this->_str_tags);
                if ($tag !== false) {
                    $func = $this->_name_tags[$tag];
                    if (empty($func) ) {
                        $this->_log( "line:$line, tag:" . var_export($tag,true));
                    }
                    $this->{'_process_'.$func}($tag, $line, $file, $num+1);
                }
            }
        } catch(Exception $e) {
            $this->_log( "catch exception:". $e->getMessage()."\n");
            exit;
        }
        $this->_remove_alias($file);
    }

    private function _add_default_acts($file) {
        foreach($this->_default_acts as $name => $title) {
            $this->_result[$file]['sub'][$name] = array('title' => $title, 'name' => $name);
        }

        $alias = $this->_alias;
        if ( count($alias) > 0 ) {
            foreach($alias as $name => $title) {
                $this->_result[$file]['sub'][strtolower($name)] = 1;
            }
        }
    }

    private function _log($msg) {
        echo "$msg<br/>\n";
    }

    private function _remove_alias($file) {
        $alias = $this->_alias;
        if ( count($alias) > 0 ) {
            foreach($alias as $name => $title) {
                unset($this->_result[$file]['sub'][strtolower($name)]);
            }
        }
    }

    private function _get_mod_name($file) {
        return strtolower(basename($file,"Controller.class.php"));
    }

    private function _process_mod($tag, $line, $file, $num) {
        if ($this->_actname != '') {
            throw new Exception("file parser error:$file\n$line($num)\nactname:{$this->_actname} not match function.\n");
        }
        $t = explode($tag, $line);
        if (!isset($t[1]) ) {
            throw new Exception("file parser error:$file\n$line($num)");
        }

        $this->_result[$file]['title'] = trim($t[1]);
    }

    private function _process_act($tag, $line, $file, $line_num) {
        if ($this->_actname != '') {
           throw new Exception("file parser error:$file\n$line($line_num)\nactname:{$this->_actname} not match function.\n");
        }
        $t = explode($tag, $line);
        if (!isset($t[1]) ) {
            throw new Exception("file parser error:$file\n$line($line_num)");
        }

        $this->_actname = trim($t[1]);
        //$this->_result[$file]['sub'][$this->_actname] = array('title' => $this->_actname);
    }

    private function _process_func($tag, $line, $file, $line_num) {
        if ( false !== $this->_match($line, 'private|protected') ) {
            //$this->_log( "find private or protected func: in '".$line."'");
            return;
        }

        $matched = preg_match($this->_func_reg, $line, $matchs);

        if ( !$matched || !isset($matchs[1])) {
            throw new Exception("file parser error:$file\n$line($line_num)\nnot matched function name" );
        }
        $func = $matchs[1];
        if ( $func[0] == '_') {
            $this->_log( "find private or protected func: in '".$line."'");
            return;
        }
        if ( $func == 'checkEnv') {
            $this->_log( "find checkEnv: in '".$file."($line_num)'");
            return;
        }
        if ($this->_actname == '') {
            $this->_actname = $func;
        }
        if ( !isset($this->_result[$file]['sub'][strtolower($func)]) ) {
            $this->_result[$file]['sub'][strtolower($func)] = array('title' => $this->_actname, 'name' => $func);
        }
        $this->_actname = '';
    }

    private function _match($string, $words) {
        $matched = preg_match('/'.$words.'/', $string, $result);
        if ( $matched && isset($result[0]) && strlen($result[0]) > 0 )   {
            return $result[0];
        }else{
            return false;
        }
    }

    private function _get_files($dir)
    {
        $d = \dir($dir);
        $files = array();
        while (false !== ($entry = $d->read())) {
            if ( $entry == "." || $entry == ".." ) continue;
            if (is_dir($dir . "/" .$entry)) {
                continue; //
            }
            $files[] = $entry;
        }
        $d->close();
        return $files;
    }
}
?>