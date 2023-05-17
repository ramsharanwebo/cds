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

            event(new GenericEvent(
                'Role created successfully', 
                Route::current()->uri(),
                Route::current()->methods(),
                $request
            ));
 
            return ResponseHelper::successHandler($role, "Role created successfully", RESPONSE::HTTP_OK);
        }
        catch(BadMethodCallException $badMethodCallException){
            return ResponseHelper::errorHandling($badMethodCallException->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'name' => 'sometimes|required|max:199',
                'description' => 'nullable|max:199',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $role = $this->roleRepository->update($request, $id);
            
            return ResponseHelper::successHandler($role, "Role updated successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function delete(int $id): JsonResponse
    {
        try{
            $this->roleRepository->delete($id);
            
            return ResponseHelper::successHandler("Role deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
