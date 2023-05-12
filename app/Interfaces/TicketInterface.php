<?php
namespace App\Interfaces;

use App\Models\Ducket;
use App\Models\Ticket;
use App\Models\Location;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\UserLocation;

interface TicketInterface
{
    

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?Ticket;

    public function create(Request $data): ?Ticket;

    public function update(Request $request, int $id): ?Ticket;

    public function delete(int $id): ?bool;
}