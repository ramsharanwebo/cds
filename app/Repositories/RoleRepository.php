<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleRepository implements RoleInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return Role::orderBy($sort_by, $order)
        ->where('name', 'LIKE', '%'.$key.'%')
        ->orWhere('description', 'LIKE', '%'.$key.'%')
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?Role
    {
        return Role::findOrFail($id);
    }

    public function create(array $data): Role
    {
        $validated['name'] = $data['name'];
        $validated['slug'] = Str::slug($data['name']);
        $validated['description'] = $data['description'];

        return Role::create($validated);
    }

    public function update(Request $request, int $id): Role
    {
        $role = $this->getById($id);

        if($request->name != null){
            $request->request->add(['slug' => Str::slug($request->name)]);
        }else{
            $request->request->add(['slug' => Str::slug($role->name)]);
        }

        $role->update($request->all());

        return $role;
    }

    public function delete(int $id): bool
    {
        $role = $this->getById($id);

        if($role->delete()){
            return true;
        }else{
            return false;
        }
    }
}
