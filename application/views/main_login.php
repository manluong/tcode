<?=$status?>

<form name="form1" method="post" action="<?php echo base_url("main/login");?>">
  <p> Username:
    <input name="username" type="text" id="username">
    <br>
    Password:
    <input name="password" type="text" id="password">
  </p>
  <p>
    <input type="submit" name="Submit" value="Submit">
  </p>
</form>

