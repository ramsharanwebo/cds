<?php

namespace App\Repositories;

use App\Interfaces\LocationInterface;
use App\Models\Location;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationRepository implements LocationInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return Location::orderBy($sort_by, $order)
        ->where('name', 'LIKE', '%'.$key.'%')
        ->orWhere('email', 'LIKE', '%'.$key.'%')
        ->orWhere('phone', 'LIKE', '%'.$key.'%')
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?Location
    {
        return Location::findOrFail($id);
    }

    public function create(Request $request): Location
    {
        $validated['name'] = $request['name'];
        $validated['email'] = $request->email??"";
        $validated['phone'] = $request['phone']??"";
        $validated['created_by'] = $request['created_by']??"";
        $validated['status'] = $request['status']??1;

        return Location::create($validated);
    }

    public function update(Request $request, int $id): Location
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
