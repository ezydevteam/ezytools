<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserManagerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = User::query()->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|in:user,moderator',
            'is_active' => 'required|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Admin users cannot be edited from this panel for security reasons.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:user,moderator',
            'is_active' => 'required|boolean',
            'subscription_type' => 'required|in:free,pro',
            'subscription_expires_at' => 'nullable|date',
            'ai_credit' => 'required|integer|min:0',
        ]);

        $user->update($request->only([
            'name', 'email', 'role', 'is_active', 
            'subscription_type', 'subscription_expires_at', 'ai_credit'
        ]));

        if ($request->filled('password')) {
            $request->validate(['password' => [Rules\Password::defaults()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Admin status cannot be toggled.');
        }

        $user->update(['is_active' => !$user->is_active]);
        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Admin role cannot be changed.');
        }

        $validated = $request->validate([
            'role' => 'required|in:user,moderator',
        ]);

        $user->update($validated);
        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function updateCredits(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot modify admin credits.');
        }

        $request->validate([
            'amount' => 'required|integer',
            'action' => 'required|in:add,subtract,set',
        ]);

        $newCredits = $user->ai_credit;

        if ($request->action === 'add') {
            $newCredits += $request->amount;
        } elseif ($request->action === 'subtract') {
            $newCredits = max(0, $newCredits - $request->amount);
        } else {
            $newCredits = max(0, $request->amount);
        }

        $user->update(['ai_credit' => $newCredits]);

        return redirect()->back()->with('success', 'AI credits updated successfully.');
    }

    public function makePro(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Admins are already pro by default.');
        }

        $request->validate([
            'months' => 'required|integer|min:1',
        ]);

        $defaultCredits = (int) AiSetting::getValue('pro_ai_credit_limit', 1000);

        $user->update([
            'subscription_type' => 'pro',
            'subscription_expires_at' => now()->addMonths($request->months),
            'ai_credit' => $user->ai_credit + $defaultCredits,
        ]);

        return redirect()->back()->with('success', "User is now PRO for {$request->months} months.");
    }

    public function destroy(User $user)
    {
        if ($user->id === 1 || $user->role === 'admin') {
            return redirect()->back()->with('error', 'Admin users cannot be deleted.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
