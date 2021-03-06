<?php

namespace Osm\Framework\Http\Advices;

use Osm\Core\App;
use Osm\Framework\Areas\Area;
use Osm\Framework\Http\Advice;
use Osm\Framework\Http\Exceptions\NotFound;
use Osm\Framework\Http\Request;

/**
 * @property Area $area @required
 * @property Request $request @required
 */
class DetectRoute extends Advice
{
    protected function default($property) {
        global $osm_app; /* @var App $osm_app */

        switch ($property) {
            case 'request': return $osm_app->request;
            case 'area': return $osm_app->area_;
        }
        return parent::default($property);
    }

    public function around(callable $next) {
        global $osm_app; /* @var App $osm_app */

        if (!isset($osm_app->controller)) {
            $osm_app->controller = $this->findController();
        }

        return $next();
    }

    protected function findController() {
        if (!isset($this->area->controllers["{$this->request->method} {$this->request->route}"])) {
            throw new NotFound(osm_t("Page not found"));
        }

        $controller = $this->area->controllers["{$this->request->method} {$this->request->route}"];

        if ($controller->abstract) {
            throw new NotFound(osm_t("Page not found"));
        }

        return $controller;
    }
}