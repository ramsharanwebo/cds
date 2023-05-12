<?php
namespace App\Interfaces;

use App\Models\Location;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\UserLocation;
interface LocationInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Location;

    public function create(Request $data): ?Location;

    public function update(Request $request, int $id): ?Location;

    public function delete(int $id): ?bool;

    public function assignLocationToUser(Request $request): ?UserLocation;

    public function removeLocationToUser(Request $request): ?bool;
}