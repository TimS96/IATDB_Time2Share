<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserPublicController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', fn () => redirect()->route('items.index'));
Route::get('/dashboard', fn () => redirect()->route('items.index'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Profile (auth required, not blocked)
 */
Route::middleware(['auth', 'ensure.not.blocked'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Categories (public)
 * - Items by category
 */
Route::get('/categories/{category}/items', [ItemController::class, 'byCategory'])
    ->name('categories.items');

// Fallback for old links to categories.index
Route::get('/categories', fn () => redirect()->route('items.index'))
    ->name('categories.index');


/**
 * Items
 */
Route::get('/items', [ItemController::class, 'index'])->name('items.index');

Route::middleware(['auth', 'ensure.not.blocked'])->group(function () {
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::patch('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

/**
 * Loans & Reviews (auth, not blocked)
 */
Route::middleware(['auth', 'ensure.not.blocked'])->group(function () {
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::patch('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

/**
 * Public user profile
 */
Route::get('/users/{user}', [UserPublicController::class, 'show'])->name('users.show');

/**
 * Admin-only routes (all under /admin, names prefixed with admin.)
 */
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth', 'ensure.not.blocked', AdminMiddleware::class])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        // Toggle block/unblock with one route
        Route::post('/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])
            ->name('users.toggleBlock');

        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });



require __DIR__ . '/auth.php';
