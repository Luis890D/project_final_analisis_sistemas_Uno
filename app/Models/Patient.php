<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'dpi',
        'birth_date',
        'phone',
        'address',
        'gender',
        'blood_type',
        'allergies',
        'user_id',
    ];

    /**
     * Get the tenant that owns the patient.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Get the user associated with the patient.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the accesses/medical history for the patient.
     */
    public function accesses(): HasMany
    {
        return $this->hasMany(PatientAccess::class, 'patient_id', 'id');
    }
}
