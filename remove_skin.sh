#!/bin/bash
# -*- ENCODING: UTF-8 -*-

#################################################
#                WARRIORS LAB'S                 #
#         TODOS LOS DERECHOS RESERVADOS         #
#                                               #
#  Titulo:                                      #
#     Revertir skin WDM                         #
#  Autor:                                       #
#     Olivares Flores José Darío                #
#     -                                         #
#  Email:                                       #
#     jose.olivares@warriorslabs.com            #
#  Descripción:                                 #
#     Revierte el skin de Warriors Lab's al     #
#     diseño original del proyecto              #
#                                               #
#################################################


# Rutas y nombres de carpetas
user_login=$(logname)
path_server="/var/www/html/mailscanner"
name_folder_backup="respaldos_mailscanner"
path_backup="/home/${name_folder_backup}"

#------------------------------------------------------------
# Revertir el skin
#------------------------------------------------------------
echo "[--] - Iniciando..."

if [ -d "$path_server" ]
then

   if [ -d "$path_backup/respaldo_inicial" ]
   then

      rm -rf $path_server/
      cp -rf $path_backup/respaldo_inicial $path_server
      echo "[OK] - Se ha restaurado el skin original"

   else 

      echo "[ERROR] - No se encontro el respaldo inicial"

   fi

else 

   echo "[ERROR] - La ubicación del proyecto del servidor es incorrecta"

fi





