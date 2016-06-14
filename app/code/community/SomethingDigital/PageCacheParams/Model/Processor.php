<?php

class SomethingDigital_PageCacheParams_Model_Processor
{
    protected $blacklist = null;

    public function extractContent($content)
    {
        if ($content) {
            // It's too late, FPC (or some other handler) has already run.
            // Just bail out.
            return $content;
        }

        // These operate directly on server / request vars because FPC does too.
        // Magento hasn't initialized yet so we can't even use the request object.
        $this->processArray($_GET);
        $this->processArray($_REQUEST);
        if (isset($_SERVER['QUERY_STRING'])) {
            $this->processQueryString($_SERVER['QUERY_STRING']);
        }

        // Some of these may be unnecessary, but let's make sure it's consistent.
        // These are all the places Magento may read the URI from.
        $uriKeys = array(
            'REQUEST_URI',
            'UNENCODED_URL',
            'ORIG_PATH_INFO',
            'PATH_INFO',
            'HTTP_X_ORIGINAL_URL',
            'HTTP_X_REWRITE_URL',
        );
        foreach ($uriKeys as $key) {
            if (isset($_SERVER[$key])) {
                $this->processUri($_SERVER[$key]);
            }
        }

        // We didn't generate any content - pass along.
        return false;
    }

    protected function getBlacklist()
    {
        if ($this->blacklist !== null) {
            return $this->blacklist;
        }

        $this->blacklist = array();
        $params = Mage::getConfig()->getNode('global/sd_pagecacheparams/blacklist');
        foreach ($params->children() as $name => $node) {
            // Allow default parameters to be disabled.
            if (!isset($node['ignore'])) {
                $this->blacklist[] = $name;
            }
        }

        return $this->blacklist;
    }

    protected function processUri(&$uri)
    {
        $pos = strpos($uri, '?');
        if ($pos === false) {
            // No query string, so nothing to do.
            return;
        }

        $query = substr($uri, $pos + 1);
        if ($this->processQueryString($query)) {
            if ($query === '' || $query === null) {
                // No query anymore, let's remove the ? too.
                $uri = substr($uri, 0, $pos);
            } else {
                // Changed to a shorter query, reinsert after '?'.
                $uri = substr($uri, 0, $pos + 1) . $query;
            }
        }
    }

    protected function processQueryString(&$query)
    {
        $blacklist = $this->getBlacklist();

        $changed = false;
        // This results in an array of ['param1=value1', 'param2=value2'].
        $params = explode('&', $query);
        foreach ($params as $i => $pair) {
            foreach ($blacklist as $name) {
                // Skip anything that doesn't have a blacklisted key.
                // Note: we also strip even if it has no value.
                if (strpos($pair, $name . '=') !== 0 && $pair !== $name) {
                    continue;
                }

                // Nuke the parameter.
                unset($params[$i]);
                $changed = true;
            }
        }

        // Changed, recombine.
        if ($changed) {
            $query = implode('&', $query);
        }
        return $changed;
    }

    protected function processArray(&$array)
    {
        $blacklist = $this->getBlacklist();

        // This one's easy.
        foreach ($blacklist as $name) {
            unset($array[$name]);
        }
    }
}