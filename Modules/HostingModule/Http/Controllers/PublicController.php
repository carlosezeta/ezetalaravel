<?php
namespace Modules\HostingModule\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\HostingModule\Entities\Producto;
use Modules\Page\Entities\Page;
use Modules\Shop\Entities\Cart;

class PublicController extends BasePublicController
{
    public function addToCart($id) {
        Log::info('Entramos en PublicController@addToCart');

        $producto = Producto::findOrFail($id);
        if(!$currentUser->cart) {
            $cart = new Cart();
        }
        else {
            $cart = Cart::current();
        }
        $cart->add($producto);

        $productos = Producto::all();

        return \View::make('')

    }

    /**
     * @return \Illuminate\View\View
     */
    public function contratahosting()
    {
        $page = Page::find(2);

        $template = Page::getTemplateForPage($page);

        $productos = Producto::all();

        return view($template, compact(['page', 'productos']));
    }

    public function muestraCarroAjax() {
        $cart = Cart::current();
        $items = $cart->items;
        $respuesta = '';
        foreach ($items as $item) {
            $respuesta .= '<p>$item->name</p>';
        }

        return response()->json(['respuesta' => $respuesta]);
    }
}