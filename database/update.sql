-- correct id to old ones
SELECT ov.id, ov.value, p.id
FROM drmouse_old.doctor_category_values AS ov
LEFT JOIN properties AS p ON ov.value = p.name AND ov.category_id = p.property_category_id;


UPDATE drmouse_old.doctor_category_values AS ov, properties AS p
SET p.id = ov.id
WHERE ov.value = p.name AND ov.category_id = p.property_category_id;
