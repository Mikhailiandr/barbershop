<h1><a href="<?php echo USERV_PREF.'show_operator'; ?>"><-- Назад</a>&nbsp;&nbsp;Зарегистрировать оператора</h1>
<?php
	if(isset($_POST['operatoradd']))
	{
		$fio_operatora = $_POST['fio_operatora']==""?'null':$_POST['fio_operatora'];
		$login = $_POST['login']==""?'null':$_POST['login'];
		$pss = $_POST['parol_operatora']==""?'null':$_POST['parol_operatora'];
		$telefonniynomer = $_POST['telephone']==""?'null':$_POST['telephone'];
		$type = $_POST['type']==""?'null':$_POST['type'];
		$pss_op = $_POST['pss_op']==""?'null':$_POST['pss_op'];
		$add_op = $_POST['add_op']==""?'null':$_POST['add_op'];
		
		$user = seluserfromfield("login_operatora", $login);
		if(count($user) > 0)
		{
			echo "<div class='alert alert-danger'>Оператор с таким логином уже есть. Выберите другой</div>";
		}
		else
		{
			$hashval = md5($gethash(10));		

			$dbvals = array("'$fio_operatora'", "'$login'", "'$pss'", "'$telefonniynomer'", "'$hashval'", "$type", "'$pss_op'", "'$add_op'");
			$id_inc = puttodb("operatory", "fio_operatora, login_operatora, parol_operatora, tel, hash, type, pss_op, add_op", $dbvals);
			if($id_inc==false)
			{
				echo "<div class='alert alert-danger'><a href='".USERV_PREF."operatoradd'><span><-- </span> Вернуться назад</a>&nbsp;&nbsp;Ошибка регистрации оператора</div>";
			}
			else
			{
				unset($_POST['operatoradd']);
				unset($_POST['fio_operatora']);
				unset($_POST['login']);
				unset($_POST['parol_operatora']);
				unset($_POST['telephone']);
				unset($_POST['type']);
				unset($_POST['pss_op']);
				unset($_POST['add_op']);
				echo "<div class='alert alert-success'><a href='".USERV_PREF."show_operator'><span><-- </span> Назад к списку пользователей</a>&nbsp;&nbsp;Оператор зарегистрирован</div>";
			}
		}
	}
?>

<form class="form-horizontal" action="operatoradd" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<fieldset>

<legend></legend>

<div class="form-group">
  <label class="col-md-4 control-label" for="fio_operatora">Введите ФИО</label>  
  <div class="col-md-8">
  <input id="fio_operatora" name="fio_operatora" type="text" placeholder="ФИО" class="form-control input-md" required="" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['fio_operatora']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="pss_op">Введите паспорт</label>  
  <div class="col-md-8">
  <input id="pss_op" name="pss_op" type="number" min="1000000000" step="1" placeholder="Паспорт" class="form-control input-md" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['pss_op']."'"; ?>>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add_op">Введите адрес</label>  
  <div class="col-md-8">
  <input id="add_op" name="add_op" type="text" placeholder="Адес" class="form-control input-md" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['add_op']."'"; ?>>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="login">Требуется ввод логина</label>  
  <div class="col-md-8">
  <input id="login" name="login" type="login_operatora" placeholder="Email" class="form-control input-md" required="" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['login']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="parol_operatora">Требуется ввод пароля</label>  
  <div class="col-md-8">
  <input id="parol_operatora" name="parol_operatora" type="text" placeholder="Пароль" class="form-control input-md" required="" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['parol_operatora']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="telephone">Требуется ввод телефона</label>  
  <div class="col-md-8">
  <input id="telephone" name="telephone" type="text" placeholder="Телефон" class="form-control input-md masktel" required="" <?php if (isset($_POST['operatoradd'])) echo "value='".$_POST['telephone']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="type">Тип оператора</label>
  <div class="col-md-8">
    <select id="type" name="type" class="form-control">
	<?php
		echo "<option value='3' ".($_POST['type']==3?"selected":"").">Клиент парикмахерской</option>";
		echo "<option value='2' ".($_POST['type']==2?"selected":"").">Сотрудника салона</option>";
		echo "<option value='1' ".($_POST['type']==1?"selected":"").">Суперпользователь</option>";		
	?>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="operatoradd">Требуется подтвердить ввод</label>
  <div class="col-md-4">
    <button id="operatoradd" name="operatoradd" class="btn btn-primary">Зарегистрировать нового оператора</button>
  </div>
</div>

</fieldset>
</form>

<script>
	$(".masktel").mask("+7(999)999-99-99");
</script>