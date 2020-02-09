-- correct id to old ones
SELECT ov.id, ov.value, p.id
FROM drmouse_old.doctor_category_values AS ov
LEFT JOIN properties AS p ON ov.value = p.name AND ov.category_id = p.property_category_id;


UPDATE drmouse_old.doctor_category_values AS ov, properties AS p
SET p.id = ov.id
WHERE ov.value = p.name AND ov.category_id = p.property_category_id;


ALTER TABLE `doctors` ADD FULLTEXT KEY `search_data` (`search_name`,`description`,`street`,`city`,`country`);




INSERT INTO `scores`(`id`, `user_id`, `author_id`, `comment`, `ip_address`, `is_approved`, `created_at`, `updated_at`)
SELECT dv.id,
  (SELECT user_id FROM drmouse.doctors WHERE slug LIKE d.slug),
  NULL,
  'Původní hodnocení',
  dv.ip_address,
  0,
  dv.vote_date,
  dv.vote_date

FROM drmouse_old.doctor_voted AS dv
INNER JOIN drmouse_old.doctor_doctors AS d ON dv.doctor_id = d.id
WHERE dv.poll_id = 20;


INSERT INTO drmouse.score_details(`score_id`, score_item_id, points, `created_at`, `updated_at`)
SELECT
  (SELECT MAX(id) FROM drmouse_old.doctor_voted WHERE doctor_hash = dr.doctor_hash) AS vote_id,
  CASE
    WHEN dr.question_id = 31 THEN 1
    WHEN dr.question_id = 29 THEN 2
    WHEN dr.question_id = 30 THEN 3
    WHEN dr.question_id = 28 THEN 4
    WHEN dr.question_id = 32 THEN 5
    ELSE null
  END AS item_id,
  dr.value,
  dr.date_created,
  dr.date_created
FROM drmouse_old.doctor_rating AS dr
WHERE date_created != '0000-00-00 00:00:00'
      AND EXISTS(SELECT id FROM drmouse_old.doctor_voted WHERE doctor_hash = dr.doctor_hash)


SELECT REPLACE(avatar, 'https://www.drmouse.cz/new/wp-content/uploads/', '') FROM users

SELECT * FROM users WHERE avatar IS NOT NULL AND avatar LIKE 'http%'

UPDATE users SET avatar = SUBSTRING(avatar, 2) WHERE avatar IS NOT NULL AND SUBSTRING(avatar, 1, 1) = '/'


UPDATE users SET avatar = REPLACE(avatar, 'https://www.drmouse.cz/wp-content/uploads/', '')
UPDATE users SET avatar = REPLACE(avatar, 'https://www.drmouse.cz/new/wp-content/uploads/', '')
UPDATE users SET avatar = REPLACE(avatar, 'http://www.drmouse.cz/new/wp-content/uploads/', '')

UPDATE users SET avatar = NULL WHERE avatar LIKE 'https://www.drmouse.cz/wp-content/themes/DrMouse2/img/profileDoctor01.png'
UPDATE users SET avatar = NULL WHERE avatar LIKE 'https://www.drmouse.cz/wp-content/themes/DrMouse2/img/profileDoctor02.png'
UPDATE users SET avatar = NULL WHERE avatar LIKE 'https://www.drmouse.cz/wp-content/themes/DrMouse2/img/profileDoctor03.png'
UPDATE users SET avatar = NULL WHERE avatar LIKE 'https://www.drmouse.cz/wp-content/themes/DrMouse2/img/profileDoctor04.png'
UPDATE users SET avatar = NULL WHERE avatar LIKE 'http://www.drmouse.cz/new/wp-content/themes/DrMouse2/img/profileDoctor02.png'
UPDATE users SET avatar = NULL WHERE avatar LIKE 'https://www.drmouse.cz/wp-content/themes/DrMouse2'


SELECT * FROM users WHERE avatar IS NOT NULL AND avatar LIKE '%/%'
UPDATE users SET avatar = REPLACE(avatar, 'doctors/', '') WHERE avatar LIKE 'doctors/%'

