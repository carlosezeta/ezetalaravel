<?php namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\HostingModule\Entities\Cuenta;
use Modules\HostingModule\Entities\Producto;
use Modules\Shop\Entities\Carro;
use Modules\Shop\Entities\Item;
use Modules\Shop\Http\Requests\ItemDeleteRequest;
use Modules\Shop\Http\Requests\ShopHostingRequest;
use Modules\Shop\Repositories\CartRepository;
use Modules\Shop\Repositories\OrderRepository;
use Modules\Shop\Repositories\TransactionRepository;
use Gloudemans\Shoppingcart\Facades\Cart;

class PublicController extends BasePublicController
{
	/**
	 * @var CartRepository
	 */
	private $cart;
	/**
	 * @var OrderRepository
	 */
	private $order;
	/**
	 * @var TransactionRepository
	 */
	private $transaction;
	/**
	 * @var Application
	 */
	private $app;

	public function __construct(CartRepository $carro, Application $app)
	{
		parent::__construct();
		$this->carro = $carro;
		$this->app = $app;
	}

	/**
	 * @param $id
	 * @return \Illuminate\View\View
	 */
	public function getHosting($id)
	{
		$hosting = Producto::find($id);
		$productos = Producto::all();

		$this->throw404IfNotFound($hosting);

		$template = 'cart';
        $data=[
            'domain' => null,
            'tld' => null,
            'domainoption' => null
        ];

		return view($template, compact(['hosting', 'productos', 'data']));
	}

