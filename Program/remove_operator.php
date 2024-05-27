<?php
if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id']>0)
{
	remfromtbl("operatory", "kod_operatora", $_GET['id']);
	redir2('show_operator');
}
else
	redir2('not_found');

?>
