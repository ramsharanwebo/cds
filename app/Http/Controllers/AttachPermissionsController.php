<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Jobs\LogActivity;
use App\Repositories\AttachPermissionRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\Response;


class AttachPermissionsController extends Controller
{
    private $message;
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
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->attachPermissionToRole($request);
            $this->message = "Permissions are attached to Role successfully";
            $res = ResponseHelper::successHandler($permission, $this->message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $permission, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
    }

    public function detachPermissionToRole(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'role_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                $res= ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }
            $this->message = "Permissions are detached to Role successfully";
            $permission = $this->attachPermissionRepository->detachPermissionToRole($request);
            
            $res = ResponseHelper::successHandler($permission, $this->message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling($this->message="Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($this->message=$ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $permission, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');
        return $res;
    }

    
    public function attachPermissionToUser(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'user_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($this->message=$validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->attachPermissionToUser($request);
            
            $res = ResponseHelper::successHandler($permission, $this->message="Permissions are attached to user successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling($this->message="Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($this->message=$ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $permission, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
    }


    public function detachPermissionToUser(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'permissions' => 'required|array',
                'user_id' => 'required|integer',
            ]);
            
            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($this->message = $validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->attachPermissionRepository->detachPermissionToUser($request);
            
            $res = ResponseHelper::successHandler($permission, $this->message = "Permissions are detached to User successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling($this->message = "Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $permission, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
    }
}
