<?php

namespace Manadev\Ui\Menus\Items;

use Manadev\Core\App;
use Manadev\Core\Object_;
use Manadev\Core\Promise;
use Manadev\Ui\Menus\Module;
use Manadev\Ui\Menus\Views\Menu;

/**
 * Dependencies:
 *      @property Module $module @required
 *      @property Type $type_ @required
 * Basic menu item properties:
 *      @property Menu $parent @required
 *      @property string $name @part
 *      @property string $type @required @part
 *      @property string $modifier @part // CSS modifier
 *      @property int $sort_order @part
 * Named menu items may have title and icon:
 *      @property string $title @required @part
 *      @property string $icon @part
 * Interactive menu items may be enabled disabled, checked/unchecked, may belong to checkbox group:
 *      @property bool $disabled @part
 *      @property bool $checked @part
 *      @property string $checkbox_group @part
 * Some items may have keyboard shortcuts:
 *      @property string $shortcut @part
 * Links (only) have URL to navigate to when pressed:
 *      @property string|Promise $url @required @part
 * Sub-menus (only) may have list of child menu items
 *      @property array $items @required @part
 */
class Item extends Object_
{
    protected function default($property) {
        global $m_app; /* @var App $m_app */

        switch ($property) {
            case 'module': return $m_app->modules['Manadev_Ui_Menus'];
            case 'type_': return $this->module->item_types[$this->type];
        }
        return parent::default($property);
    }
}