<?php

namespace Osm\Framework\Views\Views;

use Osm\Framework\Views\View;

/**
 * Constructor arguments:
 *
 * @property string $element @part
 * @property string[] $attributes @required @part
 * @property string $modifier @part
 * @property View[] $items @required @part
 *
 * Computed properties:
 *
 * @property string $modifier_
 * @property View[] $items_ @required
 */
class Container extends View
{
    public $template = 'Osm_Framework_Views.container';

    protected function default($property) {
        switch ($property) {
            case 'modifier_': return $this->modifier;
            case 'items': return [];
            case 'items_': return $this->sortViews($this->items);
            case 'empty': return $this->isEmpty();

            /** @noinspection PhpDuplicateSwitchCaseBodyInspection */
            case 'attributes': return [];
        }
        return parent::default($property);
    }

    protected function isEmpty() {
        foreach ($this->items as $item) {
            if (!$item->empty) {
                return false;
            }
        }

        return true;
    }
}