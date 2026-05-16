<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }
        $users = $query->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:warga,petugas,admin_desa,super_admin',
            'password' => 'required|string|min:6',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:warga,petugas,admin_desa,super_admin',
        ]);
        $user->update($validated);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Prevent deleting super_admin
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Akun Super Admin tidak bisa dihapus.');
        }

        try {
            // Reassign tasks assigned BY this user to current admin
            \App\Models\Task::where('assigned_by', $user->id)->update(['assigned_by' => auth()->id()]);
            \App\Models\Assignment::where('assigned_by', $user->id)->update(['assigned_by' => auth()->id()]);

            // Delete tasks assigned TO this user
            $user->tasks()->each(function ($task) {
                $task->comments()->delete();
                $task->attachments()->each(function ($att) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($att->file_path);
                    $att->delete();
                });
                $task->delete();
            });

            // Delete user's reports and all related data
            $user->reports()->each(function ($report) {
                $report->task?->comments()->delete();
                $report->task?->attachments()->delete();
                $report->task?->delete();
                $report->assignment()?->delete();
                foreach ($report->media as $m) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($m->file_path);
                }
                $report->media()->delete();
                $report->logs()->delete();
                $report->comments()->delete();
                $report->votes()->delete();
                $report->ratings()->delete();
                $report->delete();
            });

            // Delete remaining user data
            $user->comments()->delete();
            $user->votes()->delete();
            $user->ratings()->delete();
            \App\Models\TaskComment::where('user_id', $user->id)->delete();

            // Delete avatar
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
