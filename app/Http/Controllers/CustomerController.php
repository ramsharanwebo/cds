<?php

namespace App\Http\Controllers;

use App\Events\GenericEvent;
use App\Helpers\ResponseHelper;
use App\Repositories\CustomerRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
            $request = new Request;
            $request->query->add(['id' => $id]);
        
            $customer = $this->customerRepository->getById($id);
            $message = "Customer fetched successfully";
            $res = ResponseHelper::successHandler($customer, $message, RESPONSE::HTTP_OK);
        } catch (ModelNotFoundException) {
            $message = "Resource not found";
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
                $message = $validator->errors();
                $res = ResponseHelper::errorHandling($message, RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $customer = $this->customerRepository->create($request);

            $message = "Customer created successfully"; 
            $res = ResponseHelper::successHandler($customer, $message, RESPONSE::HTTP_OK);
        } catch (BadMethodCallException $badMethodCallException) {
            $message = $badMethodCallException->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_BAD_REQUEST);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
            // dd($message,
            // Route::current()->uri(),
            // Route::current()->methods(),
            // $request);
            // $request->request->remove("business_model_type");
            // $request->request->remove("abn_number");
            // $request->request->remove("abn_later");
            // $request->request->remove("business_name");
            // $request->request->remove("phone");
            // $request->request->remove("name");
            // $request->request->remove("contact_number");
            // $request->request->remove("suburb");
            // $request->request->remove("state");
            $request->request->remove("postal_code");
            $request->request->remove("email");
            $request->request->remove("transaction_summary_perference");

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
        try {
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
                $message = $validator->errors()->all();
                $res = ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $customer = $this->customerRepository->update($request, $id);

            $message = "Customer updated successfully";
            $res = ResponseHelper::successHandler($customer, $message, RESPONSE::HTTP_OK);
        } 
        catch (ModelNotFoundException) {
            $message = "Resource not found";
            $res = ResponseHelper::errorHandling($message, Response::HTTP_NOT_FOUND);
        } 
        catch (QueryException $e) {
            $message = json_encode($e->errorInfo);
        }
        catch (Exception $ex) {
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
        try {
            $request = new Request;
            $request->query->add(['id' => $id]);

            $this->customerRepository->delete($id);
            $message = "Customer deleted successfully";
            $res = ResponseHelper::successHandler($data = [], $message, RESPONSE::HTTP_OK);
        } 
        catch (ModelNotFoundException) {
            $message = "Resource not found";
            $res = ResponseHelper::errorHandling($message, Response::HTTP_NOT_FOUND);
        } 
        catch(MethodNotAllowedHttpException $ex){
            $message = $ex->getMessage();
            $res = ResponseHelper::errorHandling($message, Response::HTTP_METHOD_NOT_ALLOWED);
        }
        catch (Exception $ex) {
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
