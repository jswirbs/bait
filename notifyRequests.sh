
#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z requests.php`

while true    
do
   ATIME=`stat -c %Z requests.php`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       echo "A new campaign requests has been submitted to requests.php" | ssmtp "EMAIL_ADDRESS"
       LTIME=$ATIME
   fi
   sleep 5
done
