<!--Heading-->
![npm](https://img.shields.io/npm/v/npm
) ![php](https://img.shields.io/badge/php-v8.2.0-v8
) ![laravel](https://img.shields.io/badge/laravel-v11.0-v8
)

---

# _Installation_

```diff
+PHP version 8.2 is required
```



# _Configuration_
```diff
Connect to your database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password
```

# _Configuration and running_
```
composer update && composer install
php artisan key:generate
php artisan migrate:fresh --seed 
npm install
npm run dev 


We have test user created for you, remember to run the 
migrations to seed the user. 

Here are the credentials:

email: test@example.com
password: 12345678

For the API collection, a postman collection URL is provided below.

API collection: https://www.postman.com/muva-tech/workspace/pendo/collection/36018715-f1b6704c-cc73-4d39-bf4e-2946437bbbca?action=share&creator=36018715
