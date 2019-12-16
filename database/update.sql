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
