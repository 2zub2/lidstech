<?php


namespace App\components;


/**
 * Class Logger
 * @package App\components
 */
class Logger
{
    /**
     * @var
     */
    private static $instance;
    /**
     * @var string
     */
    private $logTo = 'runtime/log.txt';

    /**
     * Logger constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Logger
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $str
     * @param bool $suffixNewLine
     */
    public function writeLine(string $str, bool $suffixNewLine = true)
    {
        file_put_contents($this->logTo, $str . ($suffixNewLine ? PHP_EOL : ''), FILE_APPEND);
    }
}