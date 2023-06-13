<?php

namespace App\Repositories;

use App\Interfaces\CDSUserInterface;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class CDSUserRepository implements CDSUserInterface
{
    public function getAllPaginated(int $page_num=1, int $perPage=1, string $sort_by, string $order, string $key): LengthAwarePaginator
    {
        return User::with(['role:id,name,description', 'permissions'])->where(function ($query) use ($key) {
            $query->where(DB::raw("CONCAT(first_name, ' ', middle_name, ' ', last_name)"), 'LIKE', '%' . $key . '%')
            ->orWhere('email', 'LIKE', '%'.$key.'%')
            ->orWhere('username', 'LIKE', '%'.$key.'%');
        })
        ->orderBy($sort_by, $order)
        ->paginate($perPage);
    }
    
    public function getById(int $id): ?User
    {
        return User::with('role:id,name,description')->findOrFail($id);
    }

    public function create(Request $request): User
    {
        $validated['first_name'] = $request->first_name;
        $validated['middle_name'] = $request->middle_name;
        $validated['last_name'] = $request->last_name;
        $validated['email'] = $request->email;
        $validated['password'] = Hash::make($request->password);
        $validated['role_id'] = $request->role_id;
        $validated['username'] = $this->usernameGenerator($request->email);

        return User::create($validated);
    }

    public function update(Request $request, int $id): User
    {
        $user = $this->getById($id);

        if($request->password != null){
            $request->request->add(['password' => Hash::make($request->password)]);
        }
        if($request->email != null){
            $request->request->add(['username' => $this->usernameGenerator($request->email)]);
        }
        
        $user->update($request->all());

        return $user;
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

    public function usernameGenerator(string $email)
    {
        if($email !=null){
            $rowEmail = explode("@", $email);
            return $rowEmail[0];
        }
        return null;
    }

    public function getUserLogs(string $id){
        $cds_url = Config::get('app.cds_url');
        $client = new Client(['timeout' => 60]);

        try {
            $response = $client->request('GET', $cds_url.'/users'.'/'.$id.'/logs');

            // $request = $client->get('http://myexample.com');
            $response = $response->getBody();
            // ...
            return $response;
        } catch (Exception $e) {
            // Handle any exceptions that occurred during the request
            // ...
        }
    }
}
