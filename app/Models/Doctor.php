<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctor_details';

    protected $fillable = [
        'doctor_id', 
        'contact_details', 
        'specialities',
        'availability',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function specialities(): Attribute {
        return Attribute::make(
            get: function () {
                return json_decode($this->attributes['specialities']);
            }
        );
    }

    public function doctorDetails(){
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }
}
