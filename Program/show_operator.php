<?php
	$type = isset($_POST['type']) ? trim($_POST['type']) : '';
	$fio_operatora = isset($_POST['fio_operatora']) ? trim($_POST['fio_operatora']) : '';
	$login_operatora = isset($_POST['login_operatora']) ? trim($_POST['login_operatora']) : '';
	$add_op = isset($_POST['add_op']) ? trim($_POST['add_op']) : '';
	
	$dbsql = "select kod_operatora, fio_operatora, tel, login_operatora, '***', type, pss_op, add_op from operatory where 1";	
	if($type > 0)
		$dbsql .= " and type=$type";
	if($fio_operatora != '')
		$dbsql .= " and fio_operatora like '%$fio_operatora%'";
	if($login_operatora != '')
		$dbsql .= " and login_operatora like '%$login_operatora%'";
	if($add_op != '')
		$dbsql .= " and add_op like '%$add_op%'";
	$dbsql .= " order by type, fio_operatora asc";
	//echo $dbsql;
	$show_operator = dbselec($dbsql);
?>
<h1>Просмотреть и отредактировать пользователей</h1>
<p>&nbsp;</p>
<form class="form-horizontal text-left" method="post" action="show_operator" enctype='multipart/form-data'>
<fieldset>
<legend>Отфильтровать данные по параметрам</legend>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="type">Тип оператора</label>
				<div class="col-md-7">
					<select id="type" name="type" class="form-control">
						<?php
							echo "<option value='0'>Любой</option>";
							echo "<option value='1' ".($type==1 ? "selected" : "").">Суперпользователь</option>";
							echo "<option value='2' ".($type==2 ? "selected" : "").">Сотрудника салона</option>";
							echo "<option value='3' ".($type==3 ? "selected" : "").">Клиент парикмахерской</option>";
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="fio_operatora">ФИО</label>  
				<div class="col-md-7">
					<input id="fio_operatora" name="fio_operatora" type="text" placeholder="ФИО" class="form-control input-md" value="<?php if($fio_operatora != "") echo $fio_operatora; ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="login_operatora">Email</label>
				<div class="col-md-7">
					<input id="login_operatora" name="login_operatora" type="text" placeholder="Email" class="form-control input-md" value="<?php if($login_operatora != "") echo $login_operatora; ?>">
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="add_op">Адрес</label>  
				<div class="col-md-7">
				<input id="add_op" name="add_op" type="text" placeholder="Адрес" class="form-control input-md" value="<?php if($add_op != "") echo $add_op; ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="filterbutton">Поиск данных</label>
				<div class="col-md-7">
					<button id="filterbutton" name="filterbutton" class="btn btn-primary">Фильтрация данных</button>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label col-md-4" for="emptyfilterbtn">Сброс параметров</label>
				<div class="col-md-7">
					<button id="emptyfilterbtn" name="emptyfilterbtn" class="btn btn-primary" onclick="return false;">Сброс параметров</button>
				</div>
			</div>
		</div>
	</div>
</div>
</fieldset>
</form>
	
<p class="text-right"><a class="btn btn-primary" href="operatoradd" role="button">Вставить новую запись в БД</a></p>
<table class="text-left table table-bordered table-striped table-hover table-responsive" id="print_table">
	<thead>
		<tr>
			<th>
				ФИО
			</th>
			<th>
				Телефон
			</th>
			<th>
				Email
			</th>
			<th>
				Пароль
			</th>
			<th>
				Тип
			</th>
			<th colspan="2">
				Действия
			</th>
		</tr>
	</thead>
	<tbody>
<?php
	$dbvalcntR = count($show_operator);
	for($i=0; $i<$dbvalcntR; $i++)
	{
?>
		<tr>
			<td>
				<?php echo $show_operator[$i][1]; ?>
			</td>
			<td>
				<?php echo $show_operator[$i][6]; ?>
			</td>
			<td>
				<?php echo $show_operator[$i][3]; ?>
			</td>
			<td>
				<?php echo $show_operator[$i][4]; ?>
			</td>
			<td>
				<?php echo ($show_operator[$i][5] == 1 ? "Суперпользователь" : ($show_operator[$i][5] == 2 ? "Сотрудника салона" : "Клиент парикмахерской")); ?>
			</td>
			<td>
				<?php echo "<a href='".USERV_PREF."edit_operator?id=".$show_operator[$i][0]."'>редактировать</a>" ?>
			</td>
			<td>
				<?php if($show_operator[$i][5] != 1) echo "<a href='".USERV_PREF."remove_operator?id=".$show_operator[$i][0]."' onclick='if (!confirm(\"Вы уверены, что хотите удалить запись?\")) return false;'>удалить</a>" ?>
			</td>

		</tr>
<?php
	}
?>
	</tbody>
</table>

<div class="row text-left">
	<div class="col-md-12">
		<input type='button' id='btn' class='btn btn-primary' value='Экспортировать в Excel' onclick='f_tab_2_xls("print_table", "Данные"); return false;'>
	</div>
</div>

<script>
	$("#emptyfilterbtn").click(function() {
		$('#type  option[value=0]').attr('selected','selected');
		$('#fio_operatora').val('');
		$('#login_operatora').val('');
		$('#add_op').val('');
	});
</script>