    /**
     * @param $id
     * @return \Illuminate\View\View
     */
    public function postHosting($id, ShopHostingRequest $request)
    {
        $hosting = Producto::find($id);
        $this->throw404IfNotFound($hosting);
        $user = $this->auth->check();
        $productos = Producto::all();

        $data['domainoption'] = $request->input('domainoption');

        if ($data['domainoption'] == 'subdomain') {
            $data['domain'] = $request->input('domain') . '.ezetahosting.com';
            $data['tld'] = '';
        } else{
            $domain = explode('.', $request->input('domain'));
            if (!isset($domain[1])){
                $data['domain'] = $request->input('domain');
                $data['tld'] = $request->input('tld');
            } else {
                $data['domain'] = $domain[0];
                $data['tld'] = $domain[1];
            }
        }

        if ($data['domainoption'] == 'subdomain') {
            $cuenta = Cuenta::where(['domain' => $data["domain"].'.ezetahosting.com'])->first();
            if (isset($cuenta)) {
                //TODO: hacer este mensaje de error traducible.
                flash()->error('El subdominio elegido está en uso. Por favor, selecciona otra opción.');

                $template = 'cart';
                return view($template, compact(['hosting', 'productos', 'data']));
            }
        } elseif ($data['domainoption'] == 'register') {
            $respuesta = $this->compruebaDominio($data['domain'], $data['tld']);
            if ($respuesta['status'] != "available"){
                //TODO: hacer este mensaje de error traducible.
                flash()->error('El dominio indicado ya está registrado. Intente buscar otro dominio.');
                $template = 'cart';
                return view($template, compact(['hosting', 'productos', 'data']));
            } else {
                flash()->success('El dominio '.$data['domain'].'.'.$data['tld'].' está libre.');
            }
        } elseif ($data['domainoption'] == 'transfer') {
            // Por ahora, almacenamos el pedido y lo gestionamos manualmente.
            $respuesta = $this->compruebaDominio($data['domain'], $data['tld']);
            if ($respuesta['status'] == "available"){
                flash()->error('El dominio indicado no está registrado. Compruebe que lo haya escrito correctamente.');
                $template = 'cart';
                return view($template, compact(['hosting', 'productos', 'data']));
            }
        }


        if ($user) {
            $carro = Carro::where('user_id', '=', $user->id)->first();
            if (empty($carro)) {
                Log::info('Dentro del if del user - 1. El valor de $user es: ' . $user);
                $carro = new Carro();
                $carro->user_id = $user->id;
                $carro->save();
            }

            // Añadimos el hosting al carro de la base de datos:
            $carro->add(['user_id' => $user->id, 'id' => $hosting->sku, 'name' => $hosting->name, 'qty' => 1, 'price' => $hosting->price * 10, 'tax' => $hosting->price * 10 * 0.21, 'domain' => $data['domain'] . '.' . $data['tld']]);

            // Añadimos el dominio al carro de la base de datos:
            if ($data['domainoption'] == 'register' || $data['domainoption'] == 'transfer') {
                //TODO: Buscar el precio en la base de datos (primero hay que introducirlo)
                $carro->add(['user_id' => $user->id, 'id' => $data['domainoption'] . $data['tld'], 'name' => $data['domainoption'] . ': ' . $data['domain'] . '.' . $data['tld'], 'qty' => 1, 'price' => 12.25, 'tax' => 12.25 * 0.21]);
            } else {
                $carro->add(['user_id' => $user->id, 'id' => 'subdominio', 'name' => $data['domainoption'] . ': ' . $data['domain'] . '.' . $data['tld'], 'qty' => 1, 'price' => 0, 'tax' => 0]);
            }
        }

        Cart::add([
            'id' => $hosting->sku,
            'name' => $hosting->name,
            'qty' => 1,
            'price' => $hosting->price*10,
            'options' => [
                'tax' => $hosting->price * 10 * 0.21,
                'domain' => $data['domain'] . '.' . $data['tld'],
            ]
        ]);

        if ($data['domainoption'] <> 'owndomain') {
            if ($data['domainoption'] <> 'subdomain') {
                Cart::add([
                    'id' => $data['domainoption'].$data['tld'],
                    'name' => $data['domainoption'].': '.$data['domain'].'.'.$data['tld'],
                    'qty' => 1,
                    'price' => $hosting->price*10,
                    'options' => [
                        'tax' => $hosting->price * 10 * 0.21,
                        'domain' => $data['domain'] . '.' . $data['tld'],
                    ]
                ]);
            } else {
                Cart::add([
                    'id' => 'subdominio',
                    'name' => $data['domainoption'].': '.$data['domain'].'.'.$data['tld'],
                    'qty' => 1,
                    'price' => 0,
                    'options' => [
                        'tax' => 0,
                        'domain' => $data['domain'] . '.' . $data['tld'],
                    ]
                ]);
            }
        }

        return redirect()->route('getCarro');
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function getCarro()
    {
        if (Cart::count() == 0) {
            $user = $this->auth->check();
            if ($user) {
                $carro = Carro::with('items')->where('user_id','=',$user->id)->first();
                if (!empty($carro)){
                    foreach ($carro->items as $item) {
                        Cart::add([
                            'id' => $item->sku,
                            'name' => $item->name,
                            'qty' => $item->quantity,
                            'price' => $item->price,
                            'options' => [
                                'tax' => $item->tax,
                                'domain' => $item->domain,
                            ]
                        ]);
                    }
                } else { return redirect('/'); }
            } else { return redirect('/'); }
        }

        $template = 'carro';
        return view($template);
    }


    /**
     *
     * @return \Illuminate\View\View
     */
    public function checkout()
    {
        $user = $this->auth->check();
        if (!$user) {
            $template = 'loginregistro';
            return view($template);
        }
        $carro = Carro::with('items')->where('user_id','=',$user->id)->first();
        if (!$carro->$items) {
            flash()->error('No se ha encontrado ningún producto en la cesta.');
            return redirect('/');
        }

        $template = 'pago';
        return view($template);
    }


	/**
	 * Throw a 404 error page if the given page is not found
	 * @param $page
	 */
	private function throw404IfNotFound($page)
	{
		if (is_null($page)) {
			$this->app->abort('404');
		}
	}

    private function compruebaDominio($domain, $tld)
    {
        //TODO: Hay que colocar esta función en el módulo Dominio.
        // Parámetros DEMO Reseller Club
        $RCAPIURL = config('domain.rcapiurl');
        $RCAPIKEY = config('domain.rcapikey');
        $RCUserId = config('domain.rcuserid');

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, $RCAPIURL.'/api/domains/available.json?auth-userid='.$RCUserId.'&api-key='.$RCAPIKEY.'&domain-name='.$data["domain"].'&tlds=com&suggest-alternative=true');
        curl_setopt($ch, CURLOPT_URL, $RCAPIURL.'/api/domains/available.json?auth-userid='.$RCUserId.'&api-key='.$RCAPIKEY.'&domain-name='.$domain.'&tlds='.$tld);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //$respuesta = array_values(json_decode(curl_exec($ch), true))[0];
        //TODO: deberíamos devolver $respuesta, pero parece que no funciona...
        return array('status' => 'available');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(ItemDeleteRequest $request, $id) {

        if ($request->ajax()) {
            $rowId = Cart::search(array('id' => $id));
            Cart::remove($rowId[0]);
            $user = $this->auth->check();
            if ($user) {
                Item::where('user_id', '=', $user->id)->where('sku', '=', $id)->delete();
                $items = Item::where('user_id', '=', $user->id)->count();
                if (!$items) {
                    Carro::where('user_id','=',$user->id)->delete();
                }
            }
            $vacio = false;
            if (Cart::count() == 0) {
                $vacio = true;
            }
            Log::info(Cart::count());
            Log::info($vacio);
            $subtotal = number_format(Cart::total(),2,',','.');
            $iva = number_format($subtotal*0.21,2,',','.');
            $total = number_format($subtotal*1.21,2,',','.');
            return response()->json(['subtotal' => $subtotal, 'iva' => $iva, 'importetotal' => $total, 'vacio' => $vacio]);
        }
    }


}
