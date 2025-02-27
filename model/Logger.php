<?php

class Logger {
    private $logFile;
    private $logLevel;
    
    // Log levels
    const ERROR = 0;
    const WARNING = 1;
    const INFO = 2;
    const DEBUG = 3;
    
    /**
     * Constructor
     * 
     * @param string $logFile Path to log file
     * @param int $logLevel Minimum log level to record
     */
    public function __construct($logFile = 'sms_system.log', $logLevel = self::INFO) {
        $this->logFile = $logFile;
        $this->logLevel = $logLevel;
    }
    
    /**
     * Log error messages
     * 
     * @param string $message Error message
     * @param array $context Additional context data
     */
    public function error($message, array $context = []) {
        if ($this->logLevel >= self::ERROR) {
            $this->log('ERROR', $message, $context);
        }
    }
    
    /**
     * Log warning messages
     * 
     * @param string $message Warning message
     * @param array $context Additional context data
     */
    public function warning($message, array $context = []) {
        if ($this->logLevel >= self::WARNING) {
            $this->log('WARNING', $message, $context);
        }
    }
    
    /**
     * Log informational messages
     * 
     * @param string $message Info message
     * @param array $context Additional context data
     */
    public function info($message, array $context = []) {
        if ($this->logLevel >= self::INFO) {
            $this->log('INFO', $message, $context);
        }
    }
    
    /**
     * Log debug messages
     * 
     * @param string $message Debug message
     * @param array $context Additional context data
     */
    public function debug($message, array $context = []) {
        if ($this->logLevel >= self::DEBUG) {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    /**
     * Main logging method
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     */
    private function log($level, $message, array $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = '';
        
        if (!empty($context)) {
            $contextStr = ' - ' . json_encode($context);
        }
        
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        // Log to file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        // Also log to PHP error log for critical issues
        if ($level === 'ERROR') {
            error_log("SMS System Error: $message");
        }
    }
    
    /**
     * Set the minimum log level
     * 
     * @param int $level Minimum log level
     */
    public function setLogLevel($level) {
        $this->logLevel = $level;
    }
}