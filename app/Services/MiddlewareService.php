<?php

namespace App\Services;

use App\Repositories\MiddlewareRepository;
use App\Interfaces\MiddlewareServiceInterface;

class MiddlewareService implements MiddlewareServiceInterface
{
    protected $middlewareRepository;


    public function __construct()
    {
        $this->middlewareRepository = new MiddlewareRepository();
    }
    public function checkRouteNid($nid)
    {
        return $this->middlewareRepository->checkRouteNid($nid);
    }
    public function checkRouteSlug($alias)
    {
        return $this->middlewareRepository->checkRouteSlug($alias);
    }
    public function checkPublicNodeId($getNodeId, $entityType)
    {
        return $this->middlewareRepository->checkPublicNodeId($getNodeId, $entityType);
    }
}

