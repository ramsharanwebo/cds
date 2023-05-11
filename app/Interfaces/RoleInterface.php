<?php
namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Role;
use Illuminate\Http\Request;

interface RoleInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Role;

    public function create(array $data): Role;

    public function update(Request $request, int $id): Role;

    public function delete(int $id): bool;

}