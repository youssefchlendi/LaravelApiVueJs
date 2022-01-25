<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Item;
use App\Models\Tags;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    public function store(Request $request)
    {
        $activity_name = $request->input('activity_name');
        $activity_priority = $request->input('priority');
        $data = array(
            'activity_name' => $activity_name,
            'priority' => $activity_priority
        );
        $activity = Activity::create($data);
        if ($activity) {
            return response()->json([
                'data' => [
                    'type' => 'activities',
                    'message' => 'Success',
                    'id' => $activity->id,
                    'attributes' => $activity
                ]
            ], 201);
        } else {
            return response()->json([
                'type' => 'activities',
                'message' => 'Fail'
            ], 400);
        }
    }

    public function storeList(Request $request, $activity_id)
    {
        $item_name = $request->input('item_name');
        $dead_line = $request->input('dead_line');
        $item = Item::create([
            'item_name' => $item_name,
            'activity_id' => $activity_id,
            'status' => 0,
            'dead_line' => $dead_line
        ]);
        if ($item) {
            return response()->json([
                'data' => [
                    'type' => 'activity items',
                    'message' => 'Success',
                    'id' => $item->id,
                    'attributes' => $item
                ]
            ], 201);
        } else {
            return response()->json([
                'type' => 'activity_items',
                'message' => 'Fail'
            ], 400);
        }
    }

    public function storeTag(Request $request)
    {
        $tag_name = $request->input('tag_name');
        $tag = Tags::create([
            'tag_name' => $tag_name,
        ]);
        if ($tag){
            return response()->json([
                'data' => [
                    'type' => 'tag',
                    'message' => 'Success',
                    'id' => $tag->id,
                    'attributes' => $tag
                ]
            ], 201);
        }else{
            return response()->json([
                'type' => 'tag',
                'message' => 'Fail'
            ], 400);
        }
    }

    public function show(Request $request)
    {
        $search = $request->input('search')?$request->input('search'):'';
        $order = $request->input('order')?$request->input('order'):'created_at';
        if($search == ''||$request->input('search') == null){
            $activities = Activity::with('items')
                ->orderBy($order,'desc')
                ->paginate('5');
        }else{
            $activities = Activity::with(['items'=>function($i) use ($request) {
                $i->where('item_name','like','%'.$request->input('search').'%');
            }])
                ->orderBy($order,'desc')
                ->where('activity_name','like','%'.$search.'%')
                ->paginate('5');
        }


        return response()->json([
            'data' => $activities
        ], 200);
    }

    public function showTags()
    {
            $tags = Tags::all();

        return response()->json([
            'data' => $tags
        ], 200);
    }


    public function activityUpdate(Request $request, $activity_id)
    {
        $activity = Activity::find($activity_id);
        if ($activity) {
            $activity->activity_name = $request->input('activity_name');
            $activity->priority = $request->input('priority');
            $activity->save();
            return response()->json([
                'data' => [
                    'type' => 'activities',
                    'message' => 'Update Success',
                    'id' => $activity->id,
                    'attributes' => $activity
                ]
            ], 201);
        } else {
            return response()->json([
                'type' => 'activities',
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function itemUpdate(Request $request, $activity_id, $item_id)
    {
        $item = Item::where('activity_id', $activity_id)->where('id', $item_id)->first();
        if ($item) {
            $item->item_name = $request->input('item_name');
            $item->dead_line = $request->input('dead_line');
            $item->status = $request->input('status');
            $item->save();
            return response()->json([
                'data' => [
                    'type' => 'items',
                    'message' => 'Update Success'
                ]
            ], 201);
        } else {
            return response()->json([
                'type' => 'items',
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function getActivityById($activity_id)
    {
        $activity = Activity::with('items')->find($activity_id);
        if ($activity) {
            return response()->json([
                'data' => [
                    'type' => 'activities',
                    'message' => 'Success',
                    'attributes' => $activity
                ]
            ], 200);
        } else {
            return response()->json([
                'type' => 'activities',
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function activityDestroy($activity_id)
    {
        $activity = Activity::find($activity_id);
        if ($activity) {
            $activity->delete();
            $activity->items()->delete();

        } else {
            return response()->json([
                'type' => 'activities',
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json([
            'type' => 'activities',
            'message' => 'Activity deleted'
        ], 204);
    }

    public function activityItemDestroy($activity_id, $item_id)
    {
        $item = Item::where('activity_id', $activity_id)->where('id', $item_id)->first();
        if ($item) {
            $item->delete();
            return response()->json([], 204);
        } else {
            return response()->json([
                'type' => 'items',
                'message' => 'Not Found'
            ], 404);
        }
    }
}
