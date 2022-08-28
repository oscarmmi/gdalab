## GDALAB



## 1. Descargue el proyecto

​		git clone https://gitlab.com/oscarxxi/gdalab.git

## 2. Reemplace el codigo de .env por el que se encuentra en .env.example 

## 3. Cree la base de datos con nombre gdlab como aparece en el script DB.sql de la carpeta sql/ del proyecto.

## 4. Abra una terminal en la carpeta del proyecto actual, gdalab, y ejecute el comando 

​	composer install 

## 5. Abra una terminal en la carpeta del proyecto actual, gdalab, y ejecute el comando 

​	php artisan migrate

## 6. Tras terminar el anterior paso, ejecute el comando 

​	php artisan serve 

