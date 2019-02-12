SELECT --Query by Craig Boman Miami University Libraries
--count(*)
DISTINCT m.record_type_code || m.record_num	AS item_record_num,
i.item_status_code || ' ' || sn.name AS item_status,
-- Copy --not sure we can get copy
p.call_number_norm,
-- Volume
b.best_author,
b.best_title,
b.publish_year,
i.last_checkout_gmt,
i.last_checkin_gmt,
i.checkout_total,
i.internal_use_count
-- Renewals,


--   (SELECT COUNT(*) FROM sierra_view.item_record i WHERE l.item_record_id = i.id
--     AND i.item_status_code IN ('-','o')) AS "Good items"  --good items select by Phil Shirley


FROM
  sierra_view.item_record_property 	AS p
JOIN
  sierra_view.phrase_entry 		AS e
ON
  e.record_id = p.item_record_id

JOIN
  sierra_view.item_record 		AS i
ON
  p.item_record_id = i.id

JOIN
  sierra_view.item_status_property 	AS s
ON
  s.code = i.item_status_code

JOIN
  sierra_view.item_status_property_name	AS sn
ON
  s.id = sn.item_status_property_id

JOIN
  sierra_view.record_metadata		AS m
on
  i.record_id = m.id


LEFT OUTER JOIN
  sierra_view.varfield			AS v
ON
  i.id = v.record_id

LEFT JOIN
  sierra_view.bib_record_item_record_link AS l
ON
  l.item_record_id = i.id

LEFT JOIN
  sierra_view.bib_record_property 	AS b
ON
  b.bib_record_id = l.bib_record_id


WHERE
m.campus_code = ''

AND

i.location_code = 'scr' --test
--i.location_code = '$location'  --production

  --comment out this section for items organized by title
AND
--test
p.call_number_norm BETWEEN lower('AY   67 N5 W7  2005') AND lower('PN  171 F56 W35 1998')
--production
--p.call_number_norm BETWEEN lower('$start') AND lower('$end')

--LIMIT 100 --test
ORDER BY
p.call_number_norm ASC
