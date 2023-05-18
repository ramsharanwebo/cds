<?php

namespace App\Http\Controllers;

use App\Events\GenericEvent;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Route;
use App\Repositories\RoleRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Validator;
use Validator;

class RoleController extends Controller
{
    private $roleRepository;
    
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

        return ResponseHelper::successHandler($roles, "Roles are fetched successfully", 200);
    }

    public function show(int $id): JsonResponse
    {
        try{
            $role = $this->roleRepository->getById($id);
            return ResponseHelper::successHandler($role, "Role fetched successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("No resource found!", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){ 
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function create(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'name'=> 'required|string|max:199',
                'description'=> 'nullable|max:199'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data['name'] = $request->name;
            $data['description'] = $request->description;
            $role = $this->roleRepository->create($data);
            $message = 'Role created successfully';

            $res = ResponseHelper::successHandler($role, $message, RESPONSE::HTTP_OK);
        }
        catch(BadMethodCallException $badMethodCallException){
            $message = $badMethodCallException->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $ex){
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        event(new GenericEvent(
            $message, 
            Route::current()->uri(),
            Route::current()->methods(),
            $request
        ));

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
            $message = "Role updated successfully";

            $res = ResponseHelper::successHandler($role, $message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $message = "Resource not found";
            $res = ResponseHelper::errorHandling($message, Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        event(new GenericEvent(
            $message, 
            Route::current()->uri(),
            Route::current()->methods(),
            $request
        ));

        return $res;
        
    }

    public function delete(int $id): JsonResponse
    {
        $request = new Request;
        $request->query->add(['id' => $id]);

        try{
            $this->roleRepository->delete($id);
            $message = "Role deleted successfully";

            $res = ResponseHelper::successHandler($data=[], $message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $message = "Resource not found";
            $res = ResponseHelper::errorHandling($message, Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        event(new GenericEvent(
            $message, 
            Route::current()->uri(),
            Route::current()->methods(),
            $request
        ));

        return $res;
    }
}
