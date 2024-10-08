<?php
class PluginRolecategoriesitilConfig extends CommonDBTM {

  static private $_instance = NULL;
  static $rightname         = 'profile';
  function getName($with_comment=0) {
    return _n('ITIL category', 'ITIL categories', $with_comment);
  }

  static function getInstance() {

    if (!isset(self::$_instance)) {
      self::$_instance = new self();
      if (!self::$_instance->getFromDB(1)) {
        self::$_instance->getEmpty();
      }
    }
    return self::$_instance;
  }
  static function showConfigForm($item) {

    if (Session::haveRight("profile", READ)) {
      // Создаем экземпляр класса Profile
      $profile = new Profile();


      // Проверяем, установлен ли ID в GET-запросе
      if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Загружаем профиль по его ID
        $profile->getFromDB($_GET['id']);

        // Получаем ID профиля
        $profile_id = $profile->getID();
      } else {
        echo "Profile ID is not set or invalid.";
      }
    } else {
      echo "You do not have permission to view this profile.";
      return false;
    }
    $config =  new self();
    $config->showFormHeader(['no_header'=>true]);
    echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);
    echo "<thead><tr class='tab_bg_1'>";
    echo "<th width='2%'>".__('ID')."</th>";
    echo "<th   width='10%'>"._n('ITIL category', 'ITIL categories', 0)."</th>";
    echo "<th  class='center' width='3%'>".__('Visibility')."</th>";
    echo "<th width='2%'>".__('Level')."</th>";
    echo "</tr></thead>";
    echo Html::hidden('profile_id', ['value' => $profile_id]);
    // Убедитесь, что пользователь имеет права на просмотр категорий ITIL
    if (Session::haveRight('itilcategory', READ)) {
      // Создаем экземпляр класса ItilCategory
      $itilCategory = new ItilCategory();
      // Получаем список категорий
      // Метод find возвращает массив категорий, удовлетворяющих условиям
      $categories = $itilCategory->find();
      //file_put_contents(GLPI_ROOT.'/tmp/buffer.txt',PHP_EOL.PHP_EOL. json_encode($categories,JSON_UNESCAPED_UNICODE), FILE_APPEND);
      // Перебор и отображение категорий
      foreach ($categories as $id => $data) {
        $fields = $config->find(['profile_id'=>$profile_id,'itilcategory_id'=>$id]);
        $fields = array_shift($fields);
        $name = $data['completename'];
        echo "<tr class='tab_bg_1'>";
        echo "<td>$id</td>";
        echo "<td >".Html::hidden('itilcategory_id[]', ['value' => $data['id']]).__("$name", "itilcategory")."</td>";

        echo "<td class='center' width='3%'>";
        Dropdown::showYesNo("active[]", $fields['active']);
        echo "</td>
        <td class='center'>".$data['level']."</td>
        </tr>";
        //  echo "ID: " . $id . " - Name: " . $data['name'] . "<br/>";
      }
    } else {
      echo "You do not have permission to view ITIL categories.";
    }

    echo "<tr class='center'><td colspan='4'>".Html::submit(__('Keep'), [
      'name'  => 'update',
      'class' => 'btn btn-primary mt-2'
      ])."</td></tr>";
      echo "</form>";
      echo <<< SCRIPT
      <script type="text/javascript">
      $('form[name="asset_form"]').find('table').removeClass('tab_cadre_fixe');
      $('form[name="asset_form"]').find('table').addClass('table table-hover');
      </script>
      SCRIPT;
      return false;
    }
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if ($item->getType()=='Profile') {
        return self::getName();
      }
      return '';
    }


    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getType()=='Profile') {
        self::showConfigForm($item);
      }
      return true;
    }

    static function updated($post)
    {
      global $DB;
      $profile_id = $post['profile_id'];
      foreach ($post['itilcategory_id'] as $key => $value) {
        $DB->update(
          'glpi_plugin_rolecategoriesitil_configs',
          [
            'active' => $post['active'][$key]
          ],
          [
            'profile_id' => $profile_id,
            'itilcategory_id'=>$value
          ]
        );
      }
      return true;
    }
  }
