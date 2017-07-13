<?php

/**
 * Created by PhpStorm.
 * User: BrianPark
 * Date: 2017-02-01
 * Time: 오후 12:09
 */
class MY_Log extends CI_Log
{
    /**
     * Sentry Options
     */
    protected $_sentry_options = false;
    protected $_sentry_client = false;

    public function __construct()
    {
        parent::__construct();

        $config =& get_config();

        try {
            // If Raven_Client isn't already defined, include the autoloader
            if (!class_exists('Raven_Client')) {
                require_once $config['sentry_path'];
                Raven_Autoloader::register();
            }
            if (empty($config['sentry_config'])) {
                $this->_sentry_client = new Raven_Client($config['sentry_client']);
            } else {
                $this->_sentry_client = new Raven_Client($config['sentry_client'], $config['sentry_config']);
            }
            $error_handler = new Raven_ErrorHandler($this->_sentry_client);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler();
            $error_handler->registerShutdownFunction();
            $this->_sentry_options = array(
                'sentry_log_threshold' => $config['sentry_log_threshold'],
                'sentry_logging_levels' => $config['sentry_logging_levels'],
                'sentry_logging_level_names' => array_flip($config['sentry_logging_levels'])
            );

        } catch (Exception $e) {
            $this->_sentry_client = false;
            $this->_sentry_options = false;
            // Do nothing, since we don't want to stop loading of the site due
            // to a Sentry miss configuration or error.
        }
    }

    public function write_log($level, $msg)
    {
        if ($this->_enabled === false) {
            return false;
        }
        // make upper case
        $level_upper = strtoupper($level);

        // check logging level
        if (in_array($level_upper, $this->_sentry_options['sentry_logging_levels']) === false
            OR $this->_sentry_options['sentry_logging_level_names'][$level_upper] < $this->_sentry_options['sentry_logging_level_names'][$this->_sentry_options['sentry_log_threshold']]) {
            return false;
        } else {
            $this->_sentry_client->captureMessage($msg, array(), $level, true);
        }
    }
}