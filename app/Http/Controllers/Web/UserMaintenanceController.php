<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserMaintenanceController extends Controller
{
    public function fetchUsers()
    {
        $users = User::all();
        return response()->json($users);
    }
}
