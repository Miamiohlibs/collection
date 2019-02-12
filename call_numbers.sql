SELECT
--*
p.call_number_norm
FROM
sierra_view.item_record_property AS p
WHERE
p.call_number_norm != ''

--LIMIT 100
ORDER BY p.call_number_norm ASC