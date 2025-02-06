<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientPrescriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'prescription_id'   => $this->id,
            'patient_id'        => $this->patient_id,
            'doctor_id'         => $this->doctor_id,
            'medicine_name'     => $this->medicine_name,
            'dosage'            => $this->dosage,
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'special_notes'     => $this->special_notes,
        ];
    }
}
