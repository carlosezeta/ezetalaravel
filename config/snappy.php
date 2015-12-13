<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => base_path('../wkhtmltopdf/bin/wkhtmltopdf.exe'),
        'timeout' => false,
        'options' => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => base_path('../wkhtmltopdf/bin/wkhtmltoimage.exe'),
        'timeout' => false,
        'options' => array(),
    ),


);
