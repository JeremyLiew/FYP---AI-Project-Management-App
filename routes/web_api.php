<?php

use App\Http\Controllers\Web\{
    ContactUsController,
    ProjectController,
    TaskController,
};
use App\Http\Controllers\Auth\{
    AuthController
};

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('general')->group(function () {
        Route::post('/contact-us', [ContactUsController::class, 'sendEmail']);
    });

});

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('project')->group(function () {
        Route::get('/listings', [ProjectController::class, 'getProjectListings']);
        Route::post('/create', [ProjectController::class, 'createProject']);
        Route::post('/update', [ProjectController::class, 'updateProject']);
        Route::get('/info/{id}', [ProjectController::class, 'projectInfo']);
        Route::get('/users-and-roles', [ProjectController::class, 'fetchUsersAndRoles']);
        Route::post('/delete/{id}', [ProjectController::class, 'deleteProject']);
    });
});

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('task')->group(function () {
        Route::get('/listings', [TaskController::class, 'getTasksByProject']);
        Route::post('/create', [TaskController::class, 'createTask']);
        Route::post('/update', [TaskController::class, 'updateTask']);
        Route::get('/info/{id}', [TaskController::class, 'TaskInfo']);
        Route::get('/{project}/members', [TaskController::class, 'fetchMembers']);
        Route::post('/delete/{id}', [TaskController::class, 'deleteTask']);
        Route::get('/comments/{id}', [TaskController::class, 'getTaskComments']);
        Route::post('/create/comment', [TaskController::class, 'createTaskComment']);
        Route::post('/edit/comment', [TaskController::class, 'editTaskComment']);
        Route::post('/delete/comment/{id}', [TaskController::class, 'deleteTaskComment']);
    });
});

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

// sanctum route
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
