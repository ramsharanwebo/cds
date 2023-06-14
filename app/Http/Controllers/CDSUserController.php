<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Jobs\LogActivity;
use App\Repositories\CDSUserRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Validator;
use App\Rules\UniqueEmailIgnoreCurrentUser;

class CDSUserController extends Controller
{
    private $cDSUserRepository;
    private $message;

    public function __construct(CDSUserRepository $cDSUserRepository)
    {
        $this->cDSUserRepository = $cDSUserRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page ?? 1;
            $key = $request->key ?? '';
            $sort_by = $request->sortby ?? "id";
            $order = $request->order ?? "ASC";
            $per_page = $request->per_page ?? 2;

            $users = $this->cDSUserRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);

            $res = ResponseHelper::successHandler($users, $this->message="Users are fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            $res = ResponseHelper::errorHandling($data = [], $this->message=$ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $users, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');
        return $res;
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->cDSUserRepository->getById($id);
            $res = ResponseHelper::successHandler($user, $this->message="User fetched successfully", RESPONSE::HTTP_OK);
        } 
        catch (ModelNotFoundException) {
            $res = ResponseHelper::errorHandling($this->message="Resource not found!", Response::HTTP_NOT_FOUND);
        } 
        catch (Exception $ex) {
            $res = ResponseHelper::errorHandling($this->message=$ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $user, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');
        return $res;
    }

    public function create(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'first_name'=> 'required|string|max:50',
                'middle_name'=> 'nullable|string|max:50',
                'last_name'=> 'required|string|max:50',
                'email'=> 'required|email|unique:users',
                'password' => 'required|max:99|min:6',
                'role_id'=> 'required|integer'
            ]);

            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($this->message=$validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }
 
            $role = $this->cDSUserRepository->create($request);
            
            $res = ResponseHelper::successHandler($role, $this->message="User created successfully", RESPONSE::HTTP_OK);
        }
        catch(BadMethodCallException $badMethodCallException){
            $res = ResponseHelper::errorHandling($this->message = $badMethodCallException->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $role, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
        
    }

    public function update(Request $request, int $id): JsonResponse
    {

        try {
            $user = $this->cDSUserRepository->getById($id);

            $validator = Validator::make($request->all(), [ 
                'first_name'=> 'sometimes|required|string|max:50',
                'middle_name'=> 'nullable|string|max:50',
                'last_name'=> 'sometimes|required|string|max:50',
                'password' => 'sometimes|required|max:99|min:6',
                'role_id'=> 'sometimes|required|integer',
                'email' => [
                    'sometimes',
                    'email',
                    new UniqueEmailIgnoreCurrentUser($user->id),
                ],
            ]);

            if ($validator->fails()) {
                $res = ResponseHelper::errorHandling($this->message = $validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = $this->cDSUserRepository->update($request, $id);

            $res = ResponseHelper::successHandler($user, $this->message = "User updated successfully", RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException) {
            $res = ResponseHelper::errorHandling($this->message = "Resource not found", Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> $user, 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
    }

    public function delete(int $id): JsonResponse
    {
        try{
            $this->cDSUserRepository->delete($id);
            
            $res = ResponseHelper::successHandler($this->message = "User deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling($this->message = "Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $messageBody = ['payload'=> [], 'url'=>request()->url(), 'method'=>request()->method(), 'subject'=> $this->message];
        LogActivity::dispatch(json_encode($messageBody))->onQueue('activities');

        return $res;
    }

    public function getUserLogs(int $user_id)
    {
        try {
            $logs = $this->cDSUserRepository->getUserLogs($user_id);
            $res = ResponseHelper::successHandler($logs, $this->message = "User's logs fetched successfully", RESPONSE::HTTP_OK);
        } 
        catch (Exception $ex) {
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $res;
    }

    public function makeArchived(Request $request, int $user_id): JsonResponse
    {
        try {
            $logs = $this->cDSUserRepository->makeArchived($request, $user_id);
            $res = ResponseHelper::successHandler($logs, $this->message = $request->status=="archived"?"Archived successfully":"Removed from archived successfully", RESPONSE::HTTP_OK);
        } 
        catch(ModelNotFoundException){
            $res = ResponseHelper::errorHandling($this->message = "Resouce not found", Response::HTTP_NOT_FOUND);
        }
        catch (Exception $ex) {
            $res = ResponseHelper::errorHandling($this->message = $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $res;
    }
}
