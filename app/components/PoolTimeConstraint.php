<?php


namespace App\components;


use Spatie\Async\Pool;
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
    public $timeConstraint = 60 * 60;

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
                    $process->stop();
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
}