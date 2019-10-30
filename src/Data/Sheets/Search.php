<?php

namespace Osm\Data\Sheets;

use Osm\Core\Object_;
use Osm\Framework\Data\Traits\CloneableTrait;

/**
 * @property Sheet $parent @required
 * @property string $set @part
 *
 * @property string $for @part
 * @property int $limit @part
 * @property int $offset @part
 * @property int $id @part
 */
abstract class Search extends Object_
{
    use CloneableTrait;

    const FOR_DISPLAY = 'for_display';

    /**
     * @var string[] @required @part
     */
    public $columns = [];

    /**
     * @return SearchResult
     */
    abstract public function get();

    /**
     * @param string[] ...$columns
     * @return Search
     */
    public function select(...$columns) {
        $this->registerMethodCall(__FUNCTION__, ...$columns);

        foreach ($columns as $column) {
            $this->columns[$column] = $column;
        }

        return $this;
    }

    public function limit($limit) {
        $this->registerMethodCall(__FUNCTION__, $limit);

        $this->limit = $limit;

        return $this;
    }

    public function offset($offset) {
        $this->registerMethodCall(__FUNCTION__, $offset);

        $this->offset = $offset;

        return $this;
    }

    public function id($id) {
        $this->registerMethodCall(__FUNCTION__, $id);

        $this->id = $id;

        return $this;
    }

    public function forDisplay() {
        $this->for = static::FOR_DISPLAY;
    }

    protected function getColumnDefinition($name) {
        return $this->parent->columns_[$name];
    }

    protected function query() {
        return $this->parent->query($this->set);
    }
}