<?php
//https://gist.github.com/rayvoelker/36671222826d6560e1e4592793231bdf

require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/catalog_reports.php');
$max_weeks = 52;
header( 'Content-type: text/html; charset=utf-8' );
ob_start();

//check for get data
//select_date
if ( isset($_GET['select_date']) ) {
	if( (int) $_GET['select_date'] >= 0 AND (int) $_GET['select_date'] < $max_weeks ) {
		$get_select_date = (int) $_GET['select_date'];
	}
	else {
		$get_select_date = 0;
	}
}
else {
	$get_select_date = 0;
}

//index_tag
if ( isset( $_GET['index_tag'] ) ) {
	switch ($_GET['index_tag']) {
		case 'a':
			$get_index_tag = 'a';
			break;
		case 'd':
			$get_index_tag = 'd';
			break;
//		case 't':
//			$get_index_tag = 't';
//			break;
		default:
			$get_index_tag = 'a';
	}

}
else {
	$get_index_tag = 'a';
}

//takes a week number and a year, and returns a
//start and end date range for that week
function getDates($week, $year) {
  $date = new DateTime();
  $date->setISODate($year, $week);
  $ret[0] = $date->format('Y-m-d');
  $date->modify('+6 days');
  $ret[1] = $date->format('Y-m-d');
  return $ret;
}

$start_date = date('Y-m-d');
$date = new DateTime($start_date);
$date_select[] = array();

for($i=0; $i<$max_weeks; $i++) {
	//$date_select[$i][0] = $date->format('Y-m-d'); //23:59:59
	//$date->sub(new DateInterval('P7D'));
	//$date_select[$i][1] = $date->format('Y-m-d'); //23:59:59
	$temp_array = getDates($date->format('W'), $date->format('Y'));
	$date_select[$i][0] = $temp_array[0];
	$date_select[$i][1] = $temp_array[1];

	$date->modify('-1 week');
}

function create_query ($min_date, $max_date, $index_tag) {

	$query = "
	--  First time use (1)
	select
		c.id as 							c_id,
		c.is_locked as						c_is_locked,
		c.is_viewed as						c_is_viewed,
		c.condition_code_num as				c_condition_code_num,
		c.index_tag as 						c_index_tag, 
		c.index_entry as 					c_index_entry,
		c.record_metadata_id as				c_record_metadata_id,
		c.statistics_group_code_num as		c_statistics_group_code_num,
		c.process_gmt as					c_process_gmt,
		c.program_code as					c_program_code,
		c.iii_user_name as					c_iii_user_name,
		c.one_xx_entry as					c_one_xx_entry,
		c.authority_record_metadata_id as	c_authority_record_metadata_id,
		c.old_field as						c_old_field,
		c.new_240_field as					c_new_240_field,
		c.field as							c_field,
		c.cataloging_date_gmt as			c_cataloging_date_gmt,
		c.index_prev as						c_index_prev,
		c.index_next as						c_index_next,
		c.correct_heading as				c_correct_heading,
		c.author as							c_author,
		c.title as							c_title,
		c.phrase_entry_id as				c_phrase_entry_id,
		r.record_num as						r_record_num,
		v.field_content as					v_field_content,
		v2.field_content as					v2_field_content,
		v3.field_content as					v3_field_content
	from
	sierra_view.catmaint				c
	JOIN
	sierra_view.record_metadata			r
	ON
	  c.record_metadata_id = r.id
	LEFT OUTER JOIN
	sierra_view.varfield				v
	ON
	  ( (v.record_id = r.id) AND v.marc_tag = '001' )
	LEFT OUTER JOIN
	sierra_view.varfield				v2
	ON
	  ( (v2.record_id = r.id) AND v2.marc_tag = '910' AND v2.field_content = '|aignore_catmaint')
	LEFT OUTER JOIN
	sierra_view.varfield				v3
	ON
	  v3.record_id = r.id AND v3.marc_tag = '003'
	WHERE
	condition_code_num = 1
	AND c.index_tag = '" . $index_tag . "'
	AND (c.process_gmt >= '" . $min_date . " 00:00:00' AND c.process_gmt <= '" . $max_date . " 23:59:59')
	AND v2.field_content is NULL

	";

	if ($index_tag == "d") {
		$query .= "ORDER BY c.index_entry asc";
	}

	else {
		$query .= "ORDER BY v_field_content asc";
	}

	// $query .= "
	//ORDER by c.process_gmt desc;
	//--order by c.index_tag, c.process_gmt desc, c.id desc
	//--order by r.record_num asc
	//";

	return trim($query);
}

function do_query($query, $dsn, $username, $password) {
	//function returns json object
	try {
		$connection = new PDO($dsn, $username, $password);
	}

	catch ( PDOException $e ) {
		echo "problem connecting to database...\n";
		error_log('PDO Exception: '.$e->getMessage());
		exit(1);
	}

	$output_array = array();
	foreach ($connection->query($query) as $row) {
		$temp_array = array(
			"c_id" => $row['c_id'],
			"c_is_locked" => $row['c_is_locked'],
			"c_is_viewed" => $row['c_is_viewed'],
			"c_condition_code_num" => $row['c_condition_code_num'],
			"c_index_tag" => $row['c_index_tag'],
			"c_index_entry" => $row['c_index_entry'],
			"c_record_metadata_id" => $row['c_record_metadata_id'],
			"c_statistics_group_code_num" => $row['c_statistics_group_code_num'],
			"c_process_gmt" => $row['c_process_gmt'],
			"c_program_code" => $row['c_program_code'],
			"c_iii_user_name" => $row['c_iii_user_name'],
			"c_one_xx_entry" => $row['c_one_xx_entry'],
			"c_authority_record_metadata_id" => $row['c_authority_record_metadata_id'],
			"c_old_field" => $row['c_old_field'],
			"c_new_240_field" => $row['c_new_240_field'],
			"c_field" => $row['c_field'],
			"c_cataloging_date_gmt" => $row['c_cataloging_date_gmt'],
			"c_index_prev" => $row['c_index_prev'],
			"c_index_next" => $row['c_index_next'],
			"c_correct_heading" => $row['c_correct_heading'],
			"c_author" => $row['c_author'],
			"c_title" => $row['c_title'],
			"c_phrase_entry_id" => $row['c_phrase_entry_id'],
			"r_record_num" => $row['r_record_num'],
			"v_field_content" => $row['v_field_content'],
			"v3_field_content" => $row['v3_field_content']
		);

		$output_array[] = $temp_array;
	} //for each

	return json_encode($output_array);
}

