<?php namespace Modules\Shop\Http\Controllers;

use Helge\Client\SimpleWhoisClient;
use Helge\Loader\JsonLoader;
use Helge\Service\DomainAvailability;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\HostingModule\Entities\Cuenta;
use Modules\HostingModule\Entities\Producto;
use Modules\Shop\Entities\Carro;
use Modules\Shop\Entities\Item;
use Modules\Shop\Entities\Order;
use Modules\Shop\Entities\Transaction;
use Modules\Shop\Http\Requests\EnviarPagoRequest;
use Modules\Shop\Http\Requests\ItemDeleteRequest;
use Modules\Shop\Http\Requests\ShopHostingRequest;
use Modules\Shop\Repositories\CartRepository;
use Modules\Shop\Repositories\OrderRepository;
use Modules\Shop\Repositories\TransactionRepository;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\User\Entities\Sentinel\User;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Error\Card;
use Stripe\Stripe;

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
        Log::info('Entramos en postHosting');
        $hosting = Producto::find($id);
        $this->throw404IfNotFound($hosting);
        $user = $this->auth->check();

        $data['domainoption'] = $request->input('domainoption');

        Log::info($data['domainoption']);

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
            Log::info('El usuario estaba logueado');
            $carro = Carro::where('user_id', '=', $user->id)->first();
            if (!$carro) {
                $carro = Carro::withTrashed()->where('user_id', '=', $user->id)->first();
                if (!$carro) {
                    $carro = new Carro();
                    $carro->user_id = $user->id;
                    $carro->save();
                } else {
                    $carro->restore();
                }
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
        } else {
            Log::info('El usuario no estaba logueado');
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
                    'price' => 12.25,
                    'options' => [
                        'tax' => 12.25 * 0.21,
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
        $carro = Carro::where('user_id','=',$user->id)->first();
        if ($carro) {
            Log::info('Hay carro');
            $items = Item::where('cart_id', '=', $carro->id)->get();
            if (!$items) {
                Log::info('No hay items');
                // Si el carro está vacío (no debería haber carro, pero por si acaso...
                $carro->destroy();
                flash()->error('No se ha encontrado ningún producto en la cesta.');
                return redirect('/');
            }
            Log::info('Hay items');
        } else {
            Log::info('No hay carro');
            // Si no hay carro
            flash()->error('No se ha encontrado ningún producto en la cesta.');
            return redirect('/');
        }
        Log::info('Hay carro y hay items:');
        Log::info($carro);
        Log::info($items);
        // Aquí creamos el pedido y eliminamos el carro.

        $order = new Order();
        $order->user_id = $user->id;
        $order->totalPrice = Cart::total();
        $order->totalTax = Cart::total()*0.21;
        $order->total = Cart::total()*1.21;
        $order->statusCode = 'in_creation';
        $order->save();

        Log::info('Pedido creado');
        Log::info($order);

        Log::info('Recorremos items:');
        $numeroDeItem = 1;
        foreach ($items as $item) {
            Log::info('Item: '.$numeroDeItem. ' valor al entrar:');
            Log::info($item);
            $item->cart_id = null;
            $item->order_id = $order->id;
            $item->save();
            Log::info('Valor al salir:');
            Log::info($item);
            $numeroDeItem +=1;
        }

        Log::info('Hemos terminado con los items. El carro es:');
        Log::info($carro);
        $carro->delete();
        Cart::destroy();


        $template = 'pago';
        return view($template, compact(['items', 'order']));
    }


    /**
     *
     * @return \Illuminate\View\View
     */
    public function getPagar()
    {
        $user = $this->auth->check();
        if (!$user) {
            return redirect()->route('login');
        }
        $order = Order::where('user_id','=',$user->id)
                        ->where(function ($query) {
                            $query->where('statusCode', '=', 'in_creation')
                                  ->orWhere('statusCode', '=', 'pending');

                        })
                        ->orderBy('created_at', 'desc')
                        ->first();

        if (!$order) {
            flash()->error('No hay ningún pedido');
            return redirect('/');
        }

        $items = Item::where('order_id', '=', $order->id)->get();


        $template = 'pago';
        return view($template, compact(['items', 'order']));
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function postPagar(EnviarPagoRequest $request)
    {
        $order = Order::find($request->input('order_id'));
        $amount = $order->total*100;
        $token = $request->input('stripeToken');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $emailCheck = User::where('email', $email)->value('email');

        Stripe::setApiKey(env('STRIPE_SK'));

        // If the email doesn't exist in the database create new customer and user record
        if (!isset($emailCheck)) {
            // Create a new Stripe customer
            try {
                $customer = Customer::create([
                    'source' => $token,
                    'email' => $email,
                    'metadata' => [
                        "First Name" => $first_name,
                        "Last Name" => $last_name
                    ]
                ]);
            } catch (Card $e) {
                return redirect()->route('order')
                    ->withErrors($e->getMessage())
                    ->withInput();
            }

            $customerID = $customer->id;

            // Create a new user in the database with Stripe
            $user = User::create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'stripe_customer_id' => $customerID,
            ]);
        } else {
            $customerID = User::where('email', $email)->value('stripe_customer_id');
            $user = User::where('email', $email)->first();
        }

        $user = $this->auth->check();

        $customerID = $user->stripe_customer_id;
        // Charging the Customer with the selected amount
        try {
            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'eur',
                'customer' => $customerID,
                'metadata' => [
                    'order_id' => $order->id
                ]
            ]);
        } catch (Card $e) {
            return redirect()->route('getPagar')
                ->withErrors($e->getMessage())
                ->withInput();
        }

        // Create transaction record in the database
        Transaction::create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'token' => $token,
            'transaction_id' => $charge->id,
        ]);

        //TODO: Reenviamos a una página de confirmación de la compra
        $template = 'pagocorrecto';
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
            $sub = Cart::total();
            $subtotal = number_format($sub,2,',','.');
            $iva = number_format($sub*0.21,2,',','.');
            $total = number_format($sub*1.21,2,',','.');
            return response()->json(['subtotal' => $subtotal, 'iva' => $iva, 'importetotal' => $total, 'vacio' => $vacio]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comprobarDominio(ItemDeleteRequest $request, $dominio) {

        if ($request->ajax()) {

            $whoisClient = new SimpleWhoisClient();
            Log::info(config('helgedomaintools.path'));
            $dataLoader = new JsonLoader(config('helgedomaintools.path'));
            $service = new DomainAvailability($whoisClient, $dataLoader);

            if ($service->isAvailable($dominio)) {
                $disponible = true;
            } else {
                $disponible = false;
            }

            return response()->json(['disponible' => $disponible]);

        }
    }


}
