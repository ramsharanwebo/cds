<?php

namespace App\Repositories;

use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PermissionRepository implements PermissionInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return Permission::orderBy($sort_by, $order)
        ->where('name', 'LIKE', '%'.$key.'%')
        ->orWhere('description', 'LIKE', '%'.$key.'%')
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data): Permission
    {
        $validated['name'] = $data['name'];
        $validated['slug'] = Str::slug($data['name']);
        $validated['description'] = $data['description'];

        return Permission::create($validated);
    }

    public function update(Request $request, int $id): Permission
    {
        $permission = $this->getById($id);

        if($request->name != null){
            $request->request->add(['slug' => Str::slug($request->name)]);
        }else{
            $request->request->add(['slug' => Str::slug($permission->name)]);
        }

        $permission->update($request->all());

        return $permission;
    }

    public function delete(int $id): bool
    {
        $permission = $this->getById($id);

        if($permission->delete()){
            return true;
        }else{
            return false;
        }
    }
}
