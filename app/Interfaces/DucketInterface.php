<?php
namespace App\Interfaces;

use App\Models\Ducket;
use App\Models\Location;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\UserLocation;

interface DucketInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Ducket;

    public function create(Request $data): ?Ducket;

    public function update(Request $request, int $id): ?Ducket;

    public function delete(int $id): ?bool;
}