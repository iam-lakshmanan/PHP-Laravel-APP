<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\MiddlewareService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Crypt;
class CheckRouteNid
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
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $getNodeId = $request->route('nodeId');
            if (is_numeric($getNodeId) && strlen($getNodeId) <= 5) {
                $nodeId = $getNodeId;
            } elseif (strlen($getNodeId) >= 20) {
                $nodeId = Crypt::decryptString($getNodeId);
            } else {
                $nodeId = '';
                throw new NotFoundHttpException('Node ID is invalid or missing.');
            }

            $node = $this->middlewareService->checkRouteNid($nodeId);

            if (!$node) {
                throw new NotFoundHttpException('Page not found');
            }
            

            $request->attributes->set('nodeId', $nodeId);

            return $next($request);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Page not found');
        }
    }
}
