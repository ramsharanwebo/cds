<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\DucketRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class DucketController extends Controller
{
    private $ducketRepository;

    public function __construct(DucketRepository $ducketRepository)
    {
        $this->ducketRepository = $ducketRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page??1;
            $key = $request->key??'';
            $sort_by = $request->sortby??"id";
            $order = $request->order??"ASC";
            $per_page = $request->per_page??2;
            
            $duckets = $this->ducketRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);

            return ResponseHelper::successHandler($duckets, "Duckets are fetched successfully", RESPONSE::HTTP_OK);
            } catch (Exception $ex) {
                return ResponseHelper::errorHandling($data=[], $ex->getMessage(), RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    public function show(int $id): JsonResponse
    {
        try{
            $location = $this->ducketRepository->getById($id);
            return ResponseHelper::successHandler($location, "Ducket fetched successfully", RESPONSE::HTTP_OK);
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
                "ducket_date"=> "required|date",
                "goods"=> "nullable|max: 199",
                "notes"=> "nullable|max:199",
                "gst"=> "nullable|max:15",
                "levy"=> "nullable|max:15",
                "total_amount"=> "nullable|max:199",
                "count"=> "nullable|integer",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $location = $this->ducketRepository->create($request);
            
            return ResponseHelper::successHandler($location, "Ducket created successfully", RESPONSE::HTTP_CREATED);
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
                "ducket_date"=> "sometimes|date",
                "goods"=> "nullable|max: 199",
                "notes"=> "nullable|max:199",
                "gst"=> "nullable|max:15",
                "levy"=> "nullable|max:15",
                "total_amount"=> "nullable|max:199",
                "count"=> "nullable|integer",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $ducket = $this->ducketRepository->update($request, $id);
            
            return ResponseHelper::successHandler($ducket, "Ducket updated successfully", RESPONSE::HTTP_OK);
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
            $this->ducketRepository->delete($id);
            
            return ResponseHelper::successHandler($data=[], "Ducket deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
