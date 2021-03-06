<?php

namespace Osm\Framework\Installer;

use Osm\Core\App;
use Osm\Core\Modules\BaseModule;

/**
 * @property Questions|Question[] $questions @required
 * @property Steps|Step[] $steps @required
 * @property Requirements|Requirement[] $requirements @required
 */
class Module extends BaseModule
{
    protected function default($property) {
        global $osm_app; /* @var App $osm_app */

        switch ($property) {
            case 'questions': return $osm_app->cache->remember("installation_questions", function($data) {
                return Questions::new($data);
            });
            case 'steps': return $osm_app->cache->remember("installation_steps", function($data) {
                return Steps::new($data);
            });
            case 'requirements': return $osm_app->cache->remember("installation_requirements", function($data) {
                return Requirements::new($data);
            });
        }
        return parent::default($property);
    }

}