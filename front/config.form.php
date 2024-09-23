<?php

include ('../../../inc/includes.php');

Session::checkRight("profile", READ);

$config = new PluginRolecategoriesitilConfig();
if (isset($_POST["update"])) {
   $config->updated($_POST);
    Html::back();
}
