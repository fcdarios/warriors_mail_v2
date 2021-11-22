#!/bin/bash
# -*- ENCODING: UTF-8 -*-

#################################################
#                WARRIORS LAB'S                 #
#         TODOS LOS DERECHOS RESERVADOS         #
#                                               #
#  Titulo:                                      #
#     Instalación de skin                       #
#  Autor:                                       #
#     Olivares Flores José Darío                #
#  Email:                                       #
#     jose.olivares@warriorslabs.com            #
#  Descripción:                                 #
#     Instalación de skin Warriors Lab's        #
#                                               #
#################################################

#############################################################
# NOTA 1:                                                   #
#   Antes de ejecutar instalar python-deamon                #
#   yum install python-daemon                               #
#-----------------------------------------------------------#
# NOTA 2:                                                   #
#   Ejecutar el script como superusuario o root             #
#-----------------------------------------------------------#
# NOTA 3:                                                   #
#   Despues de ejecutar este script iniciar el demonio.     #
#-----------------------------------------------------------#
# NOTA 4:                                                   #
#   El archivo del demoniowdm.py es ejecutado               #
#   en una versión de python 2, para usar con python3       #
#   revisar la siguiente fuente: https://programmer.group/a-tool-to-implement-python-background-program.html
#-----------------------------------------------------------#
# Comandos:                                                 #
# sh /var/www/html/{nombre_proyecto}/demoniowdm.sh start    #
# sh /var/www/html/{nombre_proyecto}/demoniowdm.sh stopt    #
#############################################################

user_login=$(logname)
# Rutas y nombres de carpetas
path_server="/var/www/html/mailscanner"
name_folder_backup="respaldos_mailscanner"
name_folder_status="status"
path_status=$path_server/$name_folder_status
path_backup="/home/${name_folder_backup}"

#------------------------------------------------------------
# Instalación del skin
#------------------------------------------------------------
echo "[--] - Iniciando..."

if [ ! -d "$path_backup" ]
then
   mkdir $path_backup
   chown -R $user_login:$user_login $path_backup
   chmod 760 -R $path_backup
   echo "[OK] - Se creo la carpeta para los respaldos en /home/${name_folder_backup}"
fi

if [ ! -d "$path_backup/respaldo_inicial" ]
then

   # Si es la primera vez que se ejecuta el script
   # Se crea un respaldo de todos los archivos y se copian los nuevos archivos
   rsync -a $path_server/ $path_backup/respaldo_inicial

   # Con --delete se eliminan los archivos que esten en la ruta de destino
   # y que no se encuentren en la ruta de origen.
   rsync -a --delete ./ $path_server

   # Se restablece el archivo conf.php 
   cp -f $path_backup/respaldo_inicial/conf.php $path_server/conf.php
   if [ -f "$path_status/install.txt" ]
   then
      rm -f $path_status/install.txt
   fi
else 

   # Si ya se a ejecutado el script anteriormente
   # se copian solo los archivos que hayan sido modificados en la ruta de origen (Por fecha de modificación y tamaño) a la ruta de destino
   # y se crea un respaldo de estos archivos que estan en la ruta de destino (Solo los que se modificaron o eliminaron)
   rsync -ab --delete --exclude=conf.php --exclude=.git/ --exclude=status/ --backup-dir=$path_backup/respaldo_$(date +%y%m%d%H%M%S) ./ $path_server
   echo "[OK] - Se ha crado un respaldo de los archivos modificados en la ruta: /home/${name_folder_backup}"
   echo "[OK] - Se ha aplicado correctamente la actualización"
fi
#------------------------------------------------------------


#------------------------------------------------------------
# Carpeta status
#------------------------------------------------------------
if [ ! -f "$path_status/install.txt" ]
then
   
   # Verificar si la carpeta status existe
   if [ -d "$path_status" ]
   then
      echo "[OK] - Reiniciando archivos de la carpeta $name_folder_status..."
      rm -rf $path_status/
   fi

   # Crear la carpeta y archivos y darles permisos
   # - wdm.txt 
   # - daemon.txt
   # - wdm_status.txt 
   mkdir $path_status
   touch $path_status/wdm.txt
   touch $path_status/daemon.txt
   touch $path_status/wdm_status.txt
   chmod 766 -R $path_status/*
fi
#------------------------------------------------------------


#------------------------------------------------------------
# Archivos para el funcionamiento del demonio
#------------------------------------------------------------
# Verificar que existe la ruta /etc/MailScanner
if [ -d "/etc/MailScanner" ]
then

   if [ ! -f "$path_status/install.txt" ]
   then
  
      # Crear el archivo mcp.cf y darle permisos
      if [ -d "/etc/MailScanner/mcp/" ]
      then 
         echo "[OK] - Creando el archivo mcp.cf..."
         touch /etc/MailScanner/mcp/mcp.cf
         chmod 666 /etc/MailScanner/mcp/mcp.cf
         echo "[OK] - El archivo mcp.cf se creo correctamente"
      else
         echo "[ERROR] - No existe la carpeta /etc/MailScanner/mcp"
      fi

      # Crear la carpeta domain y cambiarla de propietario
      # Verefica que la carpeta existe
      echo "[OK] - Creando la carpeta domain..."
      if [ -d "/etc/MailScanner/domain" ]
      then
         # Si existe, confirmar que pueden ser ejecutados 
         chown apache:apache /etc/MailScanner/domain
      else
         # Si no existe, crear la carpeta y cambiarla de propietario
         mkdir /etc/MailScanner/domain
         chown apache:apache /etc/MailScanner/domain
      fi
      echo "[OK] - La carpeta domain se creo correctamente."


      if [ -f "/etc/MailScanner/filename.rules.conf" ]
      then
         # Copiar el archivo filename.rules.conf y darle permisos
         cp /etc/MailScanner/filename.rules.conf /etc/MailScanner/domain/
         chmod 666 /etc/MailScanner/domain/filename.rules.conf
      else
         echo "[ERROR] - No existe el archivo /etc/MailScanner/filename.rules.conf."
      fi
      
      if [ -f "/etc/MailScanner/filetype.rules" ]
      then
         # Dar permisos al archivo filetype.rules
         chmod 666 /etc/MailScanner/filetype.rules
      else
         echo "[ERROR] - No existe el archivo /etc/MailScanner/filetype.rules."
      fi

      if [ -f "/etc/postfix/header_checks" ]
      then
         # Dar permisos al archivo header_checks
         chmod 666 /etc/postfix/header_checks
      else
         echo "[ERROR] - No existe el archivo /etc/postfix/header_checks."
      fi

   fi
else
   echo "[WARNING] - No existe la carpeta /etc/MailScanner."
fi
#------------------------------------------------------------
if [ ! -f "$path_status/install.txt" ]
then
   touch $path_status/install.txt
fi
echo "[OK] - Instalación finalizada."
