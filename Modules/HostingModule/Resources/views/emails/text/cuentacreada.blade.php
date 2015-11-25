Estimado {{ $user->first_name }}, le informamos de que la cuenta de hosting para {{ $cuenta->domain }} ha sido creada.

Los datos de acceso son los siguientes:

Usuario:    {{ $cuenta->username }}
Contraseña: {{ $cuenta->password }}

Puede acceder con esos datos a los siguientes servicios:

CPanel:  cpanel.{{ $cuenta->domain }}
FTP:     ftp.{{ $cuenta->domain }}
webmail: webmail.{{ $cuenta->domain }}


No dude en ponerse en contacto con nuestro equipo si necesita hacer cualquier consulta.

Gracias por confiar en nosotros,

Equipo de ezetahosting.com.