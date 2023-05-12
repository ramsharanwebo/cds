<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Repositories\TicketRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class TicketController extends Controller
{
    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    { 
        $this->ticketRepository = $ticketRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page_num = $request->page??1;
            $key = $request->key??'';
            $sort_by = $request->sortby??"id";
            $order = $request->order??"ASC";
            $per_page = $request->per_page??2;
            
            $tickets = $this->ticketRepository->getAllPaginated($page_num, $per_page, $sort_by, $order, $key);
            
            return ResponseHelper::successHandler($tickets, "Tickets are fetched successfully", RESPONSE::HTTP_OK);
            } catch (Exception $ex) {
                return ResponseHelper::errorHandling($data=[], $ex->getMessage(), RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    public function show(int $id): JsonResponse
    {
        try{
            $ticket = $this->ticketRepository->getById($id);
            return ResponseHelper::successHandler($ticket, "Ticket fetched successfully", RESPONSE::HTTP_OK);
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
                "location_id"=> "required|integer",
                "customer_id"=> "required|integer",
                "ticket_date"=> "required|date",
                "reference"=> "nullable|max:199",
                "amount"=> "nullable|max:199",
                "container_qty"=> "nullable|integer",
                "created_by"=> "nullable|integer",
                "container_qty"=> "nullable|integer",
                "status"=> "boolean|required"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $location = $this->ticketRepository->create($request);
            
            return ResponseHelper::successHandler($location, "Ticket created successfully", RESPONSE::HTTP_CREATED);
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
                "location_id"=> "sometimes|integer",
                "customer_id"=> "sometimes|integer",
                "ticket_date"=> "sometimes|date",
                "reference"=> "nullable|max:199",
                "amount"=> "nullable|max:199",
                "container_qty"=> "nullable|integer",
                "created_by"=> "nullable|integer",
                "container_qty"=> "nullable|integer",
                "status"=> "boolean|sometimes"
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorHandling($validator->errors(), RESPONSE::HTTP_UNPROCESSABLE_ENTITY);
            }

            $ticket = $this->ticketRepository->update($request, $id);
            
            return ResponseHelper::successHandler($ticket, "Ticket updated successfully", RESPONSE::HTTP_OK);
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
            $this->ticketRepository->delete($id);
            
            return ResponseHelper::successHandler($data=[], "Ticket deleted successfully", RESPONSE::HTTP_OK);
        }
        catch(ModelNotFoundException $modelNotFoundException){
            return ResponseHelper::errorHandling("Resource not found", Response::HTTP_NOT_FOUND);
        }
        catch(Exception $ex){
            return ResponseHelper::errorHandling($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
