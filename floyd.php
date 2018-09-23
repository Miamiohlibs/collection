<?php

//reset all variables needed for our connection
$username = null;
$password = null;
$dsn = null;
$connection = null;

require_once('sierra_cred.php');

//make our database connection

//set output to utf-8
$connection->query('SET NAMES UNICODE');

$sql = '
SELECT
-- i.inventory_gmt,
lower(p.barcode) as barcode,
upper(p.call_number_norm || COALESCE(' ' || v.field_content, '') ) as call_number_norm,
b.best_title AS best_title,
i.location_code AS location,
i.item_status_code AS status,
s.content AS inventory_note,
to_timestamp(c.due_gmt::text, 'YYYY-MM-DD HH24:MI:SS') as due_gmt --some dates may require 24 hour time stamp; idk

FROM
sierra_view.item_record_property	AS p
JOIN
sierra_view.item_record			AS i
ON
  p.item_record_id = i.id

LEFT OUTER JOIN
sierra_view.subfield			AS s
ON
  (s.record_id = p.item_record_id) AND s.field_type_code = 'w'

LEFT OUTER JOIN
sierra_view.checkout			AS c
ON
  (i.record_id = c.item_record_id)

LEFT OUTER JOIN
sierra_view.varfield			AS v
ON
  i.id = v.record_id AND v.varfield_type_code = 'v'

LEFT JOIN
sierra_view.bib_record_item_record_link AS l
ON
  l.item_record_id = i.id

LEFT JOIN
sierra_view.bib_record_property as b
ON
  b.bib_record_id = l.bib_record_id

WHERE
i.location_code = 'imje'
--   --comment out this section for items organized by title
-- AND
-- p.call_number_norm >= lower('PR 4879 L2 D83 2009')
-- AND
-- p.call_number_norm <= lower('PZ    7 B8163 WH19')



order by
p.call_number_norm ASC,
l.items_display_order ASC

--LIMIT 10000


'
;

$statement = $connection->prepare($sql);
$statement->execute();
$return_array = $statement->fetchAll(PDO::FETCH_ASSOC);

$encode_array['data'] = $return_array;
$encode_array['query'] = $sql;

//$row['query'] = $sql;

header('Content-Type: application/json');
echo json_encode($encode_array, JSON_PRETTY_PRINT);

$row = null;
$statement = null;
$connection = null;

?>
