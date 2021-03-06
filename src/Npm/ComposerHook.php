<?php

namespace Osm\Framework\Npm;

use Osm\Framework\Composer\Hook;
use Osm\Framework\Processes\Process;

class ComposerHook extends Hook
{
    public $events = ['post-create-project', 'post-update'];

    public function run() {
        if (!Process::runBuffered('npm -v')) {
            return;
        }
        if (env('NO_NPM_WEBPACK')) {
            return;
        }

        Process::runInConsoleExpectingSuccess('php run config:npm', true);
        Process::runInConsoleExpectingSuccess('npm install', true);
    }
}