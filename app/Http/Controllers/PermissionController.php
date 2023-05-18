<?php

namespace App\Http\Controllers;

use App\Events\GenericEvent;
use App\Helpers\ResponseHelper;
use App\Repositories\PermissionRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Validator;

class PermissionController extends Controller
{
    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page ?? 1;
            $key = $request->key ?? '';
            $sort_by = $request->sortby ?? "id";
            $order = $request->order ?? "ASC";
            $per_page = $request->per_page ?? 2;

            $permissions = $this->permissionRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);

            return ResponseHelper::successHandler($permissions, "Permissions are fetched successfully", RESPONSE::HTTP_OK);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($data = [], $ex->getMessage(), RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $request = new Request;
            $request->query->add(['id' => $id]);
            
            $permission = $this->permissionRepository->getById($id);

            $message = "Permission fetched successfully";
            $res = ResponseHelper::successHandler($permission, $message, RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException $ex) {
            $message = "No resource found";
            $res = ResponseHelper::errorHandling($message, Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
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

    public function create(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:199',
                'description' => 'nullable|max:199'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data['name'] = $request->name;
            $data['description'] = $request->description;
            $permission = $this->permissionRepository->create($data);

            return ResponseHelper::successHandler($permission, "Permission created successfully", RESPONSE::HTTP_OK);
        } catch (BadMethodCallException $badMethodCallException) {
            return ResponseHelper::errorHandling($badMethodCallException->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|max:199',
                'description' => 'nullable|max:199',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->permissionRepository->update($request, $id);

            return ResponseHelper::successHandler($permission, "Permission updated successfully", RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->permissionRepository->delete($id);

            return ResponseHelper::successHandler($data = [], "Permission deleted successfully", RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
