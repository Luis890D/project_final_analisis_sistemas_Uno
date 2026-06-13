<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAccess extends Model
{
    use HasFactory;

    protected $table = 'patient_accesses';

    protected $fillable = [
        'patient_id',
        'type',
        'description',
        'access_date',
        'doctor_in_charge',
        'status',
    ];

    protected $casts = [
        'access_date' => 'datetime',
    ];

    /**
     * Get the patient that owns the access record.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
}
