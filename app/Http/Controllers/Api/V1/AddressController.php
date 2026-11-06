<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User Addresses
 *
 * APIs for managing user addresses.
 */
class AddressController extends Controller
{
    /**
     * AddressController constructor.
     * it calls the AddressService to handle the business logic for address management.
     * @param AddressService $addressService
     */
    public function __construct(private AddressService $addressService)
    {
    }

    /**
     * List all addresses for the authenticated user.
     * if authenticated user has no addresses, it returns an empty array.
     */
    public function index(Request $request): JsonResponse
    {
        // List all addresses for the authenticated user, ordered by default status and creation date.
        $addresses = $this->addressService->listFor($request->user());

        //return api response in 
        return ApiResponse::success(AddressResource::collection($addresses));
    }

    /**
     * Store a new address for the authenticated user.
     * Validates the request data and creates a new address.
     * If the user has no addresses, the first address is automatically set as default.
     */
    public function store(AddressRequest $request): JsonResponse
    {
        $address = $this->addressService->create($request->user(), $request->validated());

        return ApiResponse::success('Address added successfully.', 201);
    }

    /**
     * Show a specific address for the authenticated user.
     * @param Address $address
     * @return JsonResponse
     */
    public function show(Address $address): JsonResponse
    {
        return ApiResponse::success(new AddressResource($address));
    }

    /**
     * Update a specific address for the authenticated user.
     * Validates the request data and updates the address.
     * @param AddressRequest $request
     * @param Address $address
     * @return JsonResponse
     */
    public function update(AddressRequest $request, Address $address): JsonResponse
    {
        $address = $this->addressService->update($address, $request->validated());

        return ApiResponse::success('Address updated successfully.', 200);
    }

    /**
     * Delete a specific address for the authenticated user.
     * @param Address $address
     * @return JsonResponse
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->addressService->delete($address);

        return ApiResponse::success(null, 'Address deleted successfully.');
    }

    /**
     * Set a specific address as the default for the authenticated user.
     * @param Address $address
     * @return JsonResponse
     */
    public function setDefault(Address $address): JsonResponse
    {
        $address = $this->addressService->setDefault($address);
    
        return ApiResponse::success('Default address set.', 200);
    }
}