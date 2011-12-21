<fieldset class="dashboard fieldset-buttons">
    <ul class="isotope-widgets">';
<?php foreach ($set as $thisset): ?>
<?php if ($thisset): foreach ($thisset as $thisrow): ?>
        <li class="<?=$thisrow['set']?>">
            <a class="button-<?=$thisrow['bg']?> ui-corner-all" href="<?=$thisrow['xlink']?>">
                <strong><?=$thisrow['result'][0]?>&nbsp;<font class="unit"><?=$thisrow['unit']?></font></strong>
                <span><?=$thisrow['desp']?></span>
            </a>
        </li>
<?php endforeach; endif; ?>
<?php endforeach; ?>
    </ul>
</fieldset>