<?php

namespace Osm\Framework\Http\Traits;

use Osm\Core\App;
use Osm\Framework\Areas\Area;
use Osm\Framework\Http\Advices;
use Osm\Framework\Http\Controllers;
use Osm\Framework\Http\Parameters;
use Osm\Framework\Http\Url;

trait AreaTrait
{
    protected function around_default(callable $proceed, $property) {
        global $osm_app; /* @var App $osm_app */

        /* @var Area $area */
        $area = $this;

        switch ($property) {
            case 'advices_': return $osm_app->cache->remember("http_advices.{$area->name}", function($data) use ($area) {
                $definitions = [];

                for (;$area; $area = $area->parent_area_) {
                    $definitions = osm_merge($area->advices ?? [], $definitions);
                }

                return Advices::new(array_merge($data, ['config' => $definitions]));
            });
            case 'controllers': return $osm_app->cache->remember("routes.{$area->name}", function($data) {
                return Controllers::new(array_merge($data, ['area' => $this->name]));
            });
            case 'parameters_':
                $definitions = [];

                for (;$area; $area = $area->parent_area_) {
                    $definitions = osm_merge($area->parameters ?? [], $definitions);
                }

                return Parameters::new(['config_' => $definitions], null, $this);
            case 'query':
                $parsedQuery = [];
                foreach ($area->parameters_ as $parameter) {
                    if (($value = $parameter->parse($osm_app->request->query)) !== null) {
                        $parsedQuery[$parameter->name] = $value;
                    }
                }
                return $parsedQuery;
        }

        return $proceed($property);
    }

    public function setUrl(Url $url) {
        global $osm_app; /* @var App $osm_app */

        /* @var Area $area */
        $area = $this;

        $area->url = $url;
        $osm_app->request->route = mb_substr($osm_app->request->route,
            mb_strlen($url->route_base_url_) - mb_strlen($url->base_url));
    }
}