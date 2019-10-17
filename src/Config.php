<?php
include_once 'Regions/EndpointConfig.php';
include_once 'Regions/LocationService.php';
/**
 *定义代理静态常量，可外部设置
 */
defined('ALIYUN_ENABLE_HTTP_PROXY') || define('ALIYUN_ENABLE_HTTP_PROXY', false);
/**
 *
 */
defined('ALIYUN_HTTP_PROXY_IP') || define('ALIYUN_HTTP_PROXY_IP', '127.0.0.1');
/**
 *
 */
defined('ALIYUN_HTTP_PROXY_PORT') || define('ALIYUN_HTTP_PROXY_PORT', '8888');
