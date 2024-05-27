<?php
	if(isset($_POST['filterbutton'])&&isset($_POST['kod_uslugi'])&&isset($_POST['dtpicker'])&&$_POST['dtpicker']!="")
	{
		$post_true = true;
		
		$kod_uslugi = trim($_POST['kod_uslugi']);
		$field_datetime = DateTime::createFromFormat('d.m.Y H:i', trim($_POST['dtpicker']));
		
		$den = $field_datetime->format('N');
		$field_datetime_sql = $field_datetime->format('Y-m-d H:i');
		$timeinsql = $field_datetime->format('H:i');
		
		// Проверяем, в данный день недели оказывается данная услуга?
		$dbsql = "select a.kod_raspisanija, a.kod_uslugi_sotrudnika, a.den_nedeli, a.vremja_nachala, a.vremja_okonchanija, b.kod_operatora, b.kod_uslugi, b.dlitelnost, b.predv_stoimost, c.fio_operatora, c.tel from raspisanie as a inner join uslugi_sotrudnikov as b on a.kod_uslugi_sotrudnika=b.kod_uslugi_sotrudnika inner join operatory as c on b.kod_operatora=c.kod_operatora where b.kod_uslugi=$kod_uslugi and a.den_nedeli=$den and (TIME('$timeinsql') >= TIME(a.vremja_nachala)) and (TIME('$timeinsql') <= TIME(a.vremja_okonchanija))";

		$dbresursult = dbselec($dbsql);
		$listfree = array();
		if(count($dbresursult) > 0) // услуга оказывается
		{
			// Для каждого мастера из выборки проверяем его занятость на конкретную дату и время
			$xx = 0;
			foreach($dbresursult as $dbrow)
			{
				$dbsql1 = "select count(a.kod_zapisi) from zapisi as a inner join uslugi_sotrudnikov as b on a.kod_uslugi_sotrudnika=b.kod_uslugi_sotrudnika where b.kod_uslugi=1 and '$field_datetime_sql' > DATE_ADD(a.data_vremja_zapisi, INTERVAL -b.dlitelnost MINUTE) and '$field_datetime_sql' < DATE_ADD(a.data_vremja_zapisi, INTERVAL b.dlitelnost MINUTE)";
	
				$dbresursult = dbselec($dbsql1);
				
				if($dbresursult[0][0] == 0) // Время свободно. Добавляем мастера в итоговый массив
				{
					$listfree[$xx][0] = $dbrow[5];  // kod_operatora
					$listfree[$xx][1] = $dbrow[9];  // fio_operatora
					$listfree[$xx][2] = $dbrow[10]; // telefon
					$xx++;
				}
			}
		}
	}
	else
		$post_true = false;
	
	echo "<h1>Записи в парикмахерскую</h1>";
?>
<div id="boxmess"></div>
<p>&nbsp;</p>
<form class="form-horizontal text-left" method="post" name="regForm" action="zapisi_klientov" enctype='multipart/form-data'>
<fieldset>
<legend><b>Записаться на прием</b></legend>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="kod_uslugi">Надо выбрать услугу</label>  
				<div class="col-md-7">
					<select id="kod_uslugi" name="kod_uslugi" class="form-control">
					<?php
						echo "<option value='-1'>Выбрать услугу</option>";
						$uslugi = dbselec("select kod_uslugi, usluga from uslugi order by usluga asc");
						$dbvalcnt = count($uslugi);
						for($i=0; $i<$dbvalcnt; $i++)
							echo "<option value='".$uslugi[$i][0]."' ".($uslugi[$i][0]==$kod_uslugi?"selected":"").">".$uslugi[$i][1]."</option>";
					?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="dt_t1">Надо выбрать время</label>
				<div class="col-md-7">
					<input type="hidden" id="dt" name="dt" />
					<input id="dtpicker" name="dtpicker" class="form-control input-md" type="text" <?php echo $post_true?"value='".$_POST['dtpicker']."'":"disabled"; ?>/>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="filterbutton"></label>
				<div class="col-md-7">
					<button id="filterbutton" name="filterbutton" class="btn btn-primary">Показать расписание</button>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="emptyfilterbtn"></label>
				<div class="col-md-7">
					<button id="emptyfilterbtn" name="emptyfilterbtn" class="btn btn-primary" onclick="return false;">Сбросить параметры</button>
				</div>
			</div>
		</div>
	</div>
