<?php
	if(isset($_POST['filterbutton']))
	{
		$post_true = true;
		
		$status_zapisi = trim($_POST['status_zapisi']);
		$kod_uslugi = trim($_POST['kod_uslugi']);
		$id_client = trim($_POST['id_client']);
		$kod_operatora = trim($_POST['kod_operatora']);
		$dtfrom = trim($_POST['dtfrom']);
		$dtto = trim($_POST['dtto']);
		
		$last = " where 1 ";
		if($status_zapisi > 0)
			$last .= " and a.status_zapisi=$status_zapisi";
		if($kod_uslugi > 0)
			$last .= " and c.kod_uslugi=$kod_uslugi";
		if($id_client > 0)
			$last .= " and e.kod_operatora=$id_client";
		if($_SESSION['tip_operatora']==1)
		{
			if($kod_operatora > 0)
				$last .= " and b.kod_operatora=$kod_operatora";
		}
		else if($_SESSION['tip_operatora']==2)
			$last .= " and b.kod_operatora=".$_SESSION['kod_operatora'];
		if($dtfrom != "")
			$last .= " and a.data_vremja_zapisi >= '$dtfrom 00:00'";
		if($dtto  != "")
			$last .= " and a.data_vremja_zapisi <= '$dtto 23:59'";		
		
		$dbsql = "select a.kod_zapisi, a.data_vremja_zapisi, d.usluga, e.fio_operatora, e.tel, b.fio_operatora, b.tel, a.status_zapisi, 
				(CASE WHEN a.cena=0 or a.status_zapisi<3 THEN c.predv_stoimost ELSE a.cena END) as predv_stoimost 
				from zapisi as a 
				inner join uslugi_sotrudnikov as c on a.kod_uslugi_sotrudnika=c.kod_uslugi_sotrudnika 
				inner join operatory as b on c.kod_operatora=b.kod_operatora 
				inner join uslugi as d on c.kod_uslugi=d.kod_uslugi 
				inner join operatory as e on a.kod_operatora=e.kod_operatora 
				$last 
				order by a.data_vremja_zapisi asc";
		//echo $dbsql;
		$listzakazi = dbselec($dbsql);
		$aCnt = count($listzakazi);
	}
	else
		$post_true = false;
	
	$cntall = 0;
	$cntst1 = 0;
	$cntst2 = 0;
	$cntst3 = 0;
	$pp1 = 0;
	$pp2 = 0;
	$pp3 = 0;
	$servarr = array();
	
	echo "<h1>Записи в парикмахерскую</h1>";
?>

<div class="modal fade" id="infomodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header row">
			<div class="col-md-11">
				<h3 class="modal-title" id="titlemodal">Отредактировать запись</h3>
			</div>
			<div class="col-md-1">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			</div>
		  </div>
		  <div class="modal-body text-left" id="modal_body">
			<input type="hidden" name="factichid" id="factichid" />
			<div class="row mrow">
				<p>Стоимость, руб.:</p>
				<input style="width:100%" type="number" min="0" max="100000" step="10" id="cena" name="cena" placeholder="Фактическая цена услуги" required />
			</div>
			<div class="row mrow">
				<p>Статус:</p>
				<select id="fact_status_zapisi" name="fact_status_zapisi" class="form-control">
					<option value='1'>Забронирована</option>
					<option value='2'>Отменена</option>
					<option value='3'>Оказана</option>
				</select>
			</div>
			<div class="row mrow text-center">
				<button id="editapp" type="button" class="btn btn-primary">Сохранение параметров</button>
			</div>
		  </div>
		</div>
	  </div>
</div>
<div id="boxmess"></div>

<p>&nbsp;</p>

