<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\LocationRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class LocationController extends Controller
{
    private $locationRepository;
    
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page??1;
            $key = $request->key??'';
            $sort_by = $request->sortby??"id";
            $order = $request->order??"ASC";
            $per_page = $request->per_page??2;
            
            $locations = $this->locationRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);

            return ResponseHelper::successHandler($locations, "Locations are fetched successfully", RESPONSE::HTTP_OK);
            } catch (Exception $ex) {
                return ResponseHelper::errorHandling($data=[], $ex->getMessage(), RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    public function show(int $id): JsonResponse
    {
        try{
            $location = $this->locationRepository->getById($id);
            return ResponseHelper::successHandler($location, "Location fetched successfully", RESPONSE::HTTP_OK);
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
                'email'=> 'nullable|max:199',
                "phone"=> 'nullable|max: 25',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $permission = $this->locationRepository->create($request);
            
            return ResponseHelper::successHandler($permission, "Permission created successfully", RESPONSE::HTTP_OK);
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
                'name'=> 'sometimes|string|max:199',
                'email'=> 'sometimes|nullable|max:199',
                "phone"=> 'sometimes|nullable|max: 25',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $location = $this->locationRepository->update($request, $id);
            
            return ResponseHelper::successHandler($location, "Location updated successfully", RESPONSE::HTTP_OK);
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
            $this->locationRepository->delete($id);
            
            return ResponseHelper::successHandler($data=[], "Location deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
