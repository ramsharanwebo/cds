<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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

            return ResponseHelper::successHandler($users, "Users are fetched successfully", Response::HTTP_OK);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($data = [], $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->cDSUserRepository->getById($id);
            return ResponseHelper::successHandler($user, "User fetched successfully", RESPONSE::HTTP_OK);
        } 
        catch (ModelNotFoundException $modelNotFoundException) {
            return ResponseHelper::errorHandling("Resource not found!", Response::HTTP_NOT_FOUND);
        } 
        catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }
 
            $role = $this->cDSUserRepository->create($request);
            
            return ResponseHelper::successHandler($role, "User created successfully", RESPONSE::HTTP_OK);
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
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->cDSUserRepository->update($request, $id);

            return ResponseHelper::successHandler($permission, "User updated successfully", RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try{
            $this->cDSUserRepository->delete($id);
            
            return ResponseHelper::successHandler("User deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
