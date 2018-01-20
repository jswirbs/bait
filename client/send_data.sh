#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z Resumes.php`

while true    
do
   ATIME=`stat -c %Z Resumes.php`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       echo "file transfered..."
       scp -P 5000 /var/www/html/Resumes.php root@45.55.218.37:/var/www/html/clients/jswirbul2
       LTIME=$ATIME
   fi
   sleep 5
done
