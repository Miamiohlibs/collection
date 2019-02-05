SELECT
--count(*)
m.record_type_code || m.record_num	AS item_record_num,
i.item_status_code,
-- Copy --not sure we can get copy
p.call_number_norm,
-- Volume
b.best_author,
b.best_title,
b.publish_year
i.last_checkin_gmt,
i.checkout_total,
i.internal_use_count,
-- Renewals,
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
  sierra_view.record_metadata		AS m
on
  i.record_id = m.id


-- Don't think I need this table
-- LEFT OUTER JOIN
--   sierra_view.checkout			AS c
-- ON
--   (i.record_id = c.item_record_id)


LEFT OUTER JOIN
  sierra_view.varfield			AS v
ON
  i.id = v.record_id 

LEFT JOIN
  sierra_view.bib_record_item_record_link AS l
ON
  l.item_record_id = i.id

LEFT JOIN
  sierra_view.bib_record_property as b
ON
  b.bib_record_id = l.bib_record_id


WHERE
m.campus_code = ''

AND

i.location_code = 'scr'

  --comment out this section for items organized by title
AND
p.call_number_norm BETWEEN lower('AY   67 N5 W7  2005') AND lower('PN  171 F56 W35 1998')

--LIMIT 100
--ORDER BY
--p.call_number_norm ASC
