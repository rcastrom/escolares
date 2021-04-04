# SII vr 2.0

*Versión del Sistema Integral de Información (SII) de los Institutos Tecnológicos totalmente
desarrollado en Laravel*.


## Comenzando 🚀

_Es necesario migrar primero la base de datos de Sybase hacia otro manejador; en particular, 
se recomienda PostgreSQL. Sin embargo, el proyecto al estar totalmente desarrollado como PDO 
le permitiría emplear otra base._

Dentro del proyecto **POSTGRE** se encuentra una base en PostgreSQL (sin valores pero sí con la estructura) así
como las definiciones de funciones que, hasta el momento, cuenta el sistema.

Hasta el momento, los módulos que se han migrado son:
* Servicios Escolares (70%).
* Estudiantes (70%).
* División de Estudios Profesionales (80%).
* Jefaturas Académicas (60%).
* Planeación (40%).
* Coordinación de Verano (90%).

### Pre-requisitos 📋

_Versión mínima de PHP: 7.3 y se recomienda a PostgreSQL como manejador de base de datos, en
cuyo caso, deberá contar con la extensión php7.3_pgsql_

```
* sudo apt install php7.0-pgsql
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
Desde consola, dirigirse primeramente a /var/www/html y crear la carpeta que será la del proyecto 
(para el ejemplo, se llamará escolares); así entonces
```
sudo mkdir -p escolares
```
Primero, liberará la carpeta para que pueda ser escribible como el usuario de 
```
git clone https://github.com/rcastrom/escolares.git 
```
_Una vez dado Enter, se habrá creado una carpeta llamada "escolares" misma que primero debe ingresar a la misma
para actualizar e instalar los componentes necesarios; para ello, teclear_

```
composer update
```

_Finaliza con un ejemplo de cómo obtener datos del sistema o como usarlos para una pequeña demo_

## Despliegue 📦

_Agrega notas adicionales sobre como hacer deploy_

## Construido con 🛠️

_Herramientas empleadas:_

* [Laravel](https://laravel.com/) - El framework web usado
* [PostgreSQL](https://www.postgresql.org/) - Manejador de base de datos
* [Bootstrap](https://getbootstrap.com/) - Usado para el CSS


## Autores ✒️

* **Ricardo Castro Méndez** - *Trabajo Inicial* - [rcastrom](https://github.com/rcastrom)
* **Julia Chávez Remigio** - *Colaboradora y revisora* - [jchavez](mailto:jchavez@ite.edu.mx)

## Licencia 📄

Este proyecto está bajo la Licencia (MIT) - mira el archivo [LICENSE.md](LICENSE.md) para detalles

---
