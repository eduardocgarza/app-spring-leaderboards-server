<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'userID' => $this->id,
            'username' => $this->username,
            'firstName' => $this->first_name,
            'points' => (int) $this->points,
            'dateBirth' => $this->date_birth,
            'address' => $this->address,
        ];
    }
}
