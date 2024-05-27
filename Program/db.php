<?php
	function fun_db($dbsql)
	{
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
		$dbsql = str_replace("'null'", "null", $dbsql);
		$dbresurs = mysqli_query($dblnk, $dbsql);
		mysqli_close($dblnk); 
		return $dbresurs;
	}
	function dbselec($dbsql)
	{
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
    	$dbquer = mysqli_query($dblnk, $dbsql);
		//echo $dbsql;
		//exit;
    	if(!$dbquer)
    		return false;
    	$dbdata = array();
		$i=0;
		while($dbrow = mysqli_fetch_array($dbquer))
		{
			$dbarr = array();
			$dbvalcnt = (count($dbrow)+1)/2;
			for($j=0; $j<$dbvalcnt; $j++){
				if(array_key_exists($j, $dbrow))
					$dbarr[$j] = $dbrow[$j];
			}
			$dbdata[$i] = $dbarr;
			$i++;
		}
		mysqli_close($dblnk); 
		return $dbdata;
	}
	
	function puttodb($dbtbl, $dbfileds, $dbvals)
	{	
		# Соединение с БД
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
		$dbsql = "insert into $dbtbl($dbfileds) values(".implode(",",$dbvals).");"; 
		$dbsql = str_replace("'null'", "null", $dbsql);
		//echo $dbsql."<br>";
		//exit;
    	if(!mysqli_query($dblnk, $dbsql))
		{
			mysqli_close($dblnk);
			return false;
		}
    	$id = mysqli_insert_id($dblnk);
    	mysqli_close($dblnk);
		return $id;
	}
	
	function updateTable($dbtbl, $dbfileds, $dbvals, $dbidname, $dbidvalue)
	{		
		$dbfileds = explode(',', $dbfileds);
		$dbvalcnt = count($dbfileds);
		if($dbvalcnt != count($dbvals))
			return false;
		
		$dblist = $dbfileds[0]."=".$dbvals[0];
		for($i=1; $i<$dbvalcnt; $i++)
			$dblist .= ", ".trim($dbfileds[$i])."=".trim($dbvals[$i]);
			
		# Соединение с БД
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
		$dbsql = "update $dbtbl set $dblist where $dbidname=$dbidvalue;";
		$dbsql = str_replace("'null'", "null", $dbsql);
		//echo $dbsql; 
		//exit();
    	if(!mysqli_query($dblnk, $dbsql))
		{
			mysqli_close($dblnk);
			return false;
		}
    	
    	mysqli_close($dblnk);
		return true;
	}
	
	function remfromtbl($dbtbl, $id, $value)
	{	
		# Соединение с БД
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
		$dbsql = "delete from $dbtbl where $id=$value;";
		$dbsql = str_replace("'null'", "null", $dbsql);
    	if(!mysqli_query($dblnk, $dbsql))
		{
			mysqli_close($dblnk);
			return false;
		}
    	
    	mysqli_close($dblnk);
		return true;
	}

?>