<form class="form-horizontal text-left" method="post" name="regForm" action="spisok_zapisey" enctype='multipart/form-data'>
<fieldset>
<legend><b>Отфильтровать записи</b></legend>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="kod_uslugi">Выбрать услугу</label>
				<div class="col-md-7">
					<select id="kod_uslugi" name="kod_uslugi" class="form-control">
					<?php
						echo "<option value='-1'>Выбрать услугу</option>";
						$uslugi = dbselec("select kod_uslugi, usluga from uslugi order by usluga asc");
						$dbvalcnt = count($uslugi);
						for($i=0; $i<$dbvalcnt; $i++)
						{
							$servarr[$uslugi[$i][1]] = 0;
							echo "<option value='".$uslugi[$i][0]."' ".($uslugi[$i][0]==$kod_uslugi?"selected":"").">".$uslugi[$i][1]."</option>";
						}
					?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="id_client">Выбрать клиента</label>
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
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="dtfrom">Запись число от</label>  
				<div class="col-md-7">
				<input id="dtfrom" name="dtfrom" type="date" placeholder="Запись число от" class="form-control input-md" value="<?php if($dtfrom != "") echo $dtfrom; ?>">
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="dtto">Запись число до</label>  
				<div class="col-md-7">
				<input id="dtto" name="dtto" type="date" placeholder="Запись число до" class="form-control input-md" value="<?php if($dtto != "") echo $dtto; ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="status_zapisi">Надо выбрать статус</label>
				<div class="col-md-7">
					<select id="status_zapisi" name="status_zapisi" class="form-control">
						<option value='-1'>Надо выбрать статус</option>
						<option value='1' <?php if($status_zapisi==1) echo selected ?>>Забронирована</option>
						<option value='2' <?php if($status_zapisi==2) echo selected ?>>Отменена</option>
						<option value='3' <?php if($status_zapisi==3) echo selected ?>>Оказана</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
		<?php
		if($_SESSION['tip_operatora']==1)
		{
		?>
			<div class="form-group">
				<label class="control-label col-md-4" for="kod_operatora">Надо выбрать мастера</label>
				<div class="col-md-7">
					<select id="kod_operatora" name="kod_operatora" class="form-control">
					<?php
						echo "<option value='-1'>Надо выбрать мастера</option>";
						$operatory = dbselec("select kod_operatora, concat(fio_operatora, ' ', tel) from operatory where type=2 order by fio_operatora asc");
						$dbvalcnt = count($operatory);
						for($i=0; $i<$dbvalcnt; $i++)
							echo "<option value='".$operatory[$i][0]."' ".($operatory[$i][0]==$kod_operatora?"selected":"").">".$operatory[$i][1]."</option>";
					?>
					</select>
				</div>
			</div>
		<?php
		}
		?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="filterbutton"></label>
				<div class="col-md-7">
					<button id="filterbutton" name="filterbutton" class="btn btn-primary">Фильтрация данных записи</button>
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

