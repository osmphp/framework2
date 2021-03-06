<?php

namespace Osm\Framework\Http;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Profiler;
use Osm\Data\Sheets\Query as SheetQuery;

/**
 * @property array $__items @required
 *
 * @see \Osm\Ui\Tables\Module:
 *      @property SheetQuery $data_table @required Typical name of data table query parameter which returns
 *          fully prepared sheet query
 */
class Query extends Object_
{
    public function __get($property) {
        global $osm_app; /* @var App $app */
        global $osm_profiler; /* @var Profiler $osm_profiler */

        switch ($property) {
            case '__items':
                if ($osm_profiler) $osm_profiler->start(__METHOD__, 'urls');
                try {
                    if ($osm_app->controller) {
                        return $this->__items = $osm_app->controller->query;
                    }

                    return $osm_app->area ? $osm_app->area_->query : [];
                }
                finally {
                    if ($osm_profiler) $osm_profiler->stop(__METHOD__);
                }
        }

        if (($value = parent::__get($property)) !== null) {
            return $value;
        }

        return $this->offsetGet($property);
    }

    public function offsetGet($offset) {
        return $this->__items[$offset];
    }

    public function offsetExists($offset) {
        return isset($this->__items[$offset]);
    }

    public function all() {
        return $this->__items;
    }
}