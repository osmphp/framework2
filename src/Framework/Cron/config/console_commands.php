<?php

use Manadev\Framework\Cron\Commands;

return [
    'scheduled-jobs' => [
        'description' => m_("Processes scheduled jobs"),
        'class' => Commands\Process::class,
    ],
];