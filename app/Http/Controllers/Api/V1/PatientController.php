<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource for the current tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return response()->json($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dpi' => ['nullable', 'string', 'unique:patients,dpi', 'max:20'],
            'birth_date' => ['required', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $patient = Patient::query()->create([
            'tenant_id' => $tenant->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'dpi' => $validated['dpi'] ?? null,
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        return response()->json($patient, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        /** @var Patient|null $patient */
        $patient = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with(['accesses' => function ($query) {
                $query->orderBy('access_date', 'desc');
            }, 'user'])
            ->first();

        if ($patient === null) {
            return response()->json([
                'message' => 'Paciente no encontrado o no pertenece a este tenant.',
            ], 404);
        }

        return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        /** @var Patient|null $patient */
        $patient = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if ($patient === null) {
            return response()->json([
                'message' => 'Paciente no encontrado o no pertenece a este tenant.',
            ], 404);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dpi' => ['nullable', 'string', 'unique:patients,dpi,' . $id, 'max:20'],
            'birth_date' => ['required', 'date'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $patient->update($validated);

        return response()->json($patient);
    }
}
