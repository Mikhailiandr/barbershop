<?php

	function passrand( $vlen = 8 ) 
	{ 
		$syms = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?"; 
		$vlen = rand(10, 16); 
		$pss = substr( str_shuffle(sha1(rand() . time()) . $syms ), 0, $vlen );
		return $pss;
	}
	
	function $gethash($vlen=4)
	{
    	//$syms = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    	$syms = "123456789";
		$hashval = "";
    	$cclen = strlen($syms) - 1;
    	while (strlen($hashval) < $vlen) {
            $hashval .= $syms[mt_rand(0,$cclen)];
    	}
    	return $hashval;
	}
	
	
	function debug()
	{
	  if(DBG_U_SITE)
	  {
        echo "POST: ";
        print_r($_POST); 
		echo "<br>GET: ";
		print_r($_GET); 
		echo "<br>FILES: "; 
		print_r($_FILES);
		echo "<br>SESSION: "; 
		print_r($_SESSION);
      }
	}
	
	function seluserfromfield($fieldName, $field)
	{
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
  		$dbsql = "SELECT kod_operatora, fio_operatora, tel, login_operatora, parol_operatora, type, pss_op, add_op FROM operatory WHERE $fieldName='".$field."' LIMIT 1";
    	//echo $dbsql;
		$dbquer = mysqli_query($dblnk, $dbsql);
    	if(!$dbquer)
    		return false;
    	$dbdata = mysqli_fetch_assoc($dbquer);
		mysqli_close($dblnk);
		return $dbdata;
	}
	
	function $admus($kod_operatora)
	{
		$dblnk=mysqli_connect(U_DB_HST, U_DB_USER, U_DB_PSSWD, U_DB_NAME);
		mysqli_set_charset($dblnk, 'utf8');
  		$dbsql = "SELECT type from operatory WHERE kod_operatora=$kod_operatora";
    	$dbquer = mysqli_query($dblnk, $dbsql);
    	if(!$dbquer)
    		return false;
    	$dbdata = mysqli_fetch_assoc($dbquer);
		mysqli_close($dblnk);
		if($dbdata['type']==1)
			return true;
		else
			return false;
	}
	
	function validU()
	{
		$dbresurs = array();
		if( isset($_POST['login']) && isset($_POST['parol_operatora']) )
		{
            $u = seluserfromfield('login_operatora', $_POST['login']); 
			if(is_array($u) && isset($u['kod_operatora']) && isset($u['parol_operatora']) && $u)
			{
    			if($u['parol_operatora'] === $_POST['parol_operatora'])
    			{
        			$hashval = md5($gethash(10));

        			updhashuser($hashval, $u['kod_operatora']);

        			setcookie("id", $u['kod_operatora'], time()+60*60*24*30, "/");
        			setcookie("hash", $hashval, time()+60*60*24*30,  "/");
                    $dbresurs[0] = true;
           			$dbresurs[1] = "";
					$_SESSION['kod_operatora'] = $u['kod_operatora'];
					$_SESSION['fio_operatora'] = $u['fio_operatora'];
					$_SESSION['telephone'] = $u['tel'];
					$_SESSION['login_operatora'] = $u['login_operatora'];
					$_SESSION['tip_operatora'] = $u['type'];
                    return $dbresurs;
    			}
    			else
    			{
        			$dbresurs[0] = false;
           			$dbresurs[1] = "Некорректный логин и/или пароль";
					if(isset($_SESSION['kod_operatora']))
					{
						unset($_SESSION['kod_operatora']);
						unset($_SESSION['fio_operatora']);
						unset($_SESSION['telephone']);
						unset($_SESSION['login_operatora']);
						unset($_SESSION['tip_operatora']);
					}
                    return $dbresurs;
    			}
			}
			else
			{
				$dbresurs[0] = false;
           		$dbresurs[1] = "Ошибка при подтверждении";
				if(isset($_SESSION['kod_operatora']))
				{
					unset($_SESSION['kod_operatora']);
					unset($_SESSION['fio_operatora']);
					unset($_SESSION['telephone']);
					unset($_SESSION['login_operatora']);
					unset($_SESSION['tip_operatora']);
				}
             	return $dbresurs;
			}
		}
        if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
		{
  			$u = seluserfromfield('kod_operatora', intval($_COOKIE['id']));
  			if(is_array($u) && isset($u['kod_operatora']) && isset($u['hash']) && $u)
			{
                if(($u['hash'] !== $_COOKIE['hash']) or ($u['kod_operatora'] !== $_COOKIE['id']))
    			{
        			setcookie("id", "", time() - 12 * 30 * 24 * 3600, "/");
        			setcookie("hash", "", time() - 12 * 30 * 24 * 3600, "/");
        			$dbresurs[0] = false;
           			$dbresurs[1] = "При валидации произошла ошибка № 1";
					if(isset($_SESSION['kod_operatora']))
					{
						unset($_SESSION['kod_operatora']);
						unset($_SESSION['fio_operatora']);
						unset($_SESSION['telephone']);
						unset($_SESSION['login_operatora']);
						unset($_SESSION['tip_operatora']);
					}
                    return $dbresurs;
    			}
    			else
    			{
        			$dbresurs[0] = true;
           			$dbresurs[1] = "";
					$_SESSION['kod_operatora'] = $u['kod_operatora'];
					$_SESSION['fio_operatora'] = $u['fio_operatora'];
					$_SESSION['tip_operatora'] = $u['type'];
                    return $dbresurs;
    			}
			}
			else
			{
                $dbresurs[0] = false;
           		$dbresurs[1] = "При валидации произошла ошибка № 2";
				if(isset($_SESSION['kod_operatora']))
				{
					unset($_SESSION['kod_operatora']);
					unset($_SESSION['fio_operatora']);
					unset($_SESSION['telephone']);
					unset($_SESSION['login_operatora']);
					unset($_SESSION['tip_operatora']);
				}
             	return $dbresurs;
			}
		}
		$dbresurs[0] = false;
  		$dbresurs[1] = "При валидации произошла ошибка № 3";
		if(isset($_SESSION['kod_operatora']))
		{
			unset($_SESSION['kod_operatora']);
			unset($_SESSION['fio_operatora']);
			unset($_SESSION['kod_operatora']);
			unset($_SESSION['fio_operatora']);
			unset($_SESSION['telephone']);
			unset($_SESSION['login_operatora']);
			unset($_SESSION['tip_operatora']);
		}
  		return $dbresurs;
	}
	
	function redir2($page)
	{
		header('Location:'.USERV_PREF.preg_replace("@^\/@isU","",$page));
	}
	
?>