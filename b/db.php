<?php
// getting config file
require("db-config.php");

// Create connection
$mysqli=mysqli_connect(SQL_HOST,SQL_USER,SQL_PASS,SQL_DB);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else {
echo "";
}


function do_sql($tbl_name, $ele, $query, $mysqli, $where) {
	switch($query) {
		case 'update':
			$sql_i = "UPDATE ".$tbl_name.' SET ';
			foreach ($ele as $column_name => $value) {
			 	$list .= ','.$column_name.'="'.$value.'"';
			}
			$wherelist= ' WHERE 1';
			foreach($where as $col_nm => $val)
				$wherelist.= ' AND '.$col_nm.'="'.$val.'"';
 
			$sql=$sql_i.substr($list,1).$wherelist;//echo "<script>alert(".$sql.");</script>";exit;
			if($mysqli->query($sql) === false) {
			  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} else {
			  $affected_rows = $mysqli->affected_rows;
			}//echo "<script>alert(".$sql.");</script>";exit;
		break;	
		case 'insert':
			// check for duplicates
			$check = "SELECT * FROM ".$tbl_name." WHERE ";
			foreach($where as $col_nm => $val)		
				$check .= $col_nm.'="'.$val.'" OR ';
			$check .= '0;';
			$mm = $mysqli->query($check) or die($mysqli->error);
			$count = $mm->num_rows;
			if($count > 0) {
				return 'Duplicate Entry';
			}
			else { 				
			$sql_i = "INSERT INTO ".$tbl_name.' ( ';
			foreach ($ele as $column_name => $value) {
			 $value = $mysqli->real_escape_string($value);
		 	 $columns .= ','.$column_name;
			 $values .= ',"'.$value.'"';
			}

 
			$sql=$sql_i.substr($columns,1).' ) VALUES ('.substr($values,1)." );";//echo $sql;
			if($mysqli->query($sql) === false) {
			  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} else {
				return $mysqli->insert_id;;
			  $affected_rows = $mysqli->affected_rows;
			}
			}//echo "<script>alert(".$sql.");</script>";exit;
		break;	
		case 'delete':
			$sql_i = "DELETE FROM ".$tbl_name.' WHERE ';
			$c = 0;
			foreach ($where as $column_name => $value) {
			 $value = $mysqli->real_escape_string($value);
			 if($c==0)
				$list= $column_name.'="'.$value.'"';
			 else
			 	$list .= ' AND '.$column_name.'="'.$value.'"';
			 $c++;
			}

 
			$sql=$sql_i.$list.";";
 
			if($mysqli->query($sql) === false) {
			  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} else {
			  $affected_rows = $mysqli->affected_rows;
			}
		break;	

	}
	return $sql;
}

// function to convert <br> to newline
function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

function query_exec($sql_stmt,$mysqli) {

	$getVal = $mysqli->prepare($sql_stmt) or die('Couldn\'t check the table.');
	$getVal->execute();
	$getVal->store_result();
	return $getVal;
}

?>
