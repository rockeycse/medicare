<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->when(
                isset($filters['role']),
                fn($q) => $q->where('role', $filters['role'])
            )
            ->when(
                isset($filters['search']),
                fn($q) => $q->where(function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%");
                })
            )
            ->when(
                isset($filters['is_active']),
                fn($q) => $q->where('is_active', $filters['is_active'])
            )
            ->latest()
            ->paginate(15);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function toggleStatus(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);
        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
