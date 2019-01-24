# Dr.Mouse

## Backend

To install Laravel run `composer install`

To create database tables run `php artisan migrate` 

To fill database tables with predefined data run `php artisan db:seed --class=DatabaseSeeder`

When adding new migration or seeder, run `composer dump-autoload` before migration's commands to regenerate a list of all classes that need to be included in the project (autoload_classmap.php)

Add helper functions to **HelperController.php**

## DB Dump
Run php artisan iseed data_rows,data_types,menus,menu_items,permissions,permission_role,settings,translations --force