<p>&nbsp;</p>
<?php
if(isset($aCnt) && $aCnt > 0)
{
	echo '<legend class="text-left"><b>Записи в парикмахерскую</b></legend>';
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
				Клиент парикмахерской
			</th>
			<th>
				Мастер
			</th>
			<th>
				Статус
			</th>			
			<th>
				Цена, руб.
			</th>
			<th>
				Действия
			</th>
		</tr>
	</thead>
	<tbody>
<?php		
		for($k=0; $k<$aCnt; $k++)
		{
			$cntall++;
?>
		<tr>
			<td>
				<?php echo str_replace(" ", "<br>", $listzakazi[$k][1]); ?>
			</td>
			<td>
				<?php $servarr[$listzakazi[$k][2]]++; echo $listzakazi[$k][2]; ?>
			</td>
			<td>
				<?php echo $listzakazi[$k][3]."<br>".$listzakazi[$k][4]; ?>
			</td>
			<td>
				<?php echo $listzakazi[$k][5]."<br>".$listzakazi[$k][6] ?>
			</td>
			<td>
				<?php 
				switch($listzakazi[$k][7])
				{
					case 1: $cntst1++; $pp1 += $listzakazi[$k][8]; echo "Забронирована"; break;
					case 2: $cntst2++; $pp2 += $listzakazi[$k][8]; echo "Отменена"; break;
					case 3: $cntst3++; $pp3 += $listzakazi[$k][8]; echo "Оказана"; break;
					default: echo "Ошибка"; break;
				}
				?>
			</td>
			<td>
				<?php echo $listzakazi[$k][8]; ?>
			</td>
			<td>
				<?php if($listzakazi[$k][7] < 3) echo "<button id='btnDel' name='btnDel' class='btn btn-sm btn-info' onclick='edit(".$listzakazi[$k][0].",".$listzakazi[$k][8].",".$listzakazi[$k][7].");'>Редактировать</button>"; ?>
			</td>
		</tr>
<?php
		}
		$allSum = $pp1 + $pp2 + $pp3;
?>
		<tr>
			<td colspan="7">
<?php
				echo "<h4>Статистика:</h4>";
				echo "<p>Всего услуг: $cntall (100%). Сумма: $allSum руб.</p>";
				echo "<p>&nbsp;&nbsp;В статусе \"Забронирована\": $cntst1 (".ceil(($cntst1/$cntall)*100)."%). Сумма: $pp1 руб. (".ceil(($pp1/$allSum)*100)."%)</p>";
				echo "<p>&nbsp;&nbsp;В статусе \"Отменена\": $cntst2 (".ceil(($cntst2/$cntall)*100)."%). Сумма: $pp2 руб. (".ceil(($pp2/$allSum)*100)."%)</p>";
				echo "<p>&nbsp;&nbsp;В статусе \"Оказана\": $cntst3 (".ceil(($cntst3/$cntall)*100)."%). Сумма: $pp3 руб. (".ceil(($pp3/$allSum)*100)."%)</p>";
				echo "<p></p>";
				echo "<p>Число оказанных видов услуг:</p>";
				foreach ($servarr as $serv => $sCnt)
					echo "<p>&nbsp;&nbsp;$serv: $sCnt (".ceil(($sCnt/$cntall)*100)."%)<p>";
?>
			</td>
		</tr>
	</tbody>
</table>
</div>

<div class="row text-left" style="margin-top:10px;">
	<div class="col-md-12">
		<input type='button' id='btn' class='btn btn-primary' value='Экспортировать в Excel' onclick='f_tab_2_xls("print_table", "Записи в парикмахерскую"); return false;'>
	</div>
</div>

<script>
	$("#emptyfilterbtn").click(function() {	// Сбросить фильтр
		$('#$optservid[value=-1]').attr('selected','selected');
		$('#$optclientid[value=-1]').attr('selected','selected');
		$('#kod_operatora option[value=-1]').attr('selected','selected');
		$('#dtfrom').val('');
		$('#dtto').val('');
	});
	
	function edit(id, predv_stoimost, status_zapisi)
	{
		$('#infomodal').on('show.bs.modal', function (event) {
			$('#factichid').val(id);
			$('#cena').val(predv_stoimost);
			$('#fact_status_zapisi').val(status_zapisi);
			$("#fact_status_zapisi").focus();
		});
		$('#infomodal').modal('toggle');
	}
	
	$('#editapp').click(function() {
		var factichid = $('#factichid').val().trim();
		var cena = $('#cena').val().trim();
		var fact_status_zapisi = $('#fact_status_zapisi').val().trim();
		
		if(factichid < 0 || fact_status_zapisi < 1)
			return;
		
		var datafromform = new FormData();
			
		datafromform.append("factichid", factichid);
		datafromform.append("cena", cena);
		datafromform.append("fact_status_zapisi", fact_status_zapisi);
		
		$.ajax({
			type: "POST",
			url: "dynamic/edit.php",
			success: function (data) {
				if(data != false){
					alert('Запись успешно отредактирована');
					location.reload();
				}
				else
				{
					alert('В скрипте ошибка');
				}
				$('#infomodal').modal('hide');
				return false;
			},
			error: function (error) {
				alert('Ошибка какая-то');
				$('#infomodal').modal('hide');
				return false;
			},
			async: true,
			data: datafromform,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 40000
		});
		$('#infomodal').modal('hide');
		return false;
	});
</script>

<?php
}
?>