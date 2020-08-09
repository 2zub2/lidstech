<?php
namespace App\console;

use App\components\LeadProcessor;
use App\components\LeadResult;

use App\components\Logger;
use App\components\PoolTimeConstraint;
use LeadGenerator\Generator;
use LeadGenerator\Lead;

/**
 * Class App
 * @package App\console
 */
class App
{
    /**
     * @var Logger
     */
    public static $logger;

    /**
     * App constructor.
     */
    public function __construct()
    {
        self::$logger = Logger::getInstance();
    }

    /**
     * запуск приложения
     */
    public function run()
    {
        $generator = new Generator();

        // создаем асинхронный пул
        $pool = PoolTimeConstraint::create();

        $executionStartTime = microtime(true);

        $generator->generateLeads(10000, function (Lead $lead) use ($pool) {
            // добавляем обработку лида в пул
            $pool->add(function () use ($lead) {
                return LeadProcessor::getInstance()->process($lead);
            })->then(function (LeadResult $output) {
                App::$logger->writeLine($output->toString());
            })->catch(function (\Throwable $exception) {
                file_put_contents('runtime/errors.txt', $exception->getMessage());
            })->timeout(function () {
                file_put_contents('runtime/errors.txt', 'timeout');
            });
        });

        $pool->wait();

        $executionEndTime = microtime(true);

        $executionTime = $executionEndTime - $executionStartTime;

        echo "completed in {$executionTime} seconds" . PHP_EOL;

        return;
    }
}