</div>
</fieldset>
</form>

<?php
if($post_true)
{
	$ffcnt = count($listfree);
	if($ffcnt == 0)
	{
	?>
		<div class="row">
			<h3 class="text-left">Данное время занято. Выберите другое</h3>
		</div>
	<?php
	}
	else
	{
	?>
	<script>
		function signup(kod_uslugi, id_client, kod_operatora, datetime)
		{
			var datafromform = new FormData();
			
			datafromform.append("kod_uslugi", kod_uslugi);
			datafromform.append("id_client", id_client);
			datafromform.append("kod_operatora", kod_operatora);
			datafromform.append("datetime", datetime);
			
			$.ajax({
				type: "POST",
				url: "dynamic/register_himself.php",
				success: function (data) {
					if(data != false){
						//$('#boxmess').html(data);
						if(data == -1)
							alert('Время уже занято, к сожалению');
						else if(data == -2)
							alert('Ошибка записи');
						else if(data == -9)
							alert('Парикмахер занят на это время');
						else if(data == 1)
						{
							var usluga = $('#$optservid:selected').text();
							var fio_operatora = $('#fio_operatora'+kod_operatora).text();
							var tel = $('#tel'+kod_operatora).text();
							alert('Вы успешно записаны '+datetime+' в парикмахерскую "'+usluga+'" к мастеру "'+fio_operatora+' ('+tel+')"');
							window.location.href = window.location.href;
						}
					}
					else
					{
						alert('В скрипте ошибка');
					}
					return false;
				},
				error: function (error) {
					alert('Ошибка какая-то');
					return false;
				},
				async: true,
				data: datafromform,
				cache: false,
				contentType: false,
				processData: false,
				timeout: 40000
			});
				
			return false;
		}
	</script>
	<div class="row">
	<h3 class="text-left">Записи на прием</h3>
	<table class="text-left table table-bordered table-striped table-hover table-responsive">
		<thead>
			<tr>
				<th>
					Дата/Время
				</th>
				<th>
					ФИО парикмахера
				</th>
				<th>
					Телефон
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			
			for($k=0; $k<$ffcnt; $k++)
			{
		?>		<tr>
					<td><?php echo $field_datetime_sql; ?></td>
					<td id="fio_operatora<?php echo $listfree[$k][0]; ?>"><?php echo $listfree[$k][1]; ?></td>
					<td id="tel<?php echo $listfree[$k][0]; ?>"><?php echo $listfree[$k][2]; ?></td>
					<td>
						<input type='button' id='signup' class='btn btn-sm btn-success' value='Записаться на прием' onclick="signup(<?php echo $kod_uslugi.",".$_SESSION['kod_operatora'].",".$listfree[$k][0].",'".$_POST['dtpicker']."'"; ?>); return false;" />
					</td>
				</tr>
		<?php
			}
		?>
		</tbody>
	</table>
	</div>
	<?php
	}
}
?>

<script>
	$("#emptyfilterbtn").click(function() {	// Сбросить фильтр
		$('#$optservid[value=-1]').attr('selected','selected');
		$('#dtpicker').dtpicker('reset');
		$('#dtpicker').attr('disabled', true);
		$('#dtpicker').val('');
	});
	$('#kod_uslugi').on('change', function() {
		$('#dtpicker').removeAttr("disabled");
	});
	$('#dtpicker').dtpicker({
	  format:'d.m.Y H:i',
	  lang:'ru',
	  <?php
	  if($post_true)
	  {
		  echo "startDate:'".$field_datetime->format('d.m.Y')."',"; ;
		  echo "defaultTime:'".$field_datetime->format('H:i')."',";
	  }
	 else
		  echo "startDate:'".date('d.m.Y')."',";
	  ?>
	  minDate:'<?php echo date('d.m.Y'); ?>',
	  minTime:'09:00',
	  maxTime:'18:00',
	  step:15,
	});
	$.dtpicker.setLocale('ru');
