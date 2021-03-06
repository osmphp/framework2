<?php

namespace Osm\Framework\Migrations;

use Osm\Framework\Installer\Question;
use Osm\Framework\Processes\Process;

class InstallationQuestion extends Question
{
    public function ask() {
        if (!$this->usesDb()) {
            return;
        }

        $value = $this->output->ask(osm_t("Enter database server name"), env('DB_HOST', 'localhost'),
            $this->required);
        Process::runInConsoleExpectingSuccess("php run env DB_HOST={$value}", true);
        putenv("DB_HOST={$value}");

        $value = $this->output->ask(osm_t("Enter database name"), env('DB_NAME'), $this->required);
        Process::runInConsoleExpectingSuccess("php run env DB_NAME={$value}", true);
        putenv("DB_NAME={$value}");

        $value = $this->output->ask(osm_t("Enter database user name"), env('DB_USER'), $this->required);
        Process::runInConsoleExpectingSuccess("php run env DB_USER={$value}", true);
        putenv("DB_USER={$value}");

        if (!env('DB_PASSWORD') || $this->yes != $this->output->choice(
            osm_t("Reuse database user password you entered last time?"),
            [$this->no, $this->yes], $this->yes))
        {
            $value = $this->output->askHidden(osm_t("Enter database user password"), $this->required);
            Process::runInConsoleExpectingSuccess("php run env -q DB_PASSWORD={$value}");
            putenv("DB_PASSWORD={$value}");
        }
    }

    protected function usesDb() {
        if (env('DB_NAME')) {
            return true;
        }

        return $this->yes == $this->output->choice(osm_t("Will your project use database?"),
            [$this->no, $this->yes], $this->yes);
    }
}