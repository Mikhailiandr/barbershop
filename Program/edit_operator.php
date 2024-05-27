<div class="container-fluid">
	<div class="row">
<h1><a href="<?php echo USERV_PREF.'show_operator'; ?>"><-- Назад</a>&nbsp;&nbsp;Редактирование пользователя</h1>
<?php
	if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>0)
	{
		if(isset($_POST['edit_operator']))
		{
			$fio_operatora = $_POST['fio_operatora']==""?'null':$_POST['fio_operatora'];
			$login = $_POST['login']==""?'null':$_POST['login'];
			$pss = $_POST['parol_operatora']==""?'null':$_POST['parol_operatora'];
			$telefonniynomer = $_POST['telephone']==""?'null':$_POST['telephone'];
			$type = $_POST['type']==""?'null':$_POST['type'];
			$pss_op = $_POST['pss_op']==""?'null':$_POST['pss_op'];
			$add_op = $_POST['add_op']==""?'null':$_POST['add_op'];
			
			$dbvals = array("'$fio_operatora'", "'$telefonniynomer'", "'$login'", "'$pss'", "$type", "'$pss_op'", "'$add_op'");
		
			$ret = updateTable("operatory", "fio_operatora, tel, login_operatora, parol_operatora, type, pss_op, add_op", $dbvals, "kod_operatora", $_GET['id']);
			
			if($ret==false)
			{
				echo "<div class='alert alert-danger'><a href='".USERV_PREF."edit_operator?id=".$_GET['id']."'><span><-- </span> Назад к форме редактирования</a>&nbsp;&nbsp;Ошибка при изменении записи</div>";
			}
			else
			{
				echo "<div class='alert alert-success'><a href='".USERV_PREF."show_operator'><span><-- </span> Назад к списку пользователей</a>&nbsp;&nbsp;Запись успешно отредактирована</div>";
			}
		}
		else
		{
			$show_operator = dbselec("select kod_operatora, fio_operatora, tel, login_operatora, parol_operatora, type, pss_op, add_op from operatory where kod_operatora=".$_GET['id']);
			if($show_operator==false)
			{
				echo "<div class='alert alert-danger'><a href='".USERV_PREF."show_operator'><span><-- </span> Назад к списку пользователей</a>&nbsp;&nbsp;Пользователь по данному ID не найден</div>";
				return;
			}
			else
			{
				$fio_operatora = trim($show_operator[0][1]);
				$telefonniynomer = trim($show_operator[0][2]);
				$login = trim($show_operator[0][3]);
				$pss = trim($show_operator[0][4]);
				$type = trim($show_operator[0][5]);
				$pss_op= trim($show_operator[0][6]);
				$add_op = trim($show_operator[0][7]);
			}
		}
	}
	else
		redir2('not_found');
?>
<div class="col-md-12">
<form class="form-horizontal" action="edit_operator?id=<?php echo $_GET['id']; ?>" method="post" accept-charset="utf-8">
<fieldset>

<legend></legend>

<div class="form-group">
  <label class="col-md-4 control-label" for="fio_operatora">Введите ФИО</label>  
  <div class="col-md-8">
  <input id="fio_operatora" name="fio_operatora" type="text" placeholder="ФИО" class="form-control input-md" required="" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['fio_operatora']."'"; else echo "value='$fio_operatora'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="pss_op">Введите паспорт</label>  
  <div class="col-md-8">
  <input id="pss_op" name="pss_op" type="text" placeholder="Паспорт" class="form-control input-md" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['pss_op']."'"; else echo "value='$pss_op'"; ?>>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add_op">Введите адрес</label>  
  <div class="col-md-8">
  <input id="add_op" name="add_op" type="text" placeholder="Адрес" class="form-control input-md" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['add_op']."'"; else echo "value='$add_op'"; ?>>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="login">Требуется ввод логина</label>  
  <div class="col-md-8">
  <input id="login" name="login" type="text" placeholder="Email" class="form-control input-md" required="" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['login']."'"; else echo "value='$login'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="parol_operatora">Требуется ввод пароля</label>  
  <div class="col-md-8">
  <input id="parol_operatora" name="parol_operatora" type="text" placeholder="Пароль" class="form-control input-md" required="" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['parol_operatora']."'"; else echo "value='$pss'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="telephone">Требуется ввод телефона</label>  
  <div class="col-md-8">
  <input id="telephone" name="telephone" type="text" placeholder="Телефон" class="form-control input-md masktel" required="" <?php if (isset($_POST['edit_operator'])) echo "value='".$_POST['telephone']."'"; else echo "value='$telefonniynomer'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="type">Тип оператора</label>
  <div class="col-md-8">
    <select id="type" name="type" class="form-control">
	<?php
		echo "<option value='1' ".($type==1?"selected":"").">Суперпользователь</option>";
		echo "<option value='2' ".($type==2?"selected":"").">Сотрудника салона</option>";
		echo "<option value='3' ".($type==3?"selected":"").">Клиент парикмахерской</option>";
	?>
    </select>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="edit_operator">Требуется подтвердить ввод</label>
  <div class="col-md-4">
    <button id="edit_operator" name="edit_operator" class="btn btn-primary">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
  </div>
</div>

</fieldset>
</form>
</div>
</div>
</div>

<script>
	$(".masktel").mask("+7(999)999-99-99");
</script>