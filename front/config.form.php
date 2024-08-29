<?php

include ('../../../inc/includes.php');

Session::checkRight("profile", READ);

$config = new PluginRolecategoriesitilConfig();
if (isset($_POST["update"])) {

   $config->updated($_POST);
   //file_put_contents(GLPI_ROOT.'/tmp/buffer.txt',PHP_EOL.PHP_EOL. json_encode($_POST,JSON_UNESCAPED_UNICODE), FILE_APPEND);
   Html::back();
}
