<?php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\FirePHPHandler;
use \Monolog\Formatter\LineFormatter;
/**
 * @class WP_LOGGER
 */
class WP_LOGGER
{
    /**
     * Single instance of the class.
     *
     * @var WP_LOGGER
     */
    protected static $_instance = null;

    /**
     * WP_LOGGER instance.
     *
     * @static
     * @return WP_LOGGER - Main instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * WP_LOGGER Constructor.
     */
    public function __construct()
    {
        global $wp_logger;
        $this->deleteLog($wp_logger['wp_logger_log_retention']);
    }

    function init() {
        global $wp_logger;
        if($wp_logger['wp_logger_type']=='file') {
            $this->_initFileLogger();
        }
    }


    function _initFileLogger() {
        global $wp_logger;
        if ( ! class_exists( 'WP_LOGGER_FILE' ) ) {
            include_once dirname( __FILE__ ).'/class-wp-logger-file.php';
        }
    
        if(!(boolean)$wp_logger['wp_logger_log_admin_request'] && is_admin() ) {
            // wp_die('do not log admin');
            return false;
        }
        $loggerFile = WP_LOGGER_FILE::instance();
    
        if((boolean)$wp_logger['wp_logger_log_request_headers'] ) {
            $loggerFile->logger->info('Request Headers', $this->getRequestHeaders());
        }
        
        $loggerFile->logger->info('Request', $this->getFinalData());
    
    }
    
    function getFinalData() {
        return array(
            "Server" => $this->getServerParams(),
            "Request" => $_REQUEST
        );
    }
    
    function getServerParams() {
        return array(
            "REQUEST_URI" => $_SERVER['REQUEST_URI'],
            "REQUEST_METHOD" => $_SERVER['REQUEST_METHOD'],
            "IP" => $this->get_client_ip(),
            'php_version' => phpversion()
        );
    }
    
    function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
    
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP'). ' - '. 'HTTP_CLIENT_IP';
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR'). ' - '. 'HTTP_X_FORWARDED_FOR';
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED'). ' - '. 'HTTP_X_FORWARDED';
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR'). ' - '. 'HTTP_FORWARDED_FOR';
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED'). ' - '. 'HTTP_FORWARDED';
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR'). ' - '. 'REMOTE_ADDR';
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }


    function deleteLog($days) {
        $files = glob(WP_LOGGER_UPLOAD_DIR."/*");
        $now   = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) { // 2 days
                    unlink($file);
                }
            }
        }
    }
}