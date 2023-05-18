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
use App\Events\GenericEvent;
use Illuminate\Support\Facades\Route;

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
        $request = new Request;
        $request->query->add(['id' => $id]);
        try{
            $location = $this->locationRepository->getById($id);
            $message = "Location fetched successfully";
            $res = ResponseHelper::successHandler($location, $message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            $res = ResponseHelper::errorHandling("No resource found!", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){ 
            $res = ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
        try{
            $validator = Validator::make($request->all(), [ 
                'name'=> 'required|string|max:199',
                'email'=> 'nullable|max:199',
                "phone"=> 'nullable|max: 25',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $location = $this->locationRepository->create($request);
            $message = "Location created successfully";

            $res = ResponseHelper::successHandler($location, $message, RESPONSE::HTTP_CREATED);
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
                'name'=> 'sometimes|string|max:199',
                'email'=> 'sometimes|nullable|max:199',
                "phone"=> 'sometimes|nullable|max: 25',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $location = $this->locationRepository->update($request, $id);
            $message = "Location updated successfully";
            $res = ResponseHelper::successHandler($location, $message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
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
            $this->locationRepository->delete($id);
            $message = "Location deleted successfully";
            $res = ResponseHelper::successHandler($data=[], $message, RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
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

    public function assignLocationToUser(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'user_id'=> 'required|integer',
                'location_id'=> 'required|integer',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $locationAttached = $this->locationRepository->assignLocationToUser($request);
            $message = "Location Attached to User Successfully";
            $res = ResponseHelper::successHandler($locationAttached, "Location Attached to User Successfully", RESPONSE::HTTP_CREATED);
        }
        catch(BadMethodCallException $badMethodCallException){
            $message = $badMethodCallException->getMessage();
            $res = ResponseHelper::errorHandling($badMethodCallException->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $ex){
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        event(new GenericEvent(
            $message, 
            Route::current()->uri(),
            Route::current()->methods(),
            $request
        ));
        return $res;
    }


    public function removeLocationToUser(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [ 
                'user_id'=> 'required|integer',
                'location_id'=> 'required|integer',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                $res = ResponseHelper::errorHandling($message, RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $locationRemoved = $this->locationRepository->removeLocationToUser($request);
            $message = "Location removed to User Successfully";

            $res = ResponseHelper::successHandler($locationRemoved, $message, RESPONSE::HTTP_OK);
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
}
