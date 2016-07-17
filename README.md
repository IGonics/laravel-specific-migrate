# Laravel Notifications

Notification package for Laravel 5

## Installation

Step 1. Install using composer
```
composer require igonics/laravel-notifications
```

Step 2. Add Service Provider to config/app.php
```
return [
     ...
     "providers":[
        ...
        IGonics\Notification\Providers\NotificationServiceProvider::class,
     ]
];
```

Step 3. Publish Package Assets
``` 
php artisan vendor:publish --provider="IGonics\Notification\Providers\NotificationServiceProvider"
```

Step 4. Ensure that Database Seeders are included in database/seeds/DatabaseSeeder.php
```
$this->call(NotificationSubscriptionFrequencySeeder::class);
$this->call(NotificationTypeSeeder::class);
```

Step 5. Run Migrations
```
php artisan migarte
```

Step 6. Run Seeders
```
php artisan db:seed
```



# Maintained by
[IGonics](http://igonics.com)