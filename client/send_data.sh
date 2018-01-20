#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z Resumes.php`

while true    
do
   ATIME=`stat -c %Z Resumes.php`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       echo "file transfered..."
       scp -P 5000 /var/www/html/Resumes.php root@"WEBSERVER_IP":/var/www/html/clients/"CLIENT_FOLDER"
       LTIME=$ATIME
   fi
   sleep 5
done
