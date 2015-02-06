<?php

// cache interface
abstract class ICache {

  abstract function get($key, $expiration = false);

  abstract function set($key, $value);

  abstract function delete($key);
}