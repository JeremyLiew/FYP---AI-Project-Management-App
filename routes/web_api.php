<?php

use App\Http\Controllers\Web\{
    ActivityLogController,
    ContactUsController,
    ProjectController,
    TaskController,
    NotificationController,
    ProfileController,
    BudgetController,
    ExpenseController,
    UserMaintenanceController,
    ReportController,
    HomeController,
    OpenAIController,
    SettingsController,
    WeatherController,
};
use App\Http\Controllers\Auth\{
    AuthController,
    CustomVerificationController,
};

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('general')->group(function () {
        Route::post('/contact-us', [ContactUsController::class, 'sendEmail']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::get('/profile', [ProfileController::class, 'getUserProfile']);
        Route::post('/user/profile-picture', [ProfileController::class, 'updateProfilePicture']);
        Route::post('/user/update-user-name', [ProfileController::class, 'updateUserName']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('project')->group(function () {
        Route::get('/listings', [ProjectController::class, 'getProjectListings']);
        Route::post('/create', [ProjectController::class, 'createProject']);
        Route::post('/update', [ProjectController::class, 'updateProject']);
        Route::get('/info/{id}', [ProjectController::class, 'projectInfo']);
        Route::get('/users-and-roles', [ProjectController::class, 'fetchUsersAndRoles']);
        Route::post('/delete/{id}', [ProjectController::class, 'deleteProject']);
        Route::get('/file/{id}', [ProjectController::class, 'fetchAttachment']);
        Route::get('/download/{id}', [ProjectController::class, 'downloadAttachment']);
        Route::get('/project/download/{projectId}', [ProjectController::class, 'downloadAttachment'])->name('download.attachment');
        Route::get('/budgets', [ProjectController::class, 'getBudgets']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user-maintenance')->group(function () {
        Route::get('/users', [UserMaintenanceController::class, 'fetchUsers']);
        Route::post('/update', [UserMaintenanceController::class, 'updateUser']);
        Route::post('/delete/{id}', [UserMaintenanceController::class, 'deleteUser']);
        Route::get('/application-roles', [UserMaintenanceController::class, 'fetchApplicationRoles']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
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

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('activity-log')->group(function () {
        Route::get('/listings', [ActivityLogController::class, 'getActivityLogs']);
        Route::get('/users', [ActivityLogController::class, 'fetchUsers']);
    });

    Route::prefix('budget')->group(function () {
        Route::get('/listings', [BudgetController::class, 'getBudgetListings']);
        Route::post('/create', [BudgetController::class, 'createBudget']);
        Route::post('/update', [BudgetController::class, 'updateBudget']);
        Route::post('/delete/{id}', [BudgetController::class, 'deleteBudget']);
        Route::get('/info/{id}', [BudgetController::class, 'budgetInfo']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('expense')->group(function () {
        Route::get('/listings', [ExpenseController::class, 'getExpenseListings']);
        Route::post('/create', [ExpenseController::class, 'createExpense']);
        Route::post('/update', [ExpenseController::class, 'updateExpense']);
        Route::post('/delete/{id}', [ExpenseController::class, 'deleteExpense']);
        Route::get('/projects', [ExpenseController::class, 'fetchProjects']);
        Route::get('/tasks/{id}', [ExpenseController::class, 'fetchTasks']);
        Route::get('/expense-categories', [ExpenseController::class, 'fetchExpenseCategories']);
        Route::get('/budgets', [ExpenseController::class, 'fetchBudgets']);

        Route::get('/info/{id}', [ExpenseController::class, 'expenseInfo']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/listings', [ExpenseController::class, 'getExpenseCategories']);
        Route::post('/create', [ExpenseController::class, 'createExpenseCategory']);
        Route::post('/update', [ExpenseController::class, 'updateExpenseCategory']);
        Route::post('/delete/{id}', [ExpenseController::class, 'deleteExpenseCategory']);
        Route::get('/info/{id}', [ExpenseController::class, 'categoryInfo']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('report')->group(function () {
        Route::get('/listings', [ReportController::class, 'getProjectsAndTasks']);
        Route::get('/expense', [ReportController::class, 'getExpenseCategoryData']);
        Route::get('/performance', [ReportController::class, 'getPerformanceData']);
        Route::get('/feedback', [ReportController::class, 'fetchAiFeedback']);
        Route::get('/project-expense/{id}', [ReportController::class, 'fetchExpensesByProjectId']);
        Route::get('/project-task/{id}', [ReportController::class, 'fetchProjectTasks']);
        Route::get('/task/{id}', [ReportController::class, 'fetchTaskStatus']);
        Route::get('/download', [ReportController::class, 'downloadProjectDetails']);
        Route::get('/project/{id}', [ReportController::class, 'fetchProject']);
    });
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('base')->group(function () {
        Route::get('/message', [OpenAIController::class, 'sendMessage']);
        Route::get('/feedbacks', [OpenAIController::class, 'getFeedbacks']);
        Route::post('/generateSummaryFeedback', [OpenAIController::class, 'generateSummaryFeedback']);
        Route::get('/generateProjectInsight/{id}', [OpenAIController::class, 'generateProjectInsight']);
        Route::post('/approved-task', [OpenAIController::class, 'approveTask']);
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

Route::get('/email/verify/{user}/{token}', [CustomVerificationController::class, 'verify'])->name('email.verify');

Route::get('/email/verify', function () {
    return view('auth.verify-notice');
})->name('verification.notice');

Route::post('/email/resend', [CustomVerificationController::class, 'resend'])->name('resend.verification');

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/settings/update', [SettingsController::class, 'updateSettings']);
    Route::get('/settings', [SettingsController::class, 'getUserSettings']); // Fetch user settings
});


// apply this to protect route from not verified email access
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });


