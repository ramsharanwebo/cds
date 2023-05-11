<?php

namespace App\Repositories;

use App\Interfaces\CustomerInterface;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        $customers = Customer::where(function ($query) use ($key) {
            $query->where('name', 'LIKE', '%' . $key . '%')
            ->orWhere('email', 'LIKE', '%'.$key.'%')
            ->orWhere('abn_number', 'LIKE', '%'.$key.'%')
            ->orWhere('business_name', 'LIKE', '%'.$key.'%')
            ->orWhere('contact_number', 'LIKE', '%'.$key.'%')
            ->orWhere('phone', 'LIKE', '%'.$key.'%')
            ->orWhere('transaction_summary_perference', 'LIKE', '%'.$key.'%');
        })
        ->orderBy($sort_by, $order)
        ->paginate($perPage);

        return $customers;
    }
    
    public function getById(int $id): ?Customer
    {
        return Customer::findOrFail($id);
    }

    public function create(Request $request): Customer
    {
        $data['business_model_type'] = $request->business_model_type;
        $data['abn_number'] = $request->abn_number;
        $data['business_name'] = $request->business_name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['name'] = $request->name;
        $data['contact_number'] = $request->contact_number;
        $data['suburb'] = $request->suburb;
        $data['state'] = $request->state;
        $data['postal_code'] = $request->postal_code;
        $data['transaction_summary_perference'] = $request->transaction_summary_perference;
        $data['created_by'] = $request->created_by;
        
        return Customer::create($data);
    }

    public function update(Request $request, int $id): Customer
    {
        $customer = $this->getById($id);
        
        $customer->update($request->all());

        return $customer;
    }

    public function delete(int $id): bool
    {
        $user = $this->getById($id);

        if($user->delete()){
            return true;
        }else{
            return false;
        }
    }
}
