<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CreateExpenseRequest;
use App\Http\Requests\Web\UpdateExpenseRequest;
use App\Http\Requests\Web\GetExpenseListingsRequest;
use App\Http\Requests\Web\CreateExpenseCategoryRequest;
use App\Http\Requests\Web\UpdateExpenseCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpenseController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function getExpenseListings(GetExpenseListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');

        $expenses = Expense::query()->with('expenseCategory:id,name');

        if ($searchQuery) {
            $expenses->where('name', 'like', '%' . $searchQuery . '%');
        }

        $this->activityLogger->logActivity('Viewed expense listings', Expense::class, 0);

        return response()->json($expenses->paginate($perPage));
    }

    public function expenseInfo($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $this->activityLogger->logActivity('Viewed expense details', Expense::class, $id);

        return response()->json([
            'expense' => [
                'id' => $expense->id,
                'name' => $expense->name,
                'amount' => $expense->amount,
                'description' => $expense->description,
                'date_incurred' => $expense->date_incurred,
                'expense_category_id' => $expense->expense_category_id,
                'project_id' => $expense->project_id,
                'task_id' => $expense->task_id,
                'budget_id' => $expense->budget_id,
            ]
        ]);
    }

    public function createExpense(CreateExpenseRequest $request)
    {
        $expense = Expense::create([
            'name' => $request->input('name'),
            'expense_category_id' => $request->input('expense_category_id'),
            'project_id' => $request->input('project_id'),
            'task_id' => $request->input('task_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'date_incurred' => $request->input('date_incurred'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $this->activityLogger->logActivity('Created a new expense', Expense::class, $expense->id);

        return response()->json([
            'message' => 'Expense created successfully.',
            'expense' => $expense
        ]);
    }


    public function updateExpense(UpdateExpenseRequest $request)
    {
        $expense = Expense::find($request->input('id'));

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        $previousData = $expense->getOriginal();

        $expense->update([
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'date_incurred' => $request->input('date_incurred'),
            'expense_category_id' => $request->input('expense_category_id'),
            'project_id' => $request->input('project_id'),
            'task_id' => $request->input('task_id'),
        ]);

        $this->activityLogger->logActivity('Updated expense details', Expense::class, $expense->id, [
            'previous' => $previousData,
            'updated' => $expense->getAttributes()
        ]);

        return response()->json([
            'message' => 'Expense updated successfully.',
            'expense' => $expense
        ]);
    }

    public function deleteExpense($id)
    {
        try {
            $expense = Expense::findOrFail($id);

            $this->activityLogger->logActivity('Deleted an expense', Expense::class, $id, null, 'warning');

            $expense->delete();

            return response()->json(['message' => 'Expense deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Expense not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete expense.'], 500);
        }
    }

    public function getExpenseCategories(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');

        $categories = ExpenseCategory::query();

        if ($searchQuery) {
            $categories->where('name', 'like', '%' . $searchQuery . '%');
        }

        return response()->json($categories->paginate($perPage));
    }

    public function categoryInfo($id)
    {
        $category = ExpenseCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Expense Category not found'], 404);
        }

        return response()->json([
            'expenseCategory' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
            ]
        ]);
    }

    public function createExpenseCategory(CreateExpenseCategoryRequest $request)
    {
        $category = ExpenseCategory::create($request->validated());

        $this->activityLogger->logActivity('Created a new expense category', ExpenseCategory::class, $category->id);

        return response()->json([
            'message' => 'Expense category created successfully.',
            'expenseCategory' => $category
        ]);
    }

    public function updateExpenseCategory(UpdateExpenseCategoryRequest $request)
    {
        $category = ExpenseCategory::find($request->input('id'));

        if (!$category) {
            return response()->json(['message' => 'Expense category not found'], 404);
        }

        $previousData = $category->getOriginal();

        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        $this->activityLogger->logActivity('Updated expense category details', ExpenseCategory::class, $category->id, [
            'previous' => $previousData,
            'updated' => $category->getAttributes()
        ]);

        return response()->json([
            'message' => 'Expense category updated successfully.',
            'expenseCategory' => $category
        ]);
    }

    public function deleteExpenseCategory($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);
            $category->delete();

            $this->activityLogger->logActivity('Deleted an expense category', ExpenseCategory::class, $id, null, 'warning');

            return response()->json(['message' => 'Expense category deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Expense category not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete expense category.'], 500);
        }
    }

    public function fetchProjects()
    {
        $projects = Project::all();

        return response()->json([
            'projects' => $projects,
        ]);
    }

    public function fetchExpenseCategories()
    {
        $expenseCategories = ExpenseCategory::all();

        return response()->json([
            'expenseCategories' => $expenseCategories,
        ]);
    }

    public function fetchTasks($projectId)
    {
        // Fetch tasks associated with the given project ID
        $tasks = Task::where('project_id', $projectId)->get();
    
        // Return the tasks as a JSON response
        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    public function fetchBudgets()
    {
        $budgets = Budget::all();

        return response()->json([
            'budgets' => $budgets,
        ]);
    }

}
