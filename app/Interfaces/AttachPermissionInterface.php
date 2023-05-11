<?php
namespace App\Interfaces;

use App\Models\PermissionRole;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


interface AttachPermissionInterface
{
    // Attach/Detach permission to role
    public function attachPermissionToRole(Request $request): Collection;

    public function detachPermissionToRole(Request $request): Collection;


    // Attach/Detach permission to user
    public function attachPermissionToUser(Request $request): Collection;
    public function detachPermissionToUser(Request $request): Collection;

}