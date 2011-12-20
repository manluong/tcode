                    <nav class="collapsed">
                        <div id="mainmenu">
                        <ul class="dropdown dropdown-vertical">
<?php
    foreach ($menu_array as $field1) {
      $langname = "apptitle_".$field1['core_apps_name'];
      if ($field1['core_apps_icon']) { $this_class = ' class="icon32 '.$field1['core_apps_icon']; }else {$this_class = ''; } 
?>
                        <li><a href="<?=base_url()?><?=$field1['core_apps_name']?>"<?=$this_class?>"><?=$this->lang->line('core'.$langname)?></a></li>
<?php               
    }
?>
                        </ul>
                        </div>

                    <a class="chevron" href="#" style="width: 26px;">&raquo;</a>
                    </nav>
