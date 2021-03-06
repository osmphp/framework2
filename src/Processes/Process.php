<?php

namespace Osm\Framework\Processes;

use Osm\Framework\Processes\Exceptions\ProcessFailed;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    protected static $collectedMessages = false;
    protected static $path = '';

    public static function cd($path, callable $callback) {
        $originalPath = static::$path;
        static::$path = $path;
        try {
            return $callback();
        }
        finally {
            static::$path = $originalPath;
        }
    }

    public static function run($command, callable $callback = null) {
        // realpath(__DIR__ . '/../../../../../..')
        $path = static::$path ?: static::getBasePath();
        $process = SymfonyProcess::fromShellCommandline($command, $path, null, null, null);
        $process->setWorkingDirectory($path);
        return $process->run($callback) == 0;
    }

    public static function mustRun($command, callable $callback = null) {
        $output = '';
        $result = static::run($command, function($type, $buffer) use ($callback, &$output) {
            $output .= $buffer;
            if ($callback) {
                $callback($type, $buffer);
            }
        });

        if (!$result) {
            throw new ProcessFailed("Command '$command' failed unexpectedly with the following output: \n\n$output");
        }
    }

    public static function runInConsole($command, $show = false) {
        if ($show) {
            static::out("> $command\n");
        }

        return static::run($command, function($type, $buffer) {
            static::out($buffer);
        });
    }

    public static function runInConsoleExpectingSuccess($command, $show = false) {
        if (!static::runInConsole($command, $show)) {
            throw new ProcessFailed("Command '$command' failed unexpectedly");
        }
    }

    public static function runBuffered($command) {
        $output = '';
        $result = static::run($command, function($type, $buffer) use (&$output){
            $output .= $buffer;
        });

        return $result ? $output : false;
    }

    public static function start($command, callable $callback) {
        $process = new SymfonyProcess($command, static::getBasePath(), null, null, null);
        $process->start(function($type, $buffer) use ($callback) {
            if (static::$collectedMessages !== false) {
                static::$collectedMessages .= $buffer;
            }
            $callback();
        });
        return $process;
    }

    /**
     * @param SymfonyProcess $process
     * @return bool
     */
    public static function stop($process) {
        return $process->stop(10000) == 0;
    }

    public static function getBasePath() {
        return dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    protected static function out($string) {
        echo $string;
    }

    public static function escape($value) {
        return escapeshellcmd($value);
    }
}