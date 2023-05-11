<?php

namespace App\Repositories;

use App\Interfaces\AttachPermissionInterface;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttachPermissionRepository implements AttachPermissionInterface
{

    public function attachPermissionToRole(Request $request): Collection
    {
        $role = Role::findOrFail($request->role_id);
        $permissions = $request->permissions;

        foreach($permissions as $key=>$val){
            $alreadyPermitted = DB::table("permission_role")->where(['role_id'=> $role->id, 'permission_id'=> $val])->first();
            
            if(!$alreadyPermitted || $alreadyPermitted ==null){
                DB::table('permission_role')->insert([
                    'role_id'=>$request->role_id,
                    'permission_id'=>$val,
                    'created_at' =>now(),
                    'updated_at' =>now(),
                ]);
            }
            
        }

        return PermissionRole::where('role_id', $role->id)->get();       
    }

    public function detachPermissionToRole(Request $request): Collection
    {
        $role = Role::findOrFail($request->role_id);
        $permissions = $request->permissions;

        foreach($permissions as $key=>$val){
            $alreadyPermitted = DB::table("permission_role")->where(['role_id'=> $role->id, 'permission_id'=> $val])->delete();
        }

        return PermissionRole::where('role_id', $role->id)->get();       
    }

    public function attachPermissionToUser(Request $request): Collection
    {
        $user = User::findOrFail($request->user_id);
        $permissions = $request->permissions;
        
        foreach($permissions as $key=>$val){
            $alreadyPermitted = DB::table("user_permission")->where(['user_id'=> $user->id, 'permission_id'=> $val])->first();
            
            if(!$alreadyPermitted || $alreadyPermitted ==null){
                DB::table('user_permission')->insert([
                    'user_id'=>$request->user_id,
                    'permission_id'=>$val,
                    'created_at' =>now(),
                    'updated_at' =>now(),
                ]);
            }
            
        }

        return UserPermission::where('user_id', $user->id)->get();       
    }

    public function detachPermissionToUser(Request $request): Collection
    {
        $user = User::findOrFail($request->user_id);
        $permissions = $request->permissions;

        foreach($permissions as $key=>$val){
            $alreadyPermitted = DB::table("user_permission")
                ->where(['user_id'=> $user->id, 'permission_id'=> $val])->delete();
        }

        return UserPermission::where('user_id', $user->id)->get();       
    }
}