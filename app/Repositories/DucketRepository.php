<?php

namespace App\Repositories;

use App\Interfaces\DucketInterface;
use App\Models\Ducket;
use App\Models\Location;
use App\Models\UserLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DucketRepository implements DucketInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return Ducket::orderBy($sort_by, $order)
        ->where('identity', 'LIKE', '%'.$key.'%')
        ->orWhere('goods', 'LIKE', '%'.$key.'%')
        ->orWhere('notes', 'LIKE', '%'.$key.'%')
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?Ducket
    {
        return Ducket::findOrFail($id);
    }

    public function create(Request $request): Ducket
    {
        $date = Carbon::now();
        $validated['identity'] = $request['identity'];
        $validated['ducket_date'] = $request['ducket_date']??$date->toDateString();
        // $validated['ticket_id'] = $request['ticket_id'];
        $validated['goods'] = $request->goods??"";
        $validated['notes'] = $request['notes']??"";
        $validated['gst'] = $request['gst']??"0.00";
        $validated['levy'] = $request['levy']??"0.00";
        $validated['total_amount'] = $request['total_amount']??"0.00";
        $validated['count'] = $request['count']??1;
        $validated['created_by'] = 1;

        return Ducket::create($validated);
    }

    public function update(Request $request, int $id): Ducket
    {
        $location = $this->getById($id);

        $location->update($request->all());

        return $location;
    }

    public function delete(int $id): bool
    {
        $location = $this->getById($id);

        if($location->delete()){
            return true;
        }else{
            return false;
        }
    }

    public function assignLocationToUser(Request $request): UserLocation
    {
        $locationExist = UserLocation::where('user_id', $request->user_id)->where('location_id', $request->location_id)->first();
        if($locationExist && $locationExist !=null){
            $locationExist->delete();
        }

        UserLocation::create($request->all());

        return UserLocation::where('user_id', $request->user_id)->first();
    }

    public function removeLocationToUser(Request $request): ?bool
    {
        $locationExist = UserLocation::where('user_id', $request->user_id)->where('location_id', $request->location_id)->first();
        if($locationExist && $locationExist !=null){
            $detachLocation = $locationExist->delete();
            return $detachLocation;
        }
        return false;
    }
}
