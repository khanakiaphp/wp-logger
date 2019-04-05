<?php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\FirePHPHandler;
use \Monolog\Formatter\LineFormatter;
/**
 * @class WP_LOGGER_FILE
 */
class WP_LOGGER_FILE
{
    /**
     * Single instance of the class.
     *
     * @var WP_LOGGER_FILE
     */
    protected static $_instance = null;

    /**
     * WP_LOGGER_FILE instance.
     *
     * @static
     * @return WP_LOGGER_FILE - Main instance
     */
    public static function instance($args=array())
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($args);
        }

        return self::$_instance;
    }

    /**
     * WP_LOGGER_FILE Constructor.
     */
    public function __construct($args=array())
    {
        
        $atts = shortcode_atts(array(
            'name' => 'debug'
        ), $args);
       // create a log channel
        $logger = new Logger($name);
        // Create a handler
        $today = date('Y-m-d');
        $name = $atts['name'];
        $stream = new StreamHandler(WP_LOGGER_UPLOAD_DIR."/"."{$name}-{$today}.log", Logger::DEBUG);
        $logger->pushHandler($stream);
        $logger->pushHandler(new FirePHPHandler());

        // $logger->info('My logger is now ready', array('name' => 'aman'), array('method' => 'POST'));
        $this->logger = $logger;
    }
}
?>