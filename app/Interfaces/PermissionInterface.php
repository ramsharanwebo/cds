<?php
namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Permission;
use Illuminate\Http\Request;

interface PermissionInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Permission;

    public function create(array $data): Permission;

    public function update(Request $request, int $id): Permission;

    public function delete(int $id): bool;

}