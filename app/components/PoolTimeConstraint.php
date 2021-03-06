<?php


namespace App\components;


use App\console\App;
use Spatie\Async\Pool;
use Spatie\Async\Process\Runnable;
use Spatie\Async\Process\SynchronousProcess;

/**
 * Class PoolTimeConstraint
 * @package App\components
 */
class PoolTimeConstraint extends Pool
{
    /**
     * @var int
     * маскимальное время выполнения 10 минут
     */
    public $timeConstraint;

    /**
     * PoolTimeConstraint constructor.
     */
    public function __construct()
    {
        $this->timeConstraint = App::$timeout;
        parent::__construct();
        $this->status = new PoolStatusShort($this);
    }

    /**
     * переопределяем метод для поддержки ограничения по общему времени ожидания исполнения процессов
     * @param callable|null $intermediateCallback
     * @return array
     */
    public function wait(?callable $intermediateCallback = null): array
    {
        $start = microtime(true);

        while ($this->inProgress) {
            foreach ($this->inProgress as $process) {
                // останавливаем уже запущенные процессы если пул остановлен
                if ($this->stopped) {
                    $this->markAsTimedOut($process);
                    continue;
                }

                if ($process->getCurrentExecutionTime() > $this->timeout) {
                    $this->markAsTimedOut($process);
                }

                if ($process instanceof SynchronousProcess) {
                    $this->markAsFinished($process);
                }
            }

            if (!$this->inProgress) {
                break;
            }

            if ($intermediateCallback) {
                call_user_func_array($intermediateCallback, [$this]);
            }

            $cur = microtime(true);

            // останавливаем пул если общее время выполнения превышает ограничение
            if ($cur - $start > $this->timeConstraint) {
                $this->stop();
            }

            usleep($this->sleepTime);
        }

        return $this->results;
    }

    /**
     * @param callable|Runnable $process
     * @param int|null $outputLength
     * @return Runnable
     */
    public function add($process, ?int $outputLength = null): Runnable
    {
        usleep(10000);
        return parent::add($process, $outputLength);
    }
}