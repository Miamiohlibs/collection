<?php
/*
Purpose of this script is to provide a basic output for subject selectors to weed collections

Sample URL: http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/locations.php
*/

require_once('getdb.php');

$db_handle = getdb(); //call the db function

$query = "
SELECT
--Locations
FROM
sierra_view.item_record_property AS p

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
