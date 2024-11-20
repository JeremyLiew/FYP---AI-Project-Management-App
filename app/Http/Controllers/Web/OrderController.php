<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParamIdFormRequest;
use App\Http\Requests\Web\Order\ListFormRequest;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function orders(ListFormRequest $request)
    {
        $payload = $request->validated();

        $user = User::find($payload['id']);

        $orders = $user->orders()->paginate($payload['itemsPerPage'] ?? 15);

        $orders->load([
            'products.media',
        ]);

        return self::successResponse('Success', $orders);
    }

}
