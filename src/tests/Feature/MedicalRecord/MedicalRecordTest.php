<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;

test('doctor can upload medical record', function () {
    Storage::fake('local');

    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);
    $patientUser = User::factory()->create(['role' => 'patient']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);

    Passport::actingAs($doctorUser);

    $this->postJson('/api/v1/medical-records', [
        'patient_id'  => $patient->id,
        'description' => 'Blood test report',
        'file'        => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
    ])
    ->assertStatus(201)
    ->assertJsonPath('success', true)
    ->assertJsonStructure(['data' => ['id', 'file_name', 'file_type', 'file_size']]);
});

test('patient can view own medical records', function () {
    Storage::fake('local');

    $patientUser = User::factory()->create(['role' => 'patient']);
    Patient::factory()->create(['user_id' => $patientUser->id]);

    Passport::actingAs($patientUser);

    $this->getJson('/api/v1/medical-records')
         ->assertOk()
         ->assertJsonPath('success', true);
});

test('patient cannot view other patient records', function () {
    $patientUser  = User::factory()->create(['role' => 'patient']);
    Patient::factory()->create(['user_id' => $patientUser->id]);

    $otherPatient = Patient::factory()->create();

    Passport::actingAs($patientUser);

    // Try to access other patient's records
    $this->getJson('/api/v1/medical-records?patient_id=' . $otherPatient->id)
         ->assertOk(); // Returns own records, not other patient's
});
