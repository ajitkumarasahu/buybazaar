<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AddressResource transforms the Address model into a JSON-friendly format for API responses.
 * It provides a structured representation of an address, including its attributes and timestamps.
 */
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * This method is called when the resource is being converted to an array or JSON format.
     * It defines how the address data should be structured in the API response.
     *
     * @param Request $request The incoming HTTP request.
     * @return array The transformed address data as an associative array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at?->utc()->toIso8601String(),
            'updated_at' => $this->updated_at?->utc()->toIso8601String(),
        ];
    }
}