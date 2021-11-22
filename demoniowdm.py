#!/usr/bin/env python
# -*- coding: utf-8 -*-

import logging
import time
import subprocess 
from daemon import runner #python-daemon
import os                 #instrucciones de consola

class App():
   def __init__(self):
      self.stdin_path      = '/dev/null' #entrada estandar
      self.stdout_path     = '/dev/tty'  #salida estandar
      self.stderr_path     = '/dev/tty'
      self.pidfile_path    =  '/var/run/test.pid' #ruta del pid que le pertenece al demonio
      self.pidfile_timeout = 5

      # Rutas de los archivos
      self.path = '/var/www/html/mailscanner/status/'
      self.path_status = self.path + 'wdm_status.txt'
      self.path_daemon = self.path + 'daemon.txt'
      self.path_wdm = self.path + 'wdm.txt'

   def run(self): #codigo a ejecutarce en backround
      
      while True:
         f = open( self.path_daemon, 'r' ) #Cambiarlo por la ruta de el WDM de desarrollo
         line = f.readline()
         f.close()
         self.clean_daemon_file() 

         if line: 
            comandName = line
            if comandName == "start":
               # Inicia el servicio mailscanner.service y escribe el status en wdm.txt
               comand = 'systemctl start mailscanner.service && systemctl status mailscanner.service > ' + self.path_wdm
               os.system(comand)

            elif comandName=="stop":
               # Detiene el servicio mailscanner.service y escribe el status en wdm.txt
               comand = 'systemctl stop mailscanner.service && systemctl status mailscanner.service > ' + self.path_wdm
               os.system(comand)

            elif comandName=="reload":
               # Comando anterior ( Con reload marca error )
               # comand = 'service mailscanner reload > /var/www/html/mailscanner/status/wdm.txt'

               # Reinicia el servicio mailscanner.service y escribe el status en wdm.txt
               comand = 'systemctl restart mailscanner.service && systemctl status mailscanner.service > ' + self.path_wdm
               os.system(comand) 

            elif comandName=="status":
               # Escribe el status en wdm_status.txt
               comand = 'service mailscanner status > ' + self.path_status
               os.system(comand) 
               
            elif comandName=="repair":
               # Repara la base de datos
               comand = 'cd /usr/bin && ./mysqlcheck -u root --password=\'dscorp2000\' --auto-repair mailscanner > ' + self.path_wdm
               os.system(comand) 

            elif comandName=="resend":
               comand = 'postsuper -r ALL && postqueue -f'
               os.system(comand) 

            elif comandName=="deferred":
               comand = 'postsuper -d ALL deferred'
               os.system(comand) 

            elif comandName=="postfix":
               # Reinicia el servicio de postfix
               comand = 'postmap /etc/postfix/header_checks && service postfix restart'
               os.system(comand) 

            elif comandName=="engine_start":
               self.engine_action( 'start' )   
               
            elif comandName=="engine_stop":
               self.engine_action( 'stop' )
               
            else :
               pass #no realiza ninguna accion en caso de que la palabra nocoincida 
         else :
            pass  # no realiza ninguna accion en caso de que la palabra no coincida 
         time.sleep(2) #tiempo en que le el archivo de nuevo


   def engine_action(self, action):
      # Ubicacion de header_checks
      path_h = '/etc/postfix/header_checks'

      # Lee el archivo header_checks y lo guarda en una lista
      file_header = open( path_h, 'r' )
      data = file_header.readlines()
      file_header.close()

      # Reemplaza la linea según la acción ejecutada
      num_line = 552
      new_line = ('# /^Received:/ HOLD \n','/^Received:/ HOLD \n')[ action == 'start' ]
      data[ num_line ] = new_line

      # Escribe la lista con la nueva linea en el archivo
      file_header = open( path_h, 'w' )
      file_header.writelines( data )
      file_header.close()

      # Recarga el archivo de configuración
      comand = 'postmap /etc/postfix/header_checks && service postfix restart'
      os.system( comand ) 

   def clean_daemon_file(self):
      # borra el contenido del archivo   
      file = open(self.path_daemon, 'w')
      file.write("")
      file.close()
   
     

if __name__ == '__main__':
   app = App()    #Instancias
   logger = logging.getLogger("testlog")  #crea la bitacora con el log del demonio 
   logger.setLevel(logging.INFO)
   formatter = logging.Formatter(" ")
   handler = logging.FileHandler("/var/log/test.log") #ruta del archivo de la bitacora
   handler.setFormatter(formatter)
   logger.addHandler(handler)
   #se llama las instancias
   serv = runner.DaemonRunner(app)
   serv.daemon_context.files_preserve=[handler.stream]
   serv.do_action() #Ejecuta el método run del objeto app


