<?php

include_once( __DIR__ . '/FileCache.php' );
// 数据字典类

class Dictionary
{
    static private $_cache_path = 'dictionaries/';  // 基于RUNTIME_PATH的相对路径

    // key => value changeto value => value
    const V2V = '_v2v';

    // Generate dictinary cache params
    static private $_cache_params = array();

    // set all lang is same.
    static private $_dictionaries = array();

    static public function Cache($data, &$err) {

        if (empty($data)) {
            $err = 'nothing data';
            return false;
        }
        try{
            if ( isset($data['id']) ) {
                //Log::write("Cache id:".$data['id']);
                self::_write_cache($data['id'], $data['content']);
            } else {
                foreach($data as $row) {
                    //Log::write("Cache id:".$row['id']);
                    self::_write_cache($row['id'], $row['content']);
                }
            }
        } catch (Exception $e) {
            $err = $e->getMessage();
            return false;
        }
        return true;
    }

    static public function CacheById($data, $id, &$err) {
       //Log::write("CacheById id:{$id}");
       return self::Cache(array('id' => $id, 'content' => $data), $err);
    }

    static private function _write_cache($id, $content) {
        //Log::write("_write_cache id:{$id}");
 	    self::_cache_instance()->set(self::_get_key($id), $content, -1);
    }

    static private function _cache_instance() {
        static $cache = null;
        if (is_null($cache)) {
            $cache = new FileCache(RUNTIME_PATH);
        }
        return $cache;
    }

    static public function IsV2V($id) {
        return ( strpos($id, Dictionary::V2V) !== false );
    }

    static private function _get_cache_data($id) {
        $cache = self::_cache_instance();
        $key  = self::_get_key($id);
        $data = $cache->get($key);
        if ( false === $data || empty($data)) {
            self::_generate_cache($id);
        }

        return $cache->get($key);
    }

    static private function _get_dictionary($id) {
        self::_load_dictionaries();
        if ( isset(self::$_dictionaries[$id]) ){
            return self::$_dictionaries[$id];
        }

        return false;
    }

    static public function Get($id) {
        if (empty($id)) {
            return array();
        }
        if ( self::IsV2V($id) ) {
            return self::_value2value($id);
        }

        if (isset(self::$_cache_params[$id])) {
            $data = self::_get_cache_data($id);
        } else {
            $data = self::_get_dictionary($id);
        }

        if (false !== $data) {
            return $data;
        }

        return array();
    }

    // 交换数组中的键和值
    static public function GetByFlip($id, $charset='utf-8') {
        return array_flip(self::Get($id));
    }

    // 'id_v2v'
    static private function _value2value($id) {
        $id = str_replace(Dictionary::V2V, '', $id);
        $dict = self::Get($id);
        if (empty($dict)) {
            return $dict;
        }
        $values = array_values($dict);
        return array_combine($values, $values);
    }

    static private function _generate_cache($id) {
        $params = self::_get_cache_params($id);
        if ($params === false) {
            throw new Exception("id: {$id} cache params not exist.");
        }

        $service = D($params['service'], "Service");
        if ( !is_object($service) || !method_exists($service, "cacheDictionary")) {
            throw new Exception("{$params['service']} Service or method:cacheDictionary not exist");
        }

        \Think\Log::write("_generate_cache id:{$id}", \Think\Log::INFO);

        if (!$service->cacheDictionary($params) ) {
            throw new Exception($params['service']. "Service:".$service->getError());
        }
    }

    static private function _get_cache_params($id) {
        $idx = $id;
        if (!isset(self::$_cache_params[$idx])) {
            return false;
        }
        $params = self::$_cache_params[$idx];
        $params['key']   = isset($params['key'])? $params['key']: 'id';
        $params['value'] = isset($params['value'])? $params['value']: 'name';

        $params['dict_name'] = $id;

        return $params;
    }

    static public function ClearCache($id) {
        return self::_cache_instance()->delete(self::_get_key($id));
    }

    static public function Translate($id, $key) {
        $dict = self::Get($id);
        return isset($dict[$key])? $dict[$key]:$key;
    }
    static public function GetValue($id, $key) {
        $dict = self::Get($id);
        return isset($dict[$key])? $dict[$key]:NULL;
    }
    static private function _get_key($id) {
        return self::$_cache_path . $id;
    }

    static public function load_params() {
        if ( empty(self::$_cache_params) ) {
            self::$_cache_params = require COMMON_PATH . '/Conf/config.dictionaries.cache.params.php';
        }
    }

    static private function _load_dictionaries() {
        if ( empty(self::$_dictionaries) ) {
            self::$_dictionaries = require COMMON_PATH . '/Conf/config.dictionaries.php';
        }
    }
}

// load configs
Dictionary::load_params();
