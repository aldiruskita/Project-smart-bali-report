<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Admin\UserManagementController;

// ─── Public Routes ──────────────────────────────
Route::get('/', function () {
    $reports = \App\Models\Report::with(['category'])->withCount('votes')->latest()->take(6)->get();
    $categories = \App\Models\Category::withCount('reports')->get();
    $stats = [
        'total' => \App\Models\Report::count(),
        'done' => \App\Models\Report::where('status', 'done')->count(),
        'process' => \App\Models\Report::where('status', 'process')->count(),
    ];
    return view('home', compact('reports', 'categories', 'stats'));
})->name('home');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/api/reports/map', [ReportController::class, 'mapData'])->name('api.reports.map');
Route::post('/api/ai/classify', [ReportController::class, 'classifyPreview'])->name('api.ai.classify');

// ─── Auth Routes ────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Authenticated User Routes ──────────────────
Route::middleware('auth')->group(function () {
    Route::get('/reports/create/new', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/my-reports', [ReportController::class, 'myReports'])->name('reports.my');

    // Votes
    Route::post('/reports/{report}/vote', [VoteController::class, 'toggle'])->name('reports.vote');

    // Comments
    Route::post('/reports/{report}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Ratings
    Route::post('/reports/{report}/ratings', [RatingController::class, 'store'])->name('ratings.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});

// ─── Admin Routes ───────────────────────────────
Route::middleware(['auth', 'role:admin_desa,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/charts', [DashboardController::class, 'chartData'])->name('charts');

    Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports');
    Route::patch('/reports/{report}/status', [ReportManagementController::class, 'updateStatus'])->name('reports.status');
    Route::post('/reports/{report}/assign', [ReportManagementController::class, 'assign'])->name('reports.assign');
    Route::delete('/reports/{report}', [ReportManagementController::class, 'destroy'])->name('reports.destroy');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Admin: Verify task
    Route::post('/tasks/{task}/verify', [TaskController::class, 'verify'])->name('tasks.verify');
});

// ─── Officer/Petugas Routes ─────────────────────
Route::middleware(['auth', 'role:petugas,admin_desa,super_admin'])->group(function () {
    Route::get('/officer/dashboard', [TaskController::class, 'dashboard'])->name('officer.dashboard');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/accept', [TaskController::class, 'accept'])->name('tasks.accept');
    Route::post('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.progress');
    Route::post('/tasks/{task}/comment', [TaskController::class, 'addComment'])->name('tasks.comment');
    Route::post('/tasks/{task}/upload', [TaskController::class, 'uploadAttachment'])->name('tasks.upload');
});
