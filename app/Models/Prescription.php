<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'manage_prescriptions';

    protected $fillable = [
        'id',
        'patient_id',
        'doctor_id',
        'medicine_name',
        'dosage',
        'start_date',
        'end_date',
        'special_notes',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    protected $casts = [
        'created_at' => 'datetime:D d M Y H:i:s',
        'updated_at' => 'datetime:D d M Y H:i:s'
    ];
}
