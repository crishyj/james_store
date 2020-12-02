{IF BUDDYCOUNT}
    <form id="phorum-pm-list" action="{URL->ACTION}" method="post">
        {POST_VARS}
        <input type="hidden" name="page" value="buddies" />
        <input type="hidden" name="action" value="buddies" />
        <table border="0" cellspacing="0" class="list">
            <tr>
                <th align="left" width="20">&nbsp;</th>
                <th align="left">{LANG->Buddy}</th>
                <th align="center">{LANG->Mutual}</th>
            </tr>
            {LOOP BUDDIES}
                <tr>
                    <td width="5%"><input type="checkbox" name="checked[]" value="{BUDDIES->user_id}"></td>
                    <td width="40%"><a href="{BUDDIES->URL->PROFILE}"><strong>{BUDDIES->display_name}</strong></a></td>
                    <td width="20%" align="center">{IF BUDDIES->mutual}{LANG->Yes}{ELSE}{LANG->No}{/IF}</td>
                </tr>
            {/LOOP BUDDIES}
        </table>
        <br>
        <input type="submit" name="delete" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
        <input type="submit" name="send_pm" value="{LANG->SendPM}" />
    </form>
{ELSE}
    <div class="generic">{LANG->BuddyListIsEmpty}</>
{/IF}

<br>