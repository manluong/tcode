<?php if (isset($apphead)): ?>
	
    <div id="apphead">

	<?php if (isset($breadcrumb)): ?> <div class="apphead_warp"> <?php endif; ?>

	<?php if ($quicksearck): ?>
	    <form class="fr" ACTION="?app='.$app.'&an=searchquick_'.$app.'&aved=sq&" METHOD=POST>
	        <input id="quick-search" type="text" name="searchquickvalue" placeholder="Quick Search..." />
	    </form>
	<?php endif; ?>

	<?php if ($breadcrumb): $count_breadcrumb = 0; ?>
		<ul class="apphead_title">
						
		<?php $this_lasttitle = ""; foreach ($breadcrumb as $this_breadcrumb_li): ?>
			<?php if (!isset($breadcrumb[$count_breadcrumb+1])) $this_lasttitle = " lasttitle"; ?>
			<li><a href="<?=$this_breadcrumb_li['link']?>" title="<?=$this_breadcrumb_li['title']?>" class="appname<?=$this_lasttitle?>"><?=$this_breadcrumb_li['title']?></a></li>
		<?php $count_breadcrumb++; endforeach; ?>
		
		</ul>
		</div>	
	<?php endif; ?>
	
	</div>
	
<?php endif; ?>

<?php if ($style): ?>
<div id="appmenu_<?=$appmenu_gp?>" class="<?=$style?>">
<?php else: ?>
<div id="appmenu_<?=$appmenu_gp?>" class="appsubmenu">
<?php endif; ?>

    <ul id="nav" class="dropdown dropdown-horizontal">
    	
<?php

    if ($appmenu){
	$menuapp = "";
    if ($button['top']){

    foreach ($button['top'] as $thisbutton) {

        if (!isset($thisbutton['seprator'])){

            if ($thisbutton['class']) {
            $thisclass = ' class="'.$thisbutton['class'].'"';
            $thisclassname = ' '.$thisbutton['class'];
            }else{
            $thisclass = '';
            $thisclassname = '';
            }

            if ($thisbutton['icon']) {
               $thisicon = ' class="icon16 '.$thisbutton['icon'].'"';
            } else {
               $thisicon = '';
            }

            if (isset($button[$thisbutton['name']])){

                $menuapp .= '<li'.$thisclass.'><a href="'.$thisbutton['link'].'"'.$thisicon.'>'.$thisbutton['lang'].'</a><ul>';

                foreach ($button[$thisbutton['name']] as $thisbuttonchild) {

                    if ($thisbuttonchild['class']) {
                    $thisclass = ' class="'.$thisbuttonchild['class'].'"';
                    $thisclassname = ' '.$thisbuttonchild['class'];
                    }else{
                    $thisclass = '';
                    $thisclassname = '';
                    }

                    if ($thisbutton['icon']) {
                       $thisicon = ' class="icon16 '.$thisbuttonchild['icon'].'"';
                    } else {
                       $thisicon = '';
                    }

                    $menuapp .= '<li'.$thisclass.'><a href="'.$thisbuttonchild['link'].'"'.$thisicon.'>'.$thisbuttonchild['lang'].'</a></li>';

                }

                $menuapp .= '</ul></li>';

            } else {

                if (!isset($thisbutton['nolink'])){
                    $menuapp .= '<li'.$thisclass.'><a href="'.$thisbutton['link'].'"'.$thisicon.'>'.$thisbutton['lang'].'</a></li>';
                } else {
                    $menuapp .= '<li class="nolink'.$thisclassname.'">'.$thisbutton['lang'].'</li>';
                }
            }

        }


    }
    }

    }//end if menu

	echo $menuapp;
?>

    </ul>
</div>