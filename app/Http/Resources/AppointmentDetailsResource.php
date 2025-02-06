<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'appointment_id'    => $this->id,
            'patient_id'        => $this->patient_id,
            'doctor_id'         => $this->doctor_id,
            'appointment_date'  => $this->appointment_date,
            'start_time'        => $this->start_time,
            'end_time'          => $this->end_time,
            'problems'          => $this->problems,
            'status'            => $this->status,
        ];
    }
}
