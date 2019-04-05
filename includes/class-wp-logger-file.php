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
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * WP_LOGGER_FILE Constructor.
     */
    public function __construct($name)
    {
       // create a log channel
        $logger = new Logger($name);
        
        // $log->pushHandler(new StreamHandler('./wp-logger.log', Logger::WARNING));

        // the default date format is "Y-m-d H:i:s"
        // $dateFormat = "Y n j, g:i a";
        // // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        // $output = "%datetime% > %level_name% > %message% %context% %extra%\n";
        // // finally, create a formatter
        // $formatter = new LineFormatter($output, $dateFormat);

        // Create a handler
        $stream = new StreamHandler(wp_get_upload_dir()["basedir"].'/wp-logger/debug.log', Logger::DEBUG);
        // $stream->setFormatter($formatter);

        $logger->pushHandler($stream);
        $logger->pushHandler(new FirePHPHandler());

        // $logger->info('My logger is now ready', array('name' => 'aman'), array('method' => 'POST'));


        // // add records to the log
        // $logger->warning('Foo');
        // $logger->error('Bar');

        $this->logger = $logger;
    }



}

// WP_LOGGER::instance();
