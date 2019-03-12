--exclude sudoc (086 or field tag), sword (anything starting with 8)

SELECT
--Distinct *
DISTINCT left(p.call_number_norm,7) AS left_call
FROM
sierra_view.item_record_property AS p

JOIN
sierra_view.record_metadata AS m
ON
m.id = p.item_record_id

WHERE
p.call_number_norm != ''
AND
m.campus_code = ''


--OFFSET 17000 -- test 
--limit 1000
ORDER BY left_call ASC --production 