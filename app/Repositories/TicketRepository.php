<?php

namespace App\Repositories;

use App\Interfaces\DucketInterface;
use App\Interfaces\TicketInterface;
use App\Models\Ducket;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\UserLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository implements TicketInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return Ticket::orderBy($sort_by, $order)
        ->where('ticket_number', 'LIKE', '%'.$key.'%')
        ->orWhere('reference', 'LIKE', '%'.$key.'%')
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?Ticket
    {
        return Ticket::findOrFail($id);
    }

    public function create(Request $request): Ticket
    {
        $date = Carbon::now();
        $validated['ticket_number'] = fake()->unique()->numberBetween($min = 10000, $max = 500000);
        $validated['location_id'] = $request->location_id;
        $validated['customer_id'] = $request['customer_id'];
        $validated['ticket_date'] = $request->ticket_date??$date->toDate();
        $validated['reference'] = $request->reference??"";
        $validated['amount'] = $request['amount']??"0.00";
        $validated['container_qty'] = $request['container_qty']??1;
        $validated['status'] = $request['status']??1;
        $validated['created_by'] = 1;

        return Ticket::create($validated);
    }

    public function update(Request $request, int $id): Ticket
    {
        $location = $this->getById($id);

        $location->update($request->all());

        return $location;
    }

    public function delete(int $id): bool
    {
        $location = $this->getById($id);

        if($location->delete()){
            return true;
        }else{
            return false;
        }
    }
}
