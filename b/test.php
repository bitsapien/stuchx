<?php
include('db.php');
$column[0] = 'nest_id';
$column[1] = 'nest_name';
$where['nest_added'] = 2;
$where['nest_dead'] = 3;
$logic[0] = 'OR';
$table='nest';
data_extract($table,$column,$mysqli,$where,$logic);
?>
