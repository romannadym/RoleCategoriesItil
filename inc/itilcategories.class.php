<?php
class PluginRolecategoriesitilItilcategories
{
    /**
     * Хук для фильтрации категорий ITIL перед их отображением
     */
     static function init() {
       if (
         strstr($_SERVER['PHP_SELF'], "/ajax/getDropdownValue.php")
         && $_POST['itemtype'] == 'ITILCategory'
         )
       {
         $config = new PluginRolecategoriesitilConfig();
         $fields = $config->find(['profile_id'=>self::getUserProfileId(),'active'=>1]);
         $itil = Dropdown::getDropdownValue($_POST,FALSE);
         $itilMod = [];
         if(!isset($itil['results'][1]['children']))
         {
           return;
         }
         $itilChildren = $itil['results'][1]['children'];
         foreach($itilChildren AS $key => $value)
         {
           foreach ($fields as $f) {
            if($value['id'] == $f['itilcategory_id'])
            {
              $itilMod[] = $value;
            }
           }

         }
         $itil['results'][1]['children'] = $itilMod;
         $itil['count'] = count($itilMod);
         echo json_encode($itil);
         exit;
      //   file_put_contents(GLPI_ROOT.'/tmp/buffer.txt',PHP_EOL.PHP_EOL. json_encode($fields,JSON_UNESCAPED_UNICODE), FILE_APPEND);
       }
       else if (!defined('GLPI_ROOT'))
       {
          die("Sorry. You can't access this file directly");
      }

    }
    static function getUserProfileId() {
        if (isset($_SESSION['glpiactiveprofile']['id'])) {
            return $_SESSION['glpiactiveprofile']['id'];
        }
        return null; // или другое значение по умолчанию
    }
}
