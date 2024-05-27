<?php
	if(isset($_POST['filterbutton'])&&isset($_POST['kod_uslugi'])&&isset($_POST['dtpicker'])&&$_POST['dtpicker']!="")
	{
		$post_true = true;
		
		$kod_uslugi = trim($_POST['kod_uslugi']); // 	1
		$field_datetime = DateTime::createFromFormat('d.m.Y H:i', trim($_POST['dtpicker']));
		
		$den = $field_datetime->format('N'); // День недели 	4
		$field_datetime_sql = $field_datetime->format('Y-m-d H:i'); //	'2021-06-11 15:00'
		$timeinsql = $field_datetime->format('H:i'); //	'15:00'
		
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
	
	echo "<h1>Регистрация клиентов на прием</h1>";
?>
<div id="boxmess"></div>
<p>&nbsp;</p>
<form class="form-horizontal text-left" method="post" name="regForm" action="ruchnoy_zakaz" enctype='multipart/form-data'>
<fieldset>
<legend><b>Зарегистрировать клиента на прием</b></legend>
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
	<p>&nbsp;</p>
	<div class="row">
		<h4 class="text-left">Надо зарегистрировать клиента или выбрать:</h4>		
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-md-5">
					<input type="radio" id="rad1" name="rad" checked />
					<label for="rad1">Выбрать клиента</label>
				</div>
				<div class="col-md-7">
					<select id="id_client" name="id_client" class="form-control">
					<?php
						echo "<option value='-1'>Выбрать клиента</option>";
						$clients = dbselec("select kod_operatora, concat(fio_operatora, ' ', tel) from operatory where type=3 order by fio_operatora asc");
						$dbvalcnt = count($clients);
						for($i=0; $i<$dbvalcnt; $i++)
							echo "<option value='".$clients[$i][0]."' ".($clients[$i][0]==$id_client?"selected":"").">".$clients[$i][1]."</option>";
					?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-md-5">
					<input type="radio" id="rad2" name="rad" />
					<label for="rad2">Надо зарегистрировать нового клиента</label>
				</div>
				<div class="col-md-7">
					<div class="row"><input type="hidden" id="ret" name="ret" /><input id="fio_operatoranew" name="fio_operatoranew" type="text" placeholder="ФИО" class="form-control input-md"></div>
					<div class="row"><input id="phonenew" name="phonenew" type="text" placeholder="Телефон" class="form-control input-md masktel"></div>
					<div class="row"><input id="loginnew" name="loginnew" type="login_operatora" placeholder="Email" class="form-control input-md"></div>
					<div class="row"><input id="new_parol_operatora" name="new_parol_operatora" type="text" placeholder="Пароль" class="form-control input-md" readonly ></div>
				</div>
			</div>
		</div>
	</div>
	<p>&nbsp;</p>
	<div class="row">
		<h4 class="text-left">Записи на прием</h4>
		<table class="text-left table table-bordered table-striped table-hover table-responsive">
			<thead>
				<tr>
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
						<td id="fio_operatora<?php echo $listfree[$k][0]; ?>"><?php echo $listfree[$k][1]; ?></td>
						<td id="tel<?php echo $listfree[$k][0]; ?>"><?php echo $listfree[$k][2]; ?></td>
						<td>
							<input type='button' id='signup' class='btn btn-sm btn-success' value='Осуществить запись' onclick="signup(<?php echo $kod_uslugi.",".$listfree[$k][0].",'".$_POST['dtpicker']."'"; ?>); return false;" />
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
	$(".masktel").mask("+7(999)999-99-99");
	
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
	  startDate:'<?php echo date('d.m.Y'); ?>',
	  minDate:'<?php echo date('d.m.Y'); ?>',
	  minTime:'09:00',
	  maxTime:'18:00',
	  step:15,
	});
	
	function signup(kod_uslugi, kod_operatora, datetime) // Осуществить запись
	{
		var fio_operatoranew = $('#fio_operatoranew').val();
		var phonenew = $('#phonenew').val();
		var loginnew = $('#loginnew').val();
		var chtorad;
		if($('#rad2').prop("checked"))
			chtorad = 1;
		else
			chtorad = 0;
		var id_client = -1;
		
		if($('#rad1').prop("checked")) 
		{
			id_client = $('#$optclientid:selected').val();
			if(id_client < 0)
			{
				alert('Выбрать клиента');
				return;
			}
		}
	
		var datafromform = new FormData();
			
		datafromform.append("kod_uslugi", kod_uslugi);
		datafromform.append("id_client", id_client);
		datafromform.append("kod_operatora", kod_operatora);
		datafromform.append("datetime", datetime);
		datafromform.append("fio_operatoranew", fio_operatoranew);
		datafromform.append("phonenew", phonenew);
		datafromform.append("loginnew", loginnew);
		datafromform.append("chtorad", chtorad);

		$.ajax({
			type: "POST",
				url: "dynamic/operator_register.php",
			success: function (data) {
				if(data != false){
					$('#boxmess').html(data);
					if(data == -3)
						alert('Уже есть в БД такой пользователь');
					else if(data == -4)
						alert('Ошибка при регистрации пользователя');
					else if(data == -1)
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
						alert('Клиент парикмахерской записан на прием '+datetime+' в парикмахерскую "'+usluga+'" к мастеру "'+fio_operatora+' ('+tel+')"');
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
