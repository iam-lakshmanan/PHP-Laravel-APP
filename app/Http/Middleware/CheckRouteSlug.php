<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\AppConstants;
use App\Services\MiddlewareService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckRouteSlug
{
    protected $middlewareService;

    public function __construct(MiddlewareService $middlewareService)
    {
        $this->middlewareService = $middlewareService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        $requestUrl = urldecode($request->path());
        $requestUrlParts = explode('/', $requestUrl);
        $requestUrlPartsData = $requestUrlParts[0];

        if ($requestUrlPartsData == AppConstants::UPCOMING_SESSIONS) {
            if (Str::contains($requestUrl, AppConstants::UPCOMING_SESSIONS . '/')) {
                $segment = AppConstants::UPCOMING_SESSIONS . '/';
                $entityType = AppConstants::UPCOMING_SESSIONS;
                $requestUrl = str_replace($segment, '', $requestUrl);
            } else {
                throw new NotFoundHttpException('Node Slug is invalid or missing.');
            }
        } else {
            $requestUrl = urldecode($request->path());
        }


        // Check URL alias
        $urlAlias = $this->middlewareService->checkRouteSlug($requestUrl);

        if ($urlAlias === null) {
            $urlAlias = $this->middlewareService->checkRouteSlug($urlAlias);
            if (!$urlAlias) {
                throw new NotFoundHttpException('Page not found');
            }
        }
        $getNodeId = $urlAlias->nid;
        $entityType = $urlAlias->type;
        // Validate the node ID
        $urlAlias = $this->middlewareService->checkPublicNodeId($getNodeId, $entityType);
        if (!$urlAlias) {
            throw new NotFoundHttpException('Page not found');
        }
        $request->attributes->set('nodeId', $getNodeId);

        return $next($request);
    }
}
