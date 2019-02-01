<?php

namespace Manadev\Framework\Http\Advices;

use Manadev\Core\App;
use Manadev\Framework\Areas\Area;
use Manadev\Framework\Http\Advice;
use Manadev\Framework\Http\Exceptions\NotFound;
use Manadev\Framework\Http\Request;
use Manadev\Framework\Http\Responses;
use Manadev\Framework\Http\UrlGenerator;

/**
 * @property Request $request @required
 * @property Responses $responses @required
 * @property Area $area @required
 * @property UrlGenerator $url_generator @required
 */
class RedirectToTrailingSlash extends Advice
{
    protected function default($property) {
        global $m_app; /* @var App $m_app */

        switch ($property) {
            case 'request': return $m_app->request;
            case 'responses': return $m_app[Responses::class];
            case 'area': return $m_app->area_;
            case 'url_generator': return $m_app->url_generator;
        }
        return parent::default($property);
    }

    public function around(callable $next) {
        try {
            return $next();
        }
        catch (NotFound $e) {
            if ($response = $this->redirect()) {
                return $response;
            }

            throw $e;
        }
    }

    protected function redirect() {
        if ($this->request->route == '/') {
            return null;
        }

        if (mb_strrpos($this->request->route, '/') === mb_strlen($this->request->route) - mb_strlen('/')) {
            $redirectTo = mb_substr($this->request->route, 0, mb_strlen($this->request->route) - mb_strlen('/'));
        }
        else {
            $redirectTo = $this->request->route . '/';
        }

        if (!isset($this->area->controllers["{$this->request->route} {$redirectTo}"])) {
            return null;
        }

        return $this->responses->redirect($this->url_generator->rawUrl(
            "{$this->request->route} {$redirectTo}", $this->request->query));
    }
}