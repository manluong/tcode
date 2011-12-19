
$output_add['html'] = '
                                <fieldset class="dashboard fieldset-buttons">
                                    <ul class="isotope-widgets">';

foreach ($this_output['data']['set'] as $thisset){

    if ($thisset){
    foreach ($thisset as $thisrow){

$output_add['html'] .= '
                                        <li class="'.$thisrow['set'].'">
                                            <a class="button-'.$thisrow['bg'].' ui-corner-all" href="'.$thisrow['xlink'].'">
                                                <strong>'.$thisrow['result'][0].'&nbsp;<font class="unit">'.$thisrow['unit'].'</font></strong>
                                                <span>'.$thisrow['desp'].'</span>
                                            </a>
                                        </li>';

    }
    }

}

$output_add['html'] .= '
                                    </ul>
                                </fieldset>';