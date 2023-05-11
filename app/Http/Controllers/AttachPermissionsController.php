<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\AttachPermissionRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\Response;


class AttachPermissionsController extends Controller
{
    private $attachPermissionRepository;

    public function __construct(AttachPermissionRepository $attachPermissionRepository)
    {
        $this->attachPermissionRepository = $attachPermissionRepository;
    }

    public function attachPermissionToRole(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'role_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->attachPermissionToRole($request);
            
            return ResponseHelper::successHandler($permission, "Permissions are attached to Role successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function detachPermissionToRole(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'role_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->detachPermissionToRole($request);
            
            return ResponseHelper::successHandler($permission, "Permissions are detached to Role successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    public function attachPermissionToUser(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'user_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->attachPermissionToUser($request);
            
            return ResponseHelper::successHandler($permission, "Permissions are attached to user successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function detachPermissionToUser(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'user_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->detachPermissionToUser($request);
            
            return ResponseHelper::successHandler($permission, "Permissions are detached to User successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