//$query = create_query($date_select[0][1], $date_select[0][0] );
$query = create_query($date_select[$get_select_date][0], $date_select[$get_select_date][1], $get_index_tag );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>catalog reports</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

	<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'>
	<style>
	body {
		font-family: 'Droid Sans Mono', 'Courier New', Courier, monospace;
	}
	</style>
</head>

<body>

<div class="container-fluid">
<div class="row">
<div class="col-md-12">
	<form class="form-inline" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<select id="select_date" name="select_date" class="form-control"></select>

	<select id="index_tag" name="index_tag" class="form-control">
		<option value="a">index tag 'a'</option>
		<option value="d">index tag 'd'</option>
		<!-- <option value="t">index tag 't'</option> -->
	</select>

	<button type="submit" class="btn btn-default">Submit</button>
	</form>

	<div id="output"></div>
</div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script>
<?php echo "/*\n" . $query . "\n*/\n";?>

var oclc = /OCoLC/gi;

//set the correct selected index_tag
var index_tag = '<?php echo $get_index_tag; ?>';
var index_tag_node = document.getElementById('index_tag').getElementsByTagName('option');
for(var i=0; i<index_tag_node.length; i++) {
	if(index_tag_node[i].value == index_tag) {
		index_tag_node[i].selected = true;
	}
}

//set the date ranges
var date_ranges = <?php echo json_encode($date_select); ?>;

var select_date = document.getElementById('select_date');
for (var i=0; i<date_ranges.length; i++) {
	var select_node = document.createElement("option");
	select_node.value = i;
	select_node.innerHTML = date_ranges[i][0]
		+ ' --> '
		+ date_ranges[i][1];
	if(i == <?php echo $get_select_date; ?>) {
		select_node.selected = true;
	}
	select_date.appendChild(select_node);
}

<?php ob_flush(); ?>

//fill our json object
var json_output = <?php echo do_query($query, $dsn, $username, $password);?>,
	output_node = document.getElementById('output');

output_node.innerHTML = json_output.length + ' first time record headings data<br /><br />';

for (var i=0;i<json_output.length; i++) {

	var div_tag = document.createElement("div"),
		contents = '';

	contents += '<b>Field</b>: ' + json_output[i].c_field + '<br />\n';
	contents += '<b>Indexed as</b>: '
		switch (json_output[i].c_index_tag) {
			case "a" :
				contents += '<b>AUTHOR</b>: ';
				break;
			case "d" :
				contents += '<b>SUBJECT</b>: ';
				break;
			case "t" :
				contents += '<b>TITLE</b>: ';
				break;
			default :
				contents += json_output[i].c_index_tag + ': ';
		}
	contents += '<a target="_blank" href="http://id.loc.gov/search/?q=';
	contents += encodeURI(json_output[i].c_index_entry) + '">';
	contents += json_output[i].c_index_entry + '</a><br />\n';
	contents += '<b>Preceded by</b>: ' + json_output[i].c_index_prev + '<br />\n';
	contents += '<b>Followed by</b>: ' + json_output[i].c_index_next + '<br />\n';

	contents += '<b>From</b>:<a target="_blank" href="http://flyers.udayton.edu/record=b' + json_output[i].r_record_num + '">b' + json_output[i].r_record_num + '</a>';
		//put bib record num here
		contents += ' ' + json_output[i].c_author;
		contents += '&nbsp;&nbsp;<i>' + json_output[i].c_title + '</i><br />\n';
		contents += '<b>Function</b>: ' + json_output[i].c_program_code + '&nbsp;&nbsp;';
		contents += '<b>Group</b>: ' + json_output[i].c_statistics_group_code_num + '&nbsp;&nbsp;';
		contents += '<b>Initials</b>: ' + json_output[i].c_iii_user_name + '&nbsp;&nbsp;';
		contents += '<b>Entry Date</b>: ' + json_output[i].c_process_gmt + '&nbsp;&nbsp;';
		contents += '<b>Control Number</b>: ';

		//if (oclc.test(json_output[i].v3_field_content) ) {
		if (json_output[i].v3_field_content == 'OCoLC') {
			contents += '<a target="_blank" href ="http://www.worldcat.org/oclc/';
			contents += json_output[i].v_field_content;
			contents += '">(OCoLC)' + json_output[i].v_field_content + '</a>';
		}
		else {
			contents += '(' + json_output[i].v3_field_content + ')' + json_output[i].v_field_content
		}
		contents += '<br /><br />\n\n';

		//output our nodes.
		//var text_node = document.createTextNode(contents);
		//div_tag.appendChild(text_node);
		div_tag.innerHTML = contents;
		output_node.appendChild(div_tag);
}//for

</script>
</body>
</html>
<?php ob_end_flush(); ?>
