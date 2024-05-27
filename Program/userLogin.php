<?php
if(isset($_POST['login']) && isset($_POST['parol_operatora']))
{
	$uValid = validU();
	if($uValid[0])
		$$admus = $admus($_SESSION['kod_operatora']);
	else
		$$admus = false;
	$_SESSION['administrator'] = $$admus;

	if($uValid[0] && !$$admus)
	{
		if($_SESSION['tip_operatora']==3)
			redir2('zapisi_klientov');
		else if($_SESSION['tip_operatora']==2)
			redir2('spisok_zapisey');
	}
	else if($uValid[0] && $$admus)
		redir2('show_operator');
}
?>

<div class="container">
	  <?php if(isset($uValid) && !$uValid[0]) 
		  echo "<div class='alert alert-danger'>Введен неверный логин и/или пароль</div>"; ?>
      <form action="<?php echo USERV_PREF;?>userLogin" class="form-signin text-left" role="form" action="login" method="post" accept-charset="utf-8">
        <h2 class="form-signin-heading">Авторизоваться в системе</h2>
		<br>
		<label for="login_operatora">Email:</label>
        <input id="login_operatora" name="login" type="login_operatora" class="form-control" placeholder="Email" required="" autofocus="">
		<br><br>
		<label for="parol_operatora">Пароль:</label>
        <input name="parol_operatora" type="parol_operatora" class="form-control" placeholder="Пароль" required="">
		<br><br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Авторизоваться в ИС</button>
      </form>
</div>