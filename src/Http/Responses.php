<?php

namespace Osm\Framework\Http;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Framework\Http\Errors\Errors;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Errors $errors @required
 */
class Responses extends Object_
{
    public $image_content_types = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
    ];

    protected function default($property) {
        global $osm_app; /* @var App $osm_app */

        switch ($property) {
            case 'errors': return $osm_app[Errors::class];
        }
        return parent::default($property);
    }

    public function response($method, $response) {
        if ($response instanceof Response) {
            return $response;
        }

        return $this->$method($response);
    }
    public function html($html) {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->createRaw(Response::class, (string)$html, Response::HTTP_OK,
            ['content-type' => 'text/html']);
    }

    public function plainText($html) {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->createRaw(Response::class, (string)$html, Response::HTTP_OK,
            ['content-type' => 'text/plain']);
    }

    public function json($json) {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->createRaw(Response::class, json_encode($json, JSON_PRETTY_PRINT),
            Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    public function redirect($to, $status = Response::HTTP_MOVED_PERMANENTLY) {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->createRaw(RedirectResponse::class, (string)$to, $status);
    }

    public function image($filename) {
        global $osm_app; /* @var App $osm_app */

        $type = $this->image_content_types[strtolower(pathinfo($filename, PATHINFO_EXTENSION))];
        $name = basename($filename);

        return $osm_app->createRaw(Response::class, file_get_contents($filename), Response::HTTP_OK, [
            'content-type' => $type,
            'content-disposition' => 'inline; filename="'.$name.'"',
        ]);
    }
}