<?php
namespace App\Interfaces;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Http\Request;

interface CustomerInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Customer;

    public function create(Request $request): Customer;

    public function update(Request $request, int $id): Customer;

    public function delete(int $id): bool;

}