<?php

namespace App\Interfaces;

interface MiddlewareServiceInterface
{

     public function checkroutenid($request);
     public function checkRouteSlug($request);
     public function checkPublicNodeId($getNodeId, $entityType);

}

