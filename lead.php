<?php

require(__DIR__ . '/vendor/autoload.php');

// проверяем доступность асинхронной обработки
if (!\Spatie\Async\Pool::isSupported()) {
    throw new \Exception('Unsupported async');
}

(new \App\console\App([
    'leadsNum' => getenv('LEADS_NUM'),
    'timeout'  => getenv('TIMEOUT'),
]))->run();


