<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['org_admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['manager', 'executive'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Team member added successfully',
            'user' => $user
        ], 201);
    }

    public function toggleStatus(User $user)
    {
        if ($user->organization_id !== auth()->user()->organization_id) {
            abort(404);
        }

        if (!auth()->user()->hasRole(['org_admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'You cannot suspend yourself.'], 400);
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'message' => "User status updated to {$newStatus} successfully.",
            'user' => $user
        ]);
    }
}
