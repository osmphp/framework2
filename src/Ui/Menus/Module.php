<?php

namespace Osm\Ui\Menus;

use Osm\Core\App;
use Osm\Core\Modules\BaseModule;
use Osm\Framework\Areas\Area;
use Osm\Ui\Menus\Items\Types;

/**
 * @property Types $item_types @required
 */
class Module extends BaseModule
{
    protected function default($property) {
        global $osm_app; /* @var App $osm_app */

        switch ($property) {
            case 'item_types': return $osm_app->cache->remember('menu_item_types', function($data) {
                return Types::new($data);
            });
        }
        return parent::default($property);
    }
}