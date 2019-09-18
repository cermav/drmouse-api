# Dr.Mouse

## Backend

To install Laravel run `composer install`

To create database tables run `php artisan migrate` 

To fill database tables with predefined data run `php artisan db:seed --class=DatabaseSeeder`

When adding new migration or seeder, run `composer dump-autoload` before migration's commands to regenerate a list of all classes that need to be included in the project (autoload_classmap.php)

Add helper functions to **HelperController.php**

## DB Dump
Run `php artisan iseed data_rows,data_types,menus,menu_items,permissions,permission_role,settings,translations --force`

## Frontend

To run on dev `npm run dev`

To run on production - minify outputs `npm run production`

To wathc changes `npm run watch`

## Export for Karatnet

SELECT d.id, u.name, d.street, d.city, d.country, d.post_code, d.phone, 
    d.second_phone, u.email
FROM doctors AS d 
INNER JOIN users AS u ON d.user_id = u.id
WHERE u.email NOT LIKE '%drmouse.cz%'
WHERE d.state_id = 3



SELECT d.id, d.name,  a.street, a.city, a.zip_code, a.country, d.phone, d.phone2, d.email 
FROM drmouse_old.doctor_doctors AS d
LEFT JOIN drmouse_old.doctor_address AS a ON d.id = a.doctor_id
LEFT JOIN drmouse_old.doctor_staff_info AS inf ON d.id = inf.doc_id
WHERE d.parent_doctor_id = 0
