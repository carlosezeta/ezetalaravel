<?php namespace Modules\Shop\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Modules\Shop\Entities\Item;
use Modules\Shop\Entities\Order;
use Pingpong\Modules\Routing\Controller;

class FacturasController extends Controller {
	
	public function index()
	{
		return view('shop::index');
	}

	public function invoice()
	{
		//$data = $this->getData();
		$date = date('d/m/Y');
		$invoice = "2015/0403";

		$order = Order::find(3);
		$items = Item::where('user_id', '=', 1)->get();


		$pdf = SnappyPdf::loadView('shop::pdf.invoice', compact('date', 'invoice', 'order', 'items'));
		$pdf->setOption('margin-top', 0);
		$pdf->setOption('margin-left', 0);
		$pdf->setOption('margin-right', 0);
		return $pdf->stream('factura'.$invoice.'.pdf');
	}

	public function getData()
	{
		$data =  [
				'quantity'      => '1' ,
				'description'   => 'some ramdom text',
				'price'   => '500',
				'total'     => '500'
		];
		return $data;
	}

}