<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura {{ $invoice }}</title>
    <!-- Bootstrap CSS -->
    {!! Theme::style('css/bootstrap-facturas.css') !!}
    <!-- Font Awesome -->
    {!! Theme::style('css/theme-style.css') !!}
    <!--Your custom colour override-->
    {!! Theme::style('css/colour-red.css') !!}
    <!-- Your custom override -->
    {!! Theme::style('css/custom-style.css') !!}

    <style>
        h1{text-align: left;}
        .slogan{margin-left: 20px;}
        .factura-ribbon-pagada {
            font-size: 1.2em;
            font-weight: bold;
            position: fixed;
            z-index: 100000;
            top: 0px;
            right:0px;
            width: 300px;
            text-align: center;
            -moz-transform-origin: 50% 50%;
            -moz-transform: translate(70px,50px) rotate(45deg);
            -webkit-transform-origin: 50% 50%;
            -webkit-transform: translate(70px,50px) rotate(45deg);
            -o-transform-origin: 50% 50%;
            -o-transform: translate(70px,50px) rotate(45deg);
            transform-origin: 50% 50%;
            transform: translate(70px,50px) rotate(45deg);
            color: white;
            border: 10px white double;
            border-image: none;
            padding: 10px;
            background-color: rgba(76, 182, 47, 1);
        }
        .factura-ribbon-nopagada {
            font-size: 1.2em;
            font-weight: bold;
            position: fixed;
            z-index: 100000;
            top: 0px;
            right:0px;
            width: 300px;
            text-align: center;
            -moz-transform-origin: 50% 50%;
            -moz-transform: translate(70px,50px) rotate(45deg);
            -webkit-transform-origin: 50% 50%;
            -webkit-transform: translate(70px,50px) rotate(45deg);
            -o-transform-origin: 50% 50%;
            -o-transform: translate(70px,50px) rotate(45deg);
            transform-origin: 50% 50%;
            transform: translate(70px,50px) rotate(45deg);
            color: white;
            border: 10px white;
            border-image: none;
            padding: 10px;
            background-color: rgba(229, 87, 87, 1);
        }
        .banda-superior{
            top: 50px;
            font-size: 1.5em;
            padding: 6px 20px;
            color: #f4f4f4;
            background-color: #be3e1d;
        }
        .datos-de, .datos-para {
            width: 300px;
        }
        .datos-espacio {
            width: 103px;
        }
        .panel {
            margin-top: 40px;
            'border: 1px #be3e1d solid;
        }
        .panel-heading {
            'border-bottom: 1px #be3e1d solid;
            'background-color: #DC9380;
        }
        body {
            margin: 1cm;
        }
        .espacio-blanco {
            height: 1cm;
        }
        .totales {
            line-height: 3em;
        }
    </style>
</head>
<body>
        <div class="espacio-blanco"></div>
        <!--Header upper region-->
        <div class="banda-superior">
            Factura #{{ $invoice }}
        </div>

        <!--Header & Branding region-->
        <div class="header" data-toggle="clingify">
            <div class="header-inner container">
                <li class="navbar">
                    <div class="pull-left">
                        <!--branding/logo-->
                        <a class="navbar-brand" href="{{ URL::to('/') }}" title="Home">
                            <h1>
                                <span>ezeta</span>hosting<span>.</span>
                            </h1>
                        </a>
                        <div class="slogan">Soluciones web para PYMEs</div>
                    </div>
                </li>
            </div>
        </div>



<div class="factura-ribbon-pagada">
    Pagada
</div>

    <div class="container">

        <div class="row">
            <div class="col-xs-5">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>De: <a href="#">ezetahosting.com</a></h4>
                    </div>
                    <div class="panel-body">C/Marquès de Caldes de Montbui <br>
                        87-89, 1º, 3ª<br>
                        17003 - Girona (España)</div>
                </div>
            </div>
            <div class="col-xs-5 col-xs-offset-2 text-right">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Para : <a href="#">CGT AGE</a></h4>
                    </div>
                    <div class="panel-body">C/Alenza 13<br>
                        28003 - Madrid (España)</div>
                </div>
            </div>

        </div>
        <!-- / fin de sección de datos del Cliente  -->

        <div class="clearfix"></div>

        <table class="table table-bordered">
            <tr>
                <th>
                    <h4>Servicio / Producto</h4>
                </th>
                <!--
                <th>
                    <h4>Descripción</h4>
                </th>

                <th>
                    <h4>Hrs / Cantidad</h4>
                </th>
                -->
                <th>
                    <h4>Precio</h4>
                </th>
                <!--
                <th>
                    <h4>Sub-Total</h4>
                </th>
                -->
            </tr>
            @foreach($items as $item)
            <tr>
                <td>
                    <a href="#">
                    {{ $item->name }}
                    @if(!empty($item->domain))
                        ({{ $item->domain }})
                    @endif
                    </a>
                </td>
                <td class=" text-right ">{{ number_format($item->price,2,',','.') }}</td>
            </tr>
            @endforeach

        </table>

        <div class="row text-right totales">
            <div class="col-xs-3 col-xs-offset-7"><strong>
                    Subtotal:
                    <br>
                    IVA 21%:
                    <br>
                    Total:
                </strong></div>
            <div class="col-xs-2"><strong>
                    {{ number_format($order->totalPrice,2,',','.') }} €
                    <br>
                    {{ number_format($order->totalTax,2,',','.') }} €
                    <br>
                    {{ number_format($order->total,2,',','.') }} €
                </strong></div>
        </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class = "col-xs-5">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4>Datos del pago</h4>
                    </div>
                    <div class="panel-body">
                        Transferencia bancaria
                        <br>
                        Entidad: CaixaBank
                        <br>
                        IBAN: ES24 1234 5678 1234 5678 1234
                        <br>
                        BIC: CAIXESBBXXX
                    </div>
                </div>
            </div>

            <div class="col-xs-7">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4> Datos de contacto </h4>
                    </div>
                    <div class=" panel-body ">
                        Email: ezeta@ezetahosting.com
                        <br>
                        Móvil: +34 665 96 60 50
                        <br>
                        Twitter: <a href="https://twitter.com/ezetahosting">@ezetahosting</a> | web: <a href="http://www.ezetahosting.com">http://www.ezetahosting.com/</a>
                        <br>
                        <h5><small> El pago debe ser por transferencia bancaria</small></h5>
                    </div>
                </div>
            </div>
        </div>

    </div><!--container-->
</body>
</html>