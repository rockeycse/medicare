<?php

namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'password' => $data['password'],
                'role'     => $data['role'] ?? 'patient',
            ]);

            if ($user->isPatient()) {
                Patient::create(['user_id' => $user->id]);
            }

            // Passport token
            $tokenResult = $user->createToken('MediCare');
            $token       = $tokenResult->accessToken;

            return ['user' => $user, 'token' => $token];
        });
    }
    public function login(array $credentials): array
    {
        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        $tokenResult = $user->createToken('MediCare');
        $token       = $tokenResult->accessToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->token()->revoke();
    }
}
