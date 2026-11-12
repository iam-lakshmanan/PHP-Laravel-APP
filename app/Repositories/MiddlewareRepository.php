<?php

namespace App\Repositories;

use App\Constants\AppConstants;
use App\Models\Node;
use App\Models\UrlAlias;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class MiddlewareRepository
{
    public function checkRouteNid($nid)
    {
        try {

            $node = Node::where('nid', '=', $nid)
                ->select('nid')
                ->first();

            return $node;
        } catch (\Exception $e) {
            Log::error('Error in checkRouteNid', [
                'function' => 'checkRouteNid',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

        }
    }
    public function checkRouteSlug($alias)
    {
        try {
            $node = UrlAlias::where('alias', $alias)->first();

            if (!$node) {
                return null;
            }
            $getNodedata = str_replace('node/', '', $node->source);
            $getNodeDetails = Node::where('nid', $getNodedata)->select('nid', 'type')->first();
            if (!$getNodeDetails) {
                return null;
            }
            return $getNodeDetails;

        } catch (\Exception $e) {
            Log::error('Error in checkRouteSlug', [
                'function' => 'checkRouteSlug',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
    public function checkPublicNodeId($getNodeId, $entityType)
    {
        try {
            $validEntityTypes = [
                AppConstants::TEAM,
                AppConstants::NODETYPE_EVENTS,
                AppConstants::COURSES,
                AppConstants::NODETYPE_ARTICLE,
                AppConstants::PAGE,
                AppConstants::UPCOMING_SESSIONS,
                AppConstants::COURSE
            ];
            if (in_array($entityType, $validEntityTypes)) {
                if ($entityType == AppConstants::TEAM) {
                    $getPublicNodeId = Node::leftJoin('field_data_field_members_visible', 'field_data_field_members_visible.entity_id', '=', 'node.nid')
                        ->where('node.type', $entityType)
                        ->where('field_data_field_members_visible.field_members_visible_value', AppConstants::FIELD_RESALES_YES)
                        ->where('node.status', AppConstants::NODE_STATUS)
                        ->where('node.nid', $getNodeId)->select('node.nid')->first();
                } elseif ($entityType == AppConstants::NODETYPE_EVENTS) {
                    $getPublicNodeId = Node::leftJoin('field_data_field_event_type', 'field_data_field_event_type.entity_id', '=', 'node.nid')
                        ->where('node.type', $entityType)
                        ->where('field_data_field_event_type.field_event_type_value', AppConstants::FIELD_EVENT_TYPE_VALUE)
                        ->where('node.status', AppConstants::NODE_STATUS)
                        ->where('node.nid', $getNodeId)->select('node.nid')->first();
                } else {
                    $getPublicNodeId = Node::where('node.status', AppConstants::NODE_STATUS)
                        ->where('node.nid', $getNodeId)
                        ->select('node.nid')
                        ->first();
                }
                return $getPublicNodeId;
            } else {
                throw new NotFoundHttpException('Page not found');
            }

        } catch (\Exception $e) {
            Log::error('Error in checkPublicNodeId', [
                'function' => 'checkPublicNodeId',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

        }
    }
}
