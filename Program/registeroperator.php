<h1>Зарегистрироваться в ИС клиента в системе</h1>
<?php
	if(isset($_POST['registeroperator']))
	{
		$fio_operatora = trim($_POST['fio_operatora']);
		$login_operatora = trim($_POST['login_operatora']);
		$pss = trim($_POST['parol_operatora']);
		$tel = trim($_POST['tel']);
		
		$user = seluserfromfield("login_operatora", $login_operatora);
		if(!is_null($user) && is_array($user) && count($user) > 0)
		{
			echo "<div class='alert alert-danger'>Оператор с таким логином уже есть. Выберите другой</div>";
		}
		else
		{
			$dbvals = array("'$fio_operatora'", "'$login_operatora'", "'$pss'", "'$tel'");
			$id_inc = puttodb("operatory", "fio_operatora, login_operatora, parol_operatora, tel", $dbvals);
			if($id_inc==false)
			{
				echo "<div class='alert alert-danger'>Ошибка при регистрации пользователя</div>";
			}
			else
			{
				unset($_POST['registeroperator']);
				unset($_POST['fio_operatora']);
				unset($_POST['login_operatora']);
				unset($_POST['parol_operatora']);
				unset($_POST['tel']);
				echo "<div class='alert alert-success'>Позравляем, вы успешно зарегистрировались в системе! Теперь можно <a href='".USERV_PREF."userLogin'>Авторизоваться в ИС</a></div>";
			}
		}
	}
?>

<form class="form-horizontal" action="registeroperator" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<fieldset>

<legend></legend>

<div class="form-group">
  <label class="col-md-4 control-label" for="fio_operatora">Требуется ввод имени</label>  
  <div class="col-md-8">
  <input id="fio_operatora" name="fio_operatora" type="text" placeholder="Имя" class="form-control input-md" required="" <?php if (isset($_POST['registeroperator'])) echo "value='".$_POST['fio_operatora']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="login_operatora">Требуется ввод логина</label>  
  <div class="col-md-8">
  <input id="login_operatora" name="login_operatora" type="login_operatora" placeholder="Email" class="form-control input-md" required="" <?php if (isset($_POST['registeroperator'])) echo "value='".$_POST['login_operatora']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="parol_operatora">Требуется ввод пароля</label>  
  <div class="col-md-8">
  <input id="parol_operatora" name="parol_operatora" type="text" placeholder="Пароль" class="form-control input-md" required="" <?php if (isset($_POST['registeroperator'])) echo "value='".$_POST['parol_operatora']."'"; ?>>
  <span class="help-block">поле является обязательным</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="tel">Требуется ввод телефона</label>  
  <div class="col-md-8">
  <input id="tel" name="tel" type="text" placeholder="Телефон" class="form-control input-md masktel" <?php if (isset($_POST['registeroperator'])) echo "value='".$_POST['tel']."'"; ?>>
  <span class="help-block">поле не является обязательным</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="registeroperator">Требуется подтвердить ввод</label>
  <div class="col-md-4">
    <button id="registeroperator" name="registeroperator" class="btn btn-primary">Зарегистрироваться в системе</button>
  </div>
</div>

</fieldset>
</form>
<script>
    $(".masktel").mask("+7(999)999-99-99");
</script>