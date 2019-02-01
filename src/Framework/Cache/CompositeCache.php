<?php

namespace Manadev\Framework\Cache;

use Manadev\Core\Profiler;
use Manadev\Framework\KeyValueStores\Store;


/**
 * @property array $store @required @part
 * @property array $tag_store @required @part
 * @property Store $store_ @required @part
 * @property Store $tag_store_ @required @part
 */
class CompositeCache extends Cache
{
    public function default($property) {
        switch ($property) {
            case 'store_': return Store::new($this->unset('store'));
            case 'tag_store_': return Store::new($this->unset('tag_store'));
        }
        return parent::default($property);
    }

    public function get($key) {
        global $m_profiler; /* @var Profiler $m_profiler */

        if ($m_profiler) $m_profiler->start($key, 'cache');
        try {
            return $this->store_->get($key);
        }
        finally {
            if ($m_profiler) $m_profiler->stop($key);
        }
    }

    public function put($key, $value, $tags = [], $minutes = null) {
        if ($minutes !== null) {
            $this->store_->put($key, $value, $minutes);
        }
        else {
            $this->store_->forever($key, $value);
        }

        foreach ($tags as $tag) {
            if (($keys = $this->tag_store_->get('tag_' . $tag)) !== null) {
                $keys[$key] = true;
            }
            else {
                $keys = [$key => true];
            }

            $this->tag_store_->forever('tag_' . $tag, $keys);
        }

        if (!empty($tags)) {
            if (($cachedTags = $this->tag_store_->get('key_' . $key)) !== null) {
                $cachedTags = array_unique(array_merge($cachedTags, $tags));
            }
            else {
                $cachedTags = $tags;
            }

            $this->tag_store_->forever('key_' . $key, $cachedTags);
        }
    }

    public function forget($key) {
        $this->store_->forget($key);

        if (($tags = $this->tag_store_->get('key_' . $key)) !== null) {
            foreach ($tags as $tag) {
                if (($keys = $this->tag_store_->get('tag_' . $tag)) !== null) {
                    unset($keys[$key]);
                }

                if (!empty($keys)) {
                    $this->tag_store_->forever('tag_' . $tag, $keys);
                }
                else {
                    $this->tag_store_->forget('tag_' . $tag);
                }
            }

            $this->tag_store_->forget('key_' . $key);
        }
    }

    public function flushTag($tag) {
        if (($keys = $this->tag_store_->get('tag_' . $tag)) !== null) {
            foreach (array_keys($keys) as $key) {
                $this->forget($key);
            }

            $this->tag_store_->forget('tag_' . $tag);
        }
    }

    public function flush() {
        $this->store_->flush();
        $this->tag_store_->flush();
    }
}