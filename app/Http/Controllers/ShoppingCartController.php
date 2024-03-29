<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    public function add(Request $request)
    {
        // 取得要加入購物車的產品資訊
        $product = Product::find($request->id);

        \Cart::add(array(
            'id' => $product->id, // inique row ID
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->qty,
            'attributes' => array(
                'image_url' => $product->image_url,
            )
        ));

        return 'success';
    }

    public function update(Request $request)
    {
        // 從資料庫中取得要更新的產品資料
        $product = Product::find($request->id);

        // 更新購物車
        \Cart::update($product->id, array(
            'quantity' => array(
                'relative' => false,
                'value' => $request->qty
            ),
        ));
        // 取出購物車中該產品資料
        $item = \Cart::get($product->id);

        // 返回該產品目前購物車內的數量
        return $item;
    }

    public function content()
    {
        dd(\Cart::getContent());
    }

    public function clear()
    {
        \Cart::clear();
        return 'clear';
    }

    public function step01()
    {
        $items = \Cart::getContent()->sortBy('id');

        return view('front.shopping-cart.step01', compact('items'));
    }
    public function step02()
    {
        return view('front.shopping-cart.step02');
    }
    public function step02Store(Request $request)
    {
        //payment 0:信用卡付款 1:網路 ATM 2:超商代碼
        //shipment 0:黑貓宅配 1:超商店到店
        session([
            'payment' => $request->payment,
            'shipment' => $request->shipment,
        ]);
        
        dd(session()->all());
        return redirect()->route('shopping-cart.step03');
    }
}
