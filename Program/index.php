<?php
	ini_set('display_errors', 1);
	header("Content-Type: text/html; charset=UTF-8"); 
	iconv_set_encoding("internal_encoding", "UTF-8"); 
	iconv_set_encoding("output_encoding", "UTF-8"); 
	ob_start("ob_iconv_handler");
	require_once("dir_libraries/f_conf.php");
	require_once("dir_libraries/f_db.php");
	require_once("dir_libraries/f_func.php");
	if(session_id() == ''){
		session_start();
		$_SESSION['u_s_root'] = preg_replace("@\/index\.php$@isu", "", $_SERVER['SCRIPT_FILENAME']);
		$_SESSION['u_s_pref'] = USERV_PREF;
	}
	$url = trim(preg_replace("@^".preg_quote(PREF_U)."@isU","",$_SERVER['REQUEST_URI']));
	$url = strtok($url, '?');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/icon.png">
    <title><?php echo U_S_NAME; ?></title>
	<script src="dir_libraries/jquery-2.2.4.min.js"></script>
	<script src="dir_libraries/bootstrap.3.3.6.min.js"></script>
	<link href="css/bootstrap.3.3.6.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/jquery.dtpicker.css"/>
	
	<script src="dir_libraries/jquery.maskedinput.js" charset="UTF-8"></script>
	<script src="dir_libraries/jquery.dtpicker.full.min.js"></script>
	
	<script>
		function div_2_print() 
		{
		  var d_two_print=document.getElementById('print');
		  var wnd_new=window.open('','Print');
		  wnd_new.document.open();
		  wnd_new.document.write('<html><body onload="window.print()">'+d_two_print.innerHTML+'</body></html>');
		  wnd_new.document.close();
		  setTimeout(function(){wnd_new.close();},10);
		}
	
		var f_tab_2_xls = (function() {
			var uri = 'data:application/vnd.ms-excel;base64,'
			, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
			, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
			, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
			return function(table, name) {
				if (!table.nodeType) table = document.getElementById(table)
				var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
				window.location.href = uri + base64(format(template, ctx))
			}
		})()
	</script>
</head>

<body>
<!--**********************************************************************************************************-->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Изменить навигацию</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><a href="<?php echo USERV_PREF; ?>" class="navbar-left" style="padding-top:0px;"><img style="max-height:50px;width:auto;" src="img/logo.png"></a></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
			<p class="navbar-text"><span class="text-uppercase">
				<strong><a class="text-primary" href="<?php echo USERV_PREF; ?>"><?php echo U_S_NAME;?></a></strong>
			</span></p>
			<?php
				echo "<li><a href='tel:".U_CONT."'><b>".U_CONT."</b></a></li>";
				echo "<li><a href='".USERV_PREF."about'><b>О салоне</b></a></li>";
				echo "<li><a href='".USERV_PREF."uslugi'><b>Услуги</b></a></li>";
				if(isset($_SESSION['kod_operatora']) && $_SESSION['tip_operatora'] != 3)
				{
					echo "<li class='dropdown'><a id='drop1' href='#' class='dropdown-toggle' data-toggle='dropdown'><b>Операции учета</b><span class='caret'></span></a>";
						echo "<ul class='dropdown-menu'>";
							if(isset($_SESSION['administrator']) && $_SESSION['administrator']) // Администратор ИС
							{
								echo "<li><a href='".USERV_PREF."show_operator'><b>Пользователи</b></a></li>";
								echo "<li><a href='".USERV_PREF."spisok_zapisey'><b>Записи в парикмахерскую</b></a></li>";
								echo "<li><a href='".USERV_PREF."ruchnoy_zakaz'><b>Ручная запись в парикмахерскую</b></a></li>";
							}
							if(!$_SESSION['administrator'] && $_SESSION['tip_operatora'] != 3) // Сотрудника салона
							{
								echo "<li><a href='".USERV_PREF."spisok_zapisey'><b>Записи в парикмахерскую</b></a></li>";
								echo "<li><a href='".USERV_PREF."ruchnoy_zakaz'><b>Ручная запись в парикмахерскую</b></a></li>";						
							}
						echo "</ul>";
					echo "</li>";
				}
				if(isset($_SESSION['kod_operatora']) && $_SESSION['tip_operatora'] == 3)
				{
					echo "<li><a href='".USERV_PREF."zapisi_klientov'><b>Записи в парикмахерскую</b></a></li>";	
				}
			?>
          </ul>
		  <?php
			echo "<ul class=\"nav navbar-nav navbar-right\">";
			if(isset($_SESSION['kod_operatora']))
				echo "<li><a href='".USERV_PREF."logout_operator'><b>Выйти [".$_SESSION['fio_operatora']."]</b></a></li>";
			else
			{
				echo "<li><a href='".USERV_PREF."registeroperator'><b>Зарегистрироваться в ИС</b></a></li>";
				echo "<li><a href='".USERV_PREF."userLogin'><b>Авторизоваться в ИС</b></a></li>";
			}
			echo "</ul>";
		  ?>
        </div>
      </div>
    </div>

<div class="container">

    <div class="main">
<?php	
		if(isset($_SESSION['kod_operatora']) && !$_SESSION['kod_operatora']) // запрет для того, кто не авторизован
			if(in_array($url, array("logout_operator", "remove_operator", "edit_operator", "show_operator", "operatoradd")))
				redir2('not_found');
		if(isset($_SESSION['kod_operatora']) && $_SESSION['kod_operatora'] && !$_SESSION['administrator']) // запрет для авторизованного, но не админа
			if(in_array($url, array("remove_operator", "edit_operator", "show_operator", "operatoradd")))
				redir2('not_found');
		if(isset($_SESSION['kod_operatora']) && $_SESSION['kod_operatora'] && $_SESSION['administrator']) // запрет дла админа
			if(in_array($url, array()))
				redir2('not_found');

		if($url=="" || $url=="/")
		{
			if(!isset($_SESSION['kod_operatora'])) // неавторизованный пользователь
				require_once("site_windows/main.php");
			else if(isset($_SESSION['kod_operatora'])) // авторизованный пользователь
			{
				if($_SESSION['administrator'])
					require_once("site_windows/show_operator.php");
				else
				{
					if($_SESSION['tip_operatora']==3)
						require_once("site_windows/zapisi_klientov.php");
					else if($_SESSION['tip_operatora']==2)
						require_once("site_windows/spisok_zapisey.php");
				}
			}
		}
		else if(!file_exists("site_windows/$url.php"))
			require_once("site_windows/not_found.php");
		else
			require_once("site_windows/$url.php");
?>
    </div>
</div>
<footer class="page-footer font-small stylish-color-dark pt-2 mt-2" style="margin-bottom:20px;">
    <div class="footer-copyright py-3 text-center border-top border-primary">
        © <?php echo date("Y"); ?> Copyright:
        <a href="<?php echo USERV_PREF; ?>"> <?php echo U_S_NAME; ?> </a>
    </div>
</footer>
<?php
if(DBG_U_SITE)
	debug();
?>
</body>
</html>