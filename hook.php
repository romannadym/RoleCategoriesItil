<?php

/**
* -------------------------------------------------------------------------
* RoleCategoriesItil plugin for GLPI
* Copyright (C) 2024 by the RoleCategoriesItil Development Team.
* -------------------------------------------------------------------------
*
* MIT License
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*
* --------------------------------------------------------------------------
*/

/**
* Plugin install process
*
* @return boolean
*/
function plugin_rolecategoriesitil_install()
{
  global $DB;
  $version = plugin_version_rolecategoriesitil();
  //создать экземпляр миграции с версией
  $migration = new Migration($version['version']);
  //Create table only if it does not exists yet!
  if (!$DB->tableExists('glpi_plugin_rolecategoriesitil_configs')) {
    //table creation query
    $query = 'CREATE TABLE glpi_plugin_rolecategoriesitil_configs (
      id INT(11) NOT NULL AUTO_INCREMENT,
      profile_id INT(11) NOT NULL,
      itilcategory_id  INT(11) NOT NULL,
      active  INT(1) NOT NULL DEFAULT 0,
      date_mod TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY  (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC';

      $DB->queryOrDie($query, $DB->error());
    }
    if (Session::haveRight("profile", READ)) {
      // Создаем экземпляр класса Profile
      $profile = new Profile();
      $profiles = $profile->find();
    }
    if (Session::haveRight('itilcategory', READ)) {
      // Создаем экземпляр класса ItilCategory
      $itilCategory = new ItilCategory();

      // Получаем список категорий
      // Метод find возвращает массив категорий, удовлетворяющих условиям
      $categories = $itilCategory->find();

    }

    foreach($profiles AS $pid => $pdata)
    {
      foreach($categories AS $cid => $cdata)
      {
        $DB->insert('glpi_plugin_rolecategoriesitil_configs',[
          'profile_id'=>$pid,
          'itilcategory_id'=>$cid
        ]);
      }
    }
    //execute the whole migration
    $migration->executeMigration();
    return true;
  }

  /**
  * Plugin uninstall process
  *
  * @return boolean
  */
  function plugin_rolecategoriesitil_uninstall()
  {
    global $DB;
    $tables = [
        'configs'
      ];
    foreach ($tables as $table) {
      $tablename = 'glpi_plugin_rolecategoriesitil_' . $table;
      //Create table only if it does not exists yet!
      if ($DB->tableExists($tablename)) {
        $DB->queryOrDie(
          "DROP TABLE `$tablename`",
          $DB->error()
        );
      }
    }
    return true;
  }
 function plugin_rolecategoriesitil_hook_init()
 {
   PluginRolecategoriesitilConfig::init();
   return true;
 }
