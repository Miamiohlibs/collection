<?php
/*
Purpose of this script is to provide a basic output for subject selectors to weed collections

Sample URL: http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/call_numbers.php*/

require_once('getdb.php');

$db_handle = getdb(); //call the db function

$query = "
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
";


$result = pg_query($db_handle, $query);

$rows = array();
while($r = pg_fetch_row($result)) {
      $rows[] = $r;
 }
echo json_encode($rows);

 pg_free_result($result);
//deprecated pg_close($db_handle);
?>
