#!/bin/bash

#Carpeta donde se localizara el script en bash 

# /etc/init.d/test 

case "$1" in
   start)
      #inicia el demonio
      echo "Starting demoniowdm.py"
      python /var/www/html/mailscanner/demoniowdm.py start ##Cambia la ruta por la localizacion del demonio
      ;;

   stop)
      #detiene el demonio
      echo "Stopping demoniowdm.py"
      python /var/www/html/mailscanner/demoniowdm.py stop ##Cambia la ruta por la localizacion del demonio
      ;;

   restart)
      #reinicia el demonio
   echo "Restarting demoniowdm.py"
      python /var/www/html/mailscanner/demoniowdm.py restart ##Cambia la ruta por la localizacion del demonio
      ;;

   *) #en caso de no encontrar ninguna de las anteriores 
      echo "sintaxis correcta: /etc/init.d/demonio.sh {start|stop|restart}" #cambia la ruta segin el WDM 
      exit 1
      ;;
esac #instrccion default 
exit 0
