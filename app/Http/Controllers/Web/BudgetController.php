<?php

namespace App\Http\Controllers\Web;

use App\Models\Budget;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CreateBudgetRequest;
use App\Http\Requests\Web\UpdateBudgetRequest;
use App\Http\Requests\Web\GetBudgetListingsRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BudgetController extends Controller
{

    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function getBudgetListings(GetBudgetListingsRequest $request)
    {
        $perPage = $request->input('itemsPerPage', 10);
        $searchQuery = $request->input('searchQuery', '');
        $statusFilter = $request->input('selectedFilter', 'All');

        $budgetsQuery = Budget::query();

        if ($searchQuery) {
            $budgetsQuery->where('name', 'like', '%' . $searchQuery . '%');
        }

        $this->activityLogger->logActivity('Viewed budget listings', Budget::class, 0, null, 'info');

        $budgets = $budgetsQuery->paginate($perPage);

        return response()->json($budgets);
    }

    public function budgetInfo($id)
    {
        // Fetch the budget by its ID
        $budget = Budget::find($id);

        if (!$budget) {
            $this->activityLogger->logActivity('Budget not found', Budget::class, $id, null, 'error');
            return response()->json(['message' => 'Budget not found'], 404);
        }

        $this->activityLogger->logActivity('Viewed budget details', Budget::class, $id, null, 'info');

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
        $budget = Budget::create([
            'name' => $request->input('name'),
            'total_budget' => $request->input('total_budget'),
            'remaining_amount' => $request->input('total_budget'),
        ]);

        $this->activityLogger->logActivity('Created a new budget', Budget::class, $budget->id, null, 'info');

        return response()->json([
            'message' => 'Budget created successfully.',
            'budget' => $budget
        ]);
    }

    public function updateBudget(UpdateBudgetRequest $request)
    {
        $budget = Budget::find($request->input('id'));

        $previousData = $budget->getOriginal();

        if (!$budget) {
            $this->activityLogger->logActivity('Budget not found for update', Budget::class, $request->input('id'), null, 'error');
            return response()->json(['message' => 'Budget not found'], 404);
        }

        $budget->update([
            'name' => $request->input('name'),
            'total_budget' => $request->input('total_budget'),
            'remaining_amount' => $request->input('total_budget'),
        ]);

        $this->activityLogger->logActivity('Updated budget details', Budget::class, $budget->id, [
            'previous' => $previousData,
            'updated' => $budget->getAttributes(),
        ]);

        return response()->json([
            'message' => 'Budget updated successfully.',
            'budget' => $budget
        ]);
    }

    public function deleteBudget($id)
    {
        try {
            $budget = Budget::findOrFail($id);

            $budget->delete();

            $this->activityLogger->logActivity('Deleted a budget', Budget::class, $id, null, 'warning');

            return response()->json(['message' => 'Budget deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            $this->activityLogger->logActivity('Budget not found for deletion', Budget::class, $id, null, 'error');
            return response()->json(['error' => 'Budget not found.'], 404);
        } catch (\Exception $e) {
            $this->activityLogger->logActivity('Failed to delete budget', Budget::class, $id, null, 'error');
            return response()->json(['error' => 'Failed to delete budget.'], 500);
        }
    }
}
