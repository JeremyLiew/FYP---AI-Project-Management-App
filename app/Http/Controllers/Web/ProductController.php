<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\IdOnlyFormRequest;
use App\Http\Requests\Web\Product\CheckoutFormRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProductController extends Controller
{
    public function productList()
    {
        $result = Product::with('media')->get();

        return self::successResponse('Success', $result);
    }

    public function productInfo($id)
    {

        $product = Product::find($id);

        if(!$product) {
            return self::errorResponse('Product not found');
        }

        $product->load([
            'media',
            'sizes'
        ]);

        return self::successResponse('Success', $product);
    }

    public function checkout(CheckoutFormRequest $request)
    {
        $YOUR_DOMAIN = env('APP_URL', 'http://localhost');

        $item = Product::find($request->id);

        $finalPrice = $request->quantity * $item->price;

        $stripeSecretKey = env('STRIPE_SECRET_KEY');
        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $checkoutSession = DB::transaction(function () use ($item, $YOUR_DOMAIN, $request, $finalPrice) {

            // use this so no need to add item in stripe dashboard manually , but to use this , the image need to be available online (domain)

            // $product = $stripe->products->create(['name' => $item->title]);

            // $price = $stripe->prices->create([
            // 	'product' => $product->id,
            // 	'unit_amount' => $item->price * 100,
            // 	'currency' => 'myr',
            //   ]);

            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                      'price' => 'price_1OD7YnBTqLGxy0Tey8zxgBLK',
                      'quantity' => $request->quantity,
                    ],
                  ],
                'phone_number_collection' => ['enabled' => true],
                'shipping_address_collection' => ['allowed_countries' => ['MY']],
                'custom_text' => [
                    'shipping_address' => [
                      'message' => 'Please note that we can\'t guarantee 2-day delivery for PO boxes at this time.',
                    ],
                  ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success-payment?token={CHECKOUT_SESSION_ID}&product_id='.$item->id.'&quantity='.$request->quantity.'&size='.$request->size,
                'cancel_url' => $YOUR_DOMAIN . '/products/' . $item->id,
              ]);

            //   dd($checkout_session);

            // create a temporary new order with token
            $newOrder = Order::create([
                'final_price' => $finalPrice,
                'order_quantity' => $request->quantity,
                'token' => $checkout_session->id,
                'user_id' =>$request->user_id,
            ]);

            return $checkout_session;

        });

        return self::successResponse('Success', ['checkout_url' => $checkoutSession->url]);

    }

    public function successPayment(Request $request)
    {

        // to be modified to store multiple record when added cart (below)
        $token = $request->query('token');
        $productId = $request->query('product_id');
        $quantity = $request->query('quantity');
        $size = $request->query('size');

        $order = Order::where('token', $token)->first();

        // if matches the token , then proceed to deduct item quantity
        if($order) {

			$order->order_status = 'paid';
			$order->token = null;
			$order->save();

            $product = Product::find($productId);
            $product->orders()->syncWithoutDetaching([$order->id => ['quantity' => $quantity , 'size' => $size]]);


            $productSize = $product->sizes()->where('size', $size)->first();
            if ($productSize) {
                $productSize->quantity -= $quantity;
                $productSize->save();
            }
        } else {
            return redirect('/page-not-found');
        }

        return view('order.success-payment');
    }
}
