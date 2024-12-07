<?php

namespace App\Http\Controllers\Web;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\Task;
use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\GetExpenseListingsRequest;
use App\Http\Requests\Web\CreateExpenseRequest;
use App\Http\Requests\Web\CreateExpenseCategoryRequest;
use App\Http\Requests\Web\UpdateExpenseRequest;
use App\Http\Requests\Web\UpdateExpenseCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpenseController extends Controller
{
    public function getExpenseListings(GetExpenseListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');

        $expenses = Expense::query()->with('expenseCategory:id,name');

        if ($searchQuery) {
            $expenses->where('name', 'like', '%' . $searchQuery . '%');
        }

        return response()->json($expenses->paginate($perPage));
    }

    public function expenseInfo($id)
    {
        // Fetch the expense by its ID
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Return the expense details
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
        // Create the expense using request inputs
        $expense = Expense::create([
            'name' => $request->input('name'),
            'expense_category_id' => $request->input('expense_category_id'),
            'project_id' => $request->input('project_id'),
            'task_id' => $request->input('task_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'date_incurred' => $request->input('date_incurred'), // Ensure date_incurred is correctly passed
            'budget_id' => $request->input('budget_id'),
        ]);
    
        // Return response
        return response()->json([
            'message' => 'Expense created successfully.',
            'expense' => $expense
        ]);
    }
    

    public function updateExpense(UpdateExpenseRequest $request)
    {
        // Find the expense by its ID
        $expense = Expense::find($request->input('id'));
    
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }
    
        // Update the expense's details
        $expense->update([
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'date_incurred' => $request->input('date_incurred'),
            'expense_category_id' => $request->input('expense_category_id'),
            'project_id' => $request->input('project_id'),
            'task_id' => $request->input('task_id'),
        ]);
    
        // Return a success response
        return response()->json([
            'message' => 'Expense updated successfully.',
            'expense' => $expense
        ]);
    }
    

    public function deleteExpense($id)
    {
        try {
            $expense = Expense::findOrFail($id);

            $expense->delete();

            return response()->json(['message' => 'Expense deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Expense not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete expense.'], 500);
        }
    }

    // Expense Category Methods
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
        // Fetch the expense category by its ID
        $category = ExpenseCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Expense Category not found'], 404);
        }

        // Return the expense category details
        return response()->json([
            'expenseCategory' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description, // assuming the category has a description
                // Add any other relevant fields you need from the ExpenseCategory model
            ]
        ]);
    }


    public function createExpenseCategory(CreateExpenseCategoryRequest $request)
    {
        $category = ExpenseCategory::create($request->validated());
    
        return response()->json([
            'message' => 'Expense category created successfully.',
            'expenseCategory' => $category
        ]);
    }

    public function updateExpenseCategory(UpdateExpenseCategoryRequest $request)
    {
        $category = ExpenseCategory::find($request->input('id'));

            // Check if the category exists
        if (!$category) {
            return response()->json(['message' => 'Expense category not found'], 404);
        }

        // Update the expense category details
        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
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

            return response()->json(['message' => 'Expense category deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Expense category not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete expense category.'], 500);
        }
    }

    // Fetch the list of projects
    public function fetchProjects()
    {
        $projects = Project::all(); // Fetch all projects (you can modify the query as needed)

        return response()->json([
            'projects' => $projects,
        ]);
    }

    // Fetch the list of expense categories
    public function fetchExpenseCategories()
    {
        $expenseCategories = ExpenseCategory::all(); // Fetch all expense categories

        return response()->json([
            'expenseCategories' => $expenseCategories,
        ]);
    }

    // Fetch the list of tasks
    public function fetchTasks()
    {
        $tasks = Task::all(); // Fetch all tasks (you can modify the query as needed)

        return response()->json([
            'tasks' => $tasks,
        ]);
    }

    // Fetch the list of budgets
    public function fetchBudgets()
    {
        $budgets = Budget::all(); // Fetch all budgets (you can modify the query as needed)

        return response()->json([
            'budgets' => $budgets,
        ]);
    }

}