UPDATE users SET avatar = REPLACE(avatar, '2016/10/', '') WHERE avatar LIKE '2016/10/%';
UPDATE users SET avatar = REPLACE(avatar, '2016/11/', '') WHERE avatar LIKE '2016/11/%';
UPDATE users SET avatar = REPLACE(avatar, '2016/12/', '') WHERE avatar LIKE '2016/12/%';

UPDATE users SET avatar = REPLACE(avatar, '2017/01/', '') WHERE avatar LIKE '2017/01/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/02/', '') WHERE avatar LIKE '2017/02/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/03/', '') WHERE avatar LIKE '2017/03/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/04/', '') WHERE avatar LIKE '2017/04/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/05/', '') WHERE avatar LIKE '2017/05/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/06/', '') WHERE avatar LIKE '2017/06/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/07/', '') WHERE avatar LIKE '2017/07/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/08/', '') WHERE avatar LIKE '2017/08/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/09/', '') WHERE avatar LIKE '2017/09/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/10/', '') WHERE avatar LIKE '2017/10/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/11/', '') WHERE avatar LIKE '2017/11/%';
UPDATE users SET avatar = REPLACE(avatar, '2017/12/', '') WHERE avatar LIKE '2017/12/%';

UPDATE users SET avatar = REPLACE(avatar, '2018/01/', '') WHERE avatar LIKE '2018/01/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/02/', '') WHERE avatar LIKE '2018/02/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/03/', '') WHERE avatar LIKE '2018/03/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/04/', '') WHERE avatar LIKE '2018/04/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/05/', '') WHERE avatar LIKE '2018/05/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/06/', '') WHERE avatar LIKE '2018/06/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/07/', '') WHERE avatar LIKE '2018/07/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/08/', '') WHERE avatar LIKE '2018/08/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/09/', '') WHERE avatar LIKE '2018/09/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/10/', '') WHERE avatar LIKE '2018/10/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/11/', '') WHERE avatar LIKE '2018/11/%';
UPDATE users SET avatar = REPLACE(avatar, '2018/12/', '') WHERE avatar LIKE '2018/12/%';


UPDATE users SET avatar = REPLACE(avatar, '2019/01/', '') WHERE avatar LIKE '2019/01/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/02/', '') WHERE avatar LIKE '2019/02/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/03/', '') WHERE avatar LIKE '2019/03/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/04/', '') WHERE avatar LIKE '2019/04/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/05/', '') WHERE avatar LIKE '2019/05/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/06/', '') WHERE avatar LIKE '2019/06/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/07/', '') WHERE avatar LIKE '2019/07/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/08/', '') WHERE avatar LIKE '2019/08/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/09/', '') WHERE avatar LIKE '2019/09/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/10/', '') WHERE avatar LIKE '2019/10/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/11/', '') WHERE avatar LIKE '2019/11/%';
UPDATE users SET avatar = REPLACE(avatar, '2019/12/', '') WHERE avatar LIKE '2019/12/%';



SELECT *
FROM doctors AS d
INNER JOIN doctors AS d2 ON d.latitude = d2.latitude AND d.longitude = d2.longitude
  AND d.id != d2.id;




ALTER TABLE doctors DROP INDEX doctor_text;
ALTER TABLE `doctors` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `doctors` CHANGE `slug` `slug` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `doctors` CHANGE `phone` `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `doctors` CHANGE `second_phone` `second_phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `website` `website` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `street` `street` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `city` `city` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `country` `country` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `working_doctors_names` `working_doctors_names` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `doctors` CHANGE `search_name` `search_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
CREATE FULLTEXT INDEX doctor_text ON doctors (search_name, description, street, city, country, working_doctors_names);

CREATE TABLE doctors_old_state
SELECT id, state_id FROM doctors;

UPDATE doctors SET state_id = 3 WHERE state_id = 1;

UPDATE doctors SET state_id = 6 WHERE id IN (SELECT id FROM doctors_old_state WHERE state_id = 3)


ALTER TABLE `scores` ADD `status_id` INT UNSIGNED NOT NULL DEFAULT '10' AFTER `author_id`;




SELECT * FROM doctors WHERE MATCH (search_name, description, street, city, country, working_doctors_names) AGAINST ('*kosar*' IN BOOLEAN MODE)

INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`) VALUES ('3', 'member', 'Paníček', CURRENT_TIME(), CURRENT_TIME());

