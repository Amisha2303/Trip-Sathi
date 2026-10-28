<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->withCount('bookings')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . addcslashes($request->string('q')->toString(), '%_\\') . '%';
                $q->where(fn ($inner) => $inner
                    ->where('name', 'LIKE', $term)
                    ->orWhere('email', 'LIKE', $term)
                    ->orWhere('phone', 'LIKE', $term));
            })
            ->when($request->filled('role'), fn ($q) => $request->string('role') === 'admin'
                ? $q->where('is_admin', true)
                : $q->where('is_admin', false))
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['q', 'role', 'status']),
        ]);
    }

    public function show(User $user): Response
    {
        $user->load([
            'bookings' => fn ($q) => $q->latest('booking_date')->limit(20),
            'bookings.destination:id,name',
        ]);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $user->bookingStats(),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        // Guard rails: an admin cannot strip their own access or lock themselves
        // out mid-session.
        $isSelf = $request->user()?->is($user);

        $data = $request->safe()->only(['name', 'email', 'phone', 'city', 'state', 'country']);

        if (! $isSelf) {
            $data['is_admin'] = $request->boolean('is_admin');
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->toString();
        }

        // is_admin / is_active are outside $fillable; forceFill the guarded keys.
        $user->forceFill($data)->save();

        if (isset($data['is_admin'])) {
            User::flushAdminCache();
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'Account updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User account deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Cannot delete user: ' . $e->getMessage());
        }
    }
}
