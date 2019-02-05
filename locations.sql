SELECT
--*
l.code,
n.name
FROM
sierra_view.location AS l
JOIN
sierra_view.location_name AS n
ON
n.location_id = l.id

--LIMIT 10

ORDER BY l.code ASC

;