<?php

namespace Diplomacy\Views;

use Diplomacy\Services\Request;

trait CanAccessRequest
{
    protected Request $request;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        if (isset($this->request)) return $this->request;

        try {
            global $app;
            $this->request = $app->make('request');
        } catch (\Exception $e) {
            $this->request = new Request();
        }
        return $this->request;
    }
}
