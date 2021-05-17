# SII vr 2.0

*Versión del Sistema Integral de Información (SII) de los Institutos Tecnológicos totalmente
desarrollado en Laravel*.

## Comenzando 🚀

_Es necesario migrar primero la base de datos hacia otro manejador; en particular, 
se recomienda PostgreSQL. Sin embargo, el proyecto al estar totalmente desarrollado como PDO 
le permitiría emplear otro tipo._

Dentro del proyecto [BDTEC](https://github.com/rcastrom/bdtec) se encuentra una base
de datos con la estructura en PostgreSQL (sin valores) así como las definiciones de 
tablas y procedimientos que, hasta el momento, cuenta el sistema.

Hasta el momento, los módulos que se han migrado son:
* Servicios Escolares (90%).
* Estudiantes (90%).
* División de Estudios Profesionales (80%).
* Jefaturas Académicas (60%).
* Planeación (40%).
* Coordinación de Verano (90%).
* Desarrollo Académico (1%).

### Pre-requisitos 📋

_Versión mínima de PHP: 7.3 y se recomienda a PostgreSQL como manejador de base de datos, en
cuyo caso, deberá contar con la extensión php7.3_pgsql_

```
* sudo apt install php7.3-pgsql
* sudo service apache2 restart
```

En caso de emplear Ningx (RECOMENDADO), se le recomienda seguir las indicaciones en

```
https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-20-04-es
```
Para posteriormente, habilitar la extensión de pgsql en fpm.

_Adicionalmente, debe contar con composer instalado_
`https://getcomposer.org/download/`

### Instalación 🔧

Desde consola, dirigirse primeramente a /var/www/html/ y crear la carpeta que será 
la del proyecto  (para el ejemplo, se llamará escolares); así entonces

```
sudo mkdir -p escolares
```

Posteriormente, ingresar a dicha carpeta y descargar el proyecto 

```
git clone https://github.com/rcastrom/escolares.git 
```

Una vez descargados los archivos que conforman tanto a Laravel 8.x, así como al proyecto de 
SII. debe actualizar e instalar los componentes necesarios para su ejecución 
(declarados en composer.json); para ello, emplee la instrucción

```
composer update
```

Hecho eso, debe copiarse el archivo ".env.example" como ".env"

```
sudo cp .env.example .env
sudo chown www-data:www-data .env
```

En el archivo recién creado (_.env_) debe indicar los datos necesarios para
su proyecto (tales como URL, usuario y contraseña para la base de datos del proyecto);
por ejemplo

```
APP_ENV=production
APP_DEBUG=false
APP_URL=<indicar la URL que empleará para SII>
DB_CONNECTION=pgsql #Si emplea PostgreSQL como manejador de la BD
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=<su base de datos>
DB_USERNAME=<su usuario>
DB_PASSWORD=<su contraseña>
```

## En caso de emplear Nginx (recomendado)

El mismo sistema de Laravel emite recomendaciones referentes a la configuración que
se recomienda emplear si decide emplear éste sistema; por favor, verifique dicha información
en el siguiente enlace

```
https://laravel.com/docs/8.x/deployment
```

## Despliegue 📦

Esta versión, ha sido creada (_por el momento_) para los siguientes tipos de usuarios 
(también conocidos como "roles"):
* escolares
* alumno
* docente
* verano
* division
* acad
* planeacion

Por lo que, deben crearse los usuarios de acuerdo al tipo de rol que van a emplear; para ello, 
desde _<ruta_proyecto>/database/seeders/_ encontrará el archivo *UserTableSeeder.php*, 
mismo que debe usar para dar de alta a todos los usuarios (incluyendo estudiantes). 

En dicho archivo, encontrará un ejemplo del cómo se debe crear al usuario tomando como ejemplo
un determinado perfil. 

Por último, solamente debe migrar la información hacia la base de datos; para
  ello, desde consola (y estando en la raíz del proyecto; por ejemplo, 
  /var/www/html/escolares/), teclee

```
  php artisan db:seed --class=UserTableSeeder
```

  De encontrarse algún error, el sistema le indicará el dato; caso contrario, el sistema
  estará listo para ser empleado. Posteriormente y por seguridad, se le recomienda
  borrar la información de los usuarios creados.

## Construido con 🛠️

Herramientas empleadas:

* [Laravel](https://laravel.com/) - El framework web usado
* [PostgreSQL](https://www.postgresql.org/) - Manejador de base de datos
* [Bootstrap](https://getbootstrap.com/) - Usado para el CSS
* [Laravel Angular Admin](https://github.com/silverbux/laravel-angular-admin) - Template administrativo

## Autores ✒️

* **Ricardo Castro Méndez** - *Trabajo Inicial* - [rcastrom](https://github.com/rcastrom)
* **Julia Chávez Remigio** - *Colaboradora y revisora* - [jchavez](mailto:jchavez@ite.edu.mx)

## Licencia 📄

Este proyecto está bajo la Licencia (MIT) - mira el archivo [LICENSE.md](LICENSE.md) para 
detalles.

El objetivo del proyecto, es que los institutos tecnológicos que deseen participar con 
observaciones y mejoras, realicen las aportaciones y/o sugerencias necesarias para así 
poder contar con un sistema creado por y para los Tecnológicos.
---
