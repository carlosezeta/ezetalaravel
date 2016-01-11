<?php namespace Modules\Shop\Http\Controllers;

use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Dominio\Entities\Dominio;
use Modules\HostingModule\Entities\Cuenta;

class PrivateController extends BasePublicController
{
    public function home() {
        $user = $this->auth->check();
        if ($user) {
            $cuentas = Cuenta::where('user_id', '=', $user->id)->get();
            $dominios = Dominio::where('user_id', '=', $user->id)->get();
			return view('shop.private.home', compact(['cuentas', 'dominios']));
		} else {
            // Store the current uri in the session
            \Session::put('url.intended', \Request::url());

            return redirect()->route('login');
		}
    }

    public function hosting() {
        return 'PrivateController@hosting';
    }

    public function dominios() {
        return 'PrivateController@dominios';
    }
}