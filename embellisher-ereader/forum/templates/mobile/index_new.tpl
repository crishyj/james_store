{LOOP FORUMS}
    {IF FORUMS->level 0}
        {IF FORUMS->forum_id FORUMS->vroot}
        {ELSE}
            <h2><a href="{FORUMS->URL->LIST}">{FORUMS->name}</a></h2>
        {/IF}
    {ELSE}
        <div class="list">
            {IF FORUMS->folder_flag}
                <h3><a href="{FORUMS->URL->INDEX}">{FORUMS->name}</a></h3>
            {ELSE}
                <h3><a href="{FORUMS->URL->LIST}">{FORUMS->name}</a>{IF FORUMS->new_message_check}&nbsp;&nbsp;<span class="new-indicator">({LANG->NewMessages})</span>{/IF}</h3>
                <div class="info">
                    {LANG->LastPost}: <?php echo phorum_relative_date($PHORUM["TMP"]["FORUMS"]["raw_last_post"]); ?>
                </div>
            {/IF}
        </div>
    {/IF}
{/LOOP FORUMS}
</table>
<div class="nav line">
    <div class="unit size1of2">
        <a href="{URL->SEARCH}">{LANG->Search}</a>
    </div>
    <div class="unit size1of2 lastUnit">
        <a href="{URL->PM}">{LANG->PM}</a>
    </div>
</div>
