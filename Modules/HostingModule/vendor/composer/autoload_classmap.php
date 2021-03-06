<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Modules\\HostingModule\\Entities\\Cuenta' => $baseDir . '/Entities/Cuenta.php',
    'Modules\\HostingModule\\Entities\\CuentaTranslation' => $baseDir . '/Entities/CuentaTranslation.php',
    'Modules\\HostingModule\\Entities\\Producto' => $baseDir . '/Entities/Producto.php',
    'Modules\\HostingModule\\Entities\\ProductoTranslation' => $baseDir . '/Entities/ProductoTranslation.php',
    'Modules\\HostingModule\\Http\\Controllers\\Admin\\CuentaController' => $baseDir . '/Http/Controllers/Admin/CuentaController.php',
    'Modules\\HostingModule\\Http\\Controllers\\Admin\\ProductoController' => $baseDir . '/Http/Controllers/Admin/ProductoController.php',
    'Modules\\HostingModule\\Http\\Controllers\\PublicController' => $baseDir . '/Http/Controllers/PublicController.php',
    'Modules\\HostingModule\\Http\\Requests\\CreateCuentaRequest' => $baseDir . '/Http/Requests/CreateCuentaRequest.php',
    'Modules\\HostingModule\\Http\\Requests\\CreateProductoRequest' => $baseDir . '/Http/Requests/CreateProductoRequest.php',
    'Modules\\HostingModule\\Http\\Requests\\UpdateCuentaRequest' => $baseDir . '/Http/Requests/UpdateCuentaRequest.php',
    'Modules\\HostingModule\\Http\\Requests\\UpdateProductoRequest' => $baseDir . '/Http/Requests/UpdateProductoRequest.php',
    'Modules\\HostingModule\\Presenters\\ProductoPresenter' => $baseDir . '/Presenters/ProductoPresenter.php',
    'Modules\\HostingModule\\Providers\\HostingModuleServiceProvider' => $baseDir . '/Providers/HostingModuleServiceProvider.php',
    'Modules\\HostingModule\\Providers\\RouteServiceProvider' => $baseDir . '/Providers/RouteServiceProvider.php',
    'Modules\\HostingModule\\Repositories\\Cache\\CacheCuentaDecorator' => $baseDir . '/Repositories/Cache/CacheCuentaDecorator.php',
    'Modules\\HostingModule\\Repositories\\Cache\\CacheProductoDecorator' => $baseDir . '/Repositories/Cache/CacheProductoDecorator.php',
    'Modules\\HostingModule\\Repositories\\CuentaRepository' => $baseDir . '/Repositories/CuentaRepository.php',
    'Modules\\HostingModule\\Repositories\\Eloquent\\EloquentCuentaRepository' => $baseDir . '/Repositories/Eloquent/EloquentCuentaRepository.php',
    'Modules\\HostingModule\\Repositories\\Eloquent\\EloquentProductoRepository' => $baseDir . '/Repositories/Eloquent/EloquentProductoRepository.php',
    'Modules\\HostingModule\\Repositories\\ProductoRepository' => $baseDir . '/Repositories/ProductoRepository.php',
    'Modules\\HostingModule\\Sidebar\\SidebarExtender' => $baseDir . '/Sidebar/SidebarExtender.php',
    'Modules\\HostingModule\\ValueObjects\\Prueba' => $baseDir . '/ValueObjects/Prueba.php',
);
