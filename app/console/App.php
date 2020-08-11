<?php
namespace App\console;

use App\components\LeadProcessor;
use App\components\LeadResult;

use App\components\LeadResultInterface;
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
    public static $leadsNum;
    public static $timeout;

    /**
     * @var Logger
     */
    public static $logger;

    /**
     * App constructor.
     */
    public function __construct(array $config)
    {
        self::$leadsNum = $config['leadsNum'] ? $config['leadsNum'] : 10000;
        self::$timeout = $config['timeout'] ? $config['timeout'] : 60 * 10;
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

        $generator->generateLeads(App::$leadsNum, function (Lead $lead) use ($pool) {
            // добавляем обработку лида в пул
            $pool->add(function () use ($lead) {
                return LeadProcessor::getInstance()->process($lead);
            })->then(function ($output) {
                // условие в связи с особенностью реализации Spatie\Async\Process\ProcessCallback
                if ($output instanceof LeadResultInterface) {
                    App::$logger->writeLine($output->toString());
                }
            })->catch(function (\Throwable $exception) {
                file_put_contents('runtime/errors.txt', $exception->getMessage());
            })->timeout(function () {
                file_put_contents('runtime/errors.txt', 'timeout');
            });
        });

        $pool->wait();

        $executionEndTime = microtime(true);

        $executionTime = $executionEndTime - $executionStartTime;

        echo $pool->status() . PHP_EOL;

        echo "completed in {$executionTime} seconds" . PHP_EOL;

        return;
    }
}