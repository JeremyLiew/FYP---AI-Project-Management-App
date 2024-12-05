<?php

namespace App\Http\Controllers\Web;

use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CreateBudgetRequest;
use App\Http\Requests\Web\UpdateBudgetRequest;
use App\Http\Requests\Web\GetBudgetListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BudgetController extends Controller
{
    public function getBudgetListings(GetBudgetListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10); // Items per page for pagination
        $searchQuery = $request->input('searchQuery', ''); // Search term
        $statusFilter = $request->input('selectedFilter', 'All'); // Filter by status

        $budgetsQuery = Budget::query();

        // Apply search query filter
        if ($searchQuery) {
            $budgetsQuery->where('name', 'like', '%' . $searchQuery . '%');
        }

        // Paginate results
        $budgets = $budgetsQuery->paginate($perPage);

        return response()->json($budgets);
    }

    public function budgetInfo($id)
    {
        // Fetch the budget by its ID
        $budget = Budget::find($id);

        if (!$budget) {
            return response()->json(['message' => 'Budget not found'], 404);
        }

        // Return the budget details
        return response()->json([
            'budget' => [
                'id' => $budget->id,
                'name' => $budget->name,
                'total_budget' => $budget->total_budget,
                'remaining_amount' => $budget->remaining_amount,
            ]
        ]);
    }

    public function createBudget(CreateBudgetRequest $request)
    {
        // Create the budget and set remaining_amount to total_budget
        $budget = Budget::create([
            'name' => $request->input('name'),
            'total_budget' => $request->input('total_budget'),
            'remaining_amount' => $request->input('total_budget'), 
        ]);
    
        return response()->json([
            'message' => 'Budget created successfully.',
            'budget' => $budget
        ]);
    }
    

    public function updateBudget(UpdateBudgetRequest $request)
    {
        // Find the budget by its ID
        $budget = Budget::find($request->input('id'));
    
        if (!$budget) {
            return response()->json(['message' => 'Budget not found'], 404);
        }
    
        // Update the budget's details
        $budget->update([
            'name' => $request->input('name'),
            'total_budget' => $request->input('total_budget'),
            'remaining_amount' => $request->input('total_budget'), 
        ]);
    
        // Return a success response
        return response()->json([
            'message' => 'Budget updated successfully.',
            'budget' => $budget
        ]);
    }

    public function deleteBudget($id)
    {
        try {
            // Find the budget by its ID
            $budget = Budget::findOrFail($id);
            
            // Delete the budget
            $budget->delete();

            // Return success response
            return response()->json(['message' => 'Budget deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the budget is not found
            return response()->json(['error' => 'Budget not found.'], 404);
        } catch (\Exception $e) {
            // Handle any other errors during deletion
            return response()->json(['error' => 'Failed to delete budget.'], 500);
        }
    }
}
