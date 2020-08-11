<?php


namespace App\components;


use Spatie\Async\PoolStatus;

/**
 * Class PoolStatusShort
 * @package App\components
 */
class PoolStatusShort extends PoolStatus
{
    public function __toString(): string
    {
        return $this->lines(
            $this->summaryToString()
        );
    }
}