</script>

<p>&nbsp;</p>

<?php
$dbsql = "select a.kod_zapisi, a.data_vremja_zapisi, d.usluga, b.fio_operatora, b.tel, a.status_zapisi, 
		(CASE WHEN a.cena=0 or a.status_zapisi<3 THEN c.predv_stoimost ELSE a.cena END) as predv_stoimost 
		from zapisi as a 
		inner join uslugi_sotrudnikov as c on a.kod_uslugi_sotrudnika=c.kod_uslugi_sotrudnika 
		inner join operatory as b on c.kod_operatora=b.kod_operatora 
		inner join uslugi as d on c.kod_uslugi=d.kod_uslugi 
		where a.kod_operatora=".$_SESSION['kod_operatora']." 
		order by a.data_vremja_zapisi desc";
		
$zapisi_klientov = dbselec($dbsql);
$aCnt = count($zapisi_klientov);
if($aCnt < 1)
	echo '<legend class="text-left"><b>Еще нет записей на прием</b></legend>';
else
{
	echo '<legend class="text-left"><b>Мои записи на прием</b></legend>';
?>
<div class="row" style="width:100%; overflow-x:scroll;">
<table class="text-left table table-bordered table-striped table-hover table-responsive" id="print_table">
	<thead>
		<tr>
			<th>
				Дата/Время
			</th>
			<th>
				Услуга
			</th>
			<th>
				ФИО парикмахера
			</th>
			<th>
				Телефон мастера
			</th>
			<th>
				Статус
			</th>			
			<th>
				Цена, руб.
			</th>
			<th>
				Действие
			</th>
		</tr>
	</thead>
	<tbody>
<?php
		$date_now = new DateTime();
		for($k=0; $k<$aCnt; $k++)
		{
?>
		<tr>
			<td>
				<?php echo $zapisi_klientov[$k][1]; ?>
			</td>
			<td>
				<?php echo $zapisi_klientov[$k][2]; ?>
			</td>
			<td>
				<?php echo $zapisi_klientov[$k][3]; ?>
			</td>
			<td>
				<?php echo $zapisi_klientov[$k][4]; ?>
			</td>
			<td>
				<?php 
				switch($zapisi_klientov[$k][5])
				{
					case 1:echo "Забронирована"; break;
					case 2:echo "Отменена"; break;
					case 3:echo "Оказана"; break;
					default: echo "Ошибка"; break;
				}
				?>
			</td>
			<td>
				<?php echo $zapisi_klientov[$k][6]; ?>
			</td>
			<td>
				<?php 
				$cd = DateTime::createFromFormat('Y-m-d H:i:s', $zapisi_klientov[$k][1]);
				if($cd < $date_now)
					echo "Запись просрочена";
				else if($zapisi_klientov[$k][5] == 1) 
					echo "<button id='btnDel' name='btnDel' class='btn btn-sm btn-warning' onclick='cancel(".$zapisi_klientov[$k][0].");'>Отменить запись</button>"; 
				?>
			</td>
		</tr>
<?php
		}
?>
	</tbody>
</table>
</div>

<div class="row text-left" style="margin-top:10px;">
	<div class="col-md-12">
		<input type='button' id='btn' class='btn btn-primary' value='Экспортировать в Excel' onclick='f_tab_2_xls("print_table", "Записи в парикмахерскую"); return false;'>
	</div>
</div>

<script>
	function cancel(id)
	{
		if(!confirm("Вы уверены, что хотите отменить запись на прием?")) 
			return false;
		var datafromform = new FormData();
			
		datafromform.append("id", id);
			
		$.ajax({
			type: "POST",
			url: "dynamic/cancel.php",
			success: function (data) {
				if(data != false){
					alert('Ваша запись успешно отменена');
					window.location.href = window.location.href;
				}
				else
				{
					alert('В скрипте ошибка');
				}
				return false;
			},
			error: function (error) {
				alert('Ошибка какая-то');
				return false;
			},
			async: true,
			data: datafromform,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 40000
		});
				
		return false;
	}
</script>

<?php
}
?>