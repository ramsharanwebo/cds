<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Jobs\LogActivity;
use App\Repositories\RoleRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class RoleController extends Controller
{
    private $roleRepository;
    private $message;
    
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $page_num = $request->page??1;
        $key = $request->key??'';
        $sort_by = $request->sortby??"id";
        $order = $request->order??"ASC";
        $per_page = $request->per_page??2;

        $roles = $this->roleRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);
        $this->message = "Roles are fetched successfully";
        
        $messageBody = ['payload'=> $roles, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');
        return ResponseHelper::successHandler($roles, $this->message, 200);
    }

    public function show(int $id): JsonResponse
    {
        try{
            $role = $this->roleRepository->getById($id);
            $this->message = "Role fetched successfully";
            $res = ResponseHelper::successHandler($role, $this->message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $this->message = "No resource found!";
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){ 
            $this->message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $id, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');
        return $res;
    }

    public function create(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'name'=> 'required|string|max:199',
                'description'=> 'nullable|max:199'
            ]);

            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data['name'] = $request->name;
            $data['description'] = $request->description;
            $role = $this->roleRepository->create($data);
            $this->message = 'Role created successfully';

            $res = ResponseHelper::successHandler($role, $this->message, RESPONSE::HTTP_OK);
        }
        catch(BadMethodCallException $badMethodCallException){
            $this->message = $badMethodCallException->getMessage();
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $ex){
            $this->message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $role, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
        
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'name' => 'sometimes|required|string|max:199',
                'description' => 'nullable|string|max:199',
            ]);

            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $role = $this->roleRepository->update($request, $id);
            $this->message = "Role updated successfully";

            $res = ResponseHelper::successHandler($role, $this->message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $this->message = "Resource not found";
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $this->message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $role, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
        
    }

    public function delete(int $id): JsonResponse
    {
        try{
            $this->roleRepository->delete($id);
            $this->message = "Role deleted successfully";

            $res = ResponseHelper::successHandler($this->message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $this->message = "Resource not found";
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $this->message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($this->message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> [], 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
        
    }
}
