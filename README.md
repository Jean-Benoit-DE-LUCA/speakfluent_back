---How to initialize the backend project---

1- Edit the example.env file and rename it to .env

2- Edit the Config.example.php file and enter your credentials. Then, rename the file as Config.php

3- Comment out the line "require_once '../Config.php'" in the JwtClass.php file

4- Comment out the line "require_once '../Config.php'" in the Mail.php file

5- Type composer install in the terminal

6- Uncomment files from step 3 and step 4

7- symfony server:start

8- Enjoy backend!
