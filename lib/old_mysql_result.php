<?php
# http://mysql.dotpointer.com/
# mysql to mysqli migration library
# by dotpointer
	function old_mysql_result ($result , $row , $field = 0) {
		if (mysqli_data_seek($result, $row) === false) return false;
		if (is_int($field)) $line=mysqli_fetch_array($result); else $line=mysqli_fetch_assoc($result);
		if (!isset($line[$field])) return false;
		return $line[$field];
	}
?>