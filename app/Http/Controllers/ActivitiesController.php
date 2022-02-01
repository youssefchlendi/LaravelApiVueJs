<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Item;
use App\Models\Tags;
use Illuminate\Http\Request;
use DB;

class ActivitiesController extends Controller
{
    public function __construct(){
        $this->middleware('apiAuth');
    }
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
        $filter = !empty($request->input('filter'))?[$request->input('filter')]:'';
        if($search == ''||$request->input('search') == null){
            if (!empty($filter)){

                $activities = Activity::with('items')
                    ->with('tags')
                    ->whereHas('tags' ,function($i) use ($filter) {
                        $i->whereIn('tags.id',$filter);
                    })
                    ->orderBy($order,'desc')
                    ->paginate('5');
            }else{
                $activities = Activity::with('items')
                    ->with('tags')
                    ->orderBy($order,'desc')
                    ->paginate('5');
            }
        }else{
            if (empty($filter)){
            $activities = Activity::with('items')
                ->whereHas('items' ,function($i) use ($search) {
                    $i->where('item_name','like','%'.$search.'%');
                })
                ->with('tags')
                ->orderBy($order,'desc')
                ->orWhere('activity_name','like','%'.$search.'%')
                ->paginate('5');
            }else{
                $activities = Activity::with('items')
                    ->whereHas('items' ,function($i) use ($search) {
                        $i->where('item_name','like','%'.$search.'%');
                    })
                    ->with('tags')
                    ->andWhereHas('tags' ,function($i) use ($filter) {
                        $i->whereIn('tags.id',$filter);
                    })
                    ->orderBy($order,'desc')
                    ->orWhere('activity_name','like','%'.$search.'%')
                    ->paginate('5');
            }
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

    public function itemUpdateAll(Request $request)
    {
        $item = Item::query()->update(['status' => $request->input('status')]);
        if ($item) {
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

    public function activityDestroyAll(){
        $activity = Activity::whereNotNull('id')->delete();
        return response()->json([
            'type' => 'activities',
            'message' => 'Activity deleted'
        ], 204);
    }

    public function tagDestroy($tag_id)
    {
        $tag = Tags::find($tag_id);
        if ($tag) {
            $tag->delete();
            return response()->json([
                'type' => 'Tag',
                'message' => 'khralel'
            ], 204);
        } else {
            return response()->json([
                'type' => 'Tag',
                'message' => 'Not Found'
            ], 404);
        }

    }

    public function linkTag($activity_id,$tag_id)
    {

        $activity = Activity::find($activity_id);

        if ($activity->tags()->where('tag_id','=',$tag_id)->count()!=0){
            $activity->tags()->detach($tag_id);
        }else {

            $activity->tags()->attach($tag_id);
        }
        return response()->json([
            'type' => 'Tag',
            'message' => 'Tag attached'
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
