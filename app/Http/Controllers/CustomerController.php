<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\CustomerRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Validator;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page ?? 1;
            $key = $request->key ?? '';
            $sort_by = $request->sortby ?? "id";
            $order = $request->order ?? "ASC";
            $per_page = $request->per_page ?? 2;
            
            $customers = $this->customerRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);
            return ResponseHelper::successHandler($customers, "Customers are fetched successfully", RESPONSE::HTTP_OK);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($data = [], $ex->getMessage(), RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $permission = $this->customerRepository->getById($id);

            return ResponseHelper::successHandler($permission, "Customer fetched successfully", RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return ResponseHelper::errorHandling("No resource found!", Response::HTTP_NOT_FOUND);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                "business_model_type" => "required|in:business,individual",
                "abn_number" => "required|max: 25",
                "abn_later" => "sometimes|boolean",
                "business_name" => "required|string",
                "phone" => "required",
                "name" => "sometimes|string",
                "contact_number" => "sometimes|string",
                "suburb" => "sometimes|string",
                "state" => "sometimes|string",
                "postal_code" => "sometimes|string",
                "email" => "sometimes|email",
                "transaction_summary_perference" => "sometimes|string",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $customer = $this->customerRepository->create($request);
            
            return ResponseHelper::successHandler($customer, "Customer created successfully", RESPONSE::HTTP_OK);
        } catch (BadMethodCallException $badMethodCallException) {
            return ResponseHelper::errorHandling($badMethodCallException->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $ex) {
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                "business_model_type" => "sometimes|in:business,individual",
                "abn_number" => "sometimes|max: 25",
                "abn_later" => "sometimes|boolean",
                "business_name" => "sometimes|string",
                "phone" => "sometimes",
                "name" => "sometimes|string",
                "contact_number" => "sometimes|string",
                "suburb" => "sometimes|string",
                "state" => "sometimes|string",
                "postal_code" => "sometimes|string",
                "email" => "sometimes|email",
                "transaction_summary_perference" => "sometimes|string",
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $customer = $this->customerRepository->update($request, $id);
            
            return ResponseHelper::successHandler($customer, "Customer updated successfully", RESPONSE::HTTP_OK);
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
            $this->customerRepository->delete($id);
            
            return ResponseHelper::successHandler($data=[], "Customer deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
