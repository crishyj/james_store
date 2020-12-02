<h2>{PMLOCATION}</h2>
{IF MESSAGECOUNT}

    {LOOP MESSAGES}
        <div class="list">
            <table cellpadding=0 cellspacing=0 border=0>
                <tr>
                    <td valign=top class="check"><input type="checkbox" name="checked[]" value="{MESSAGES->pm_message_id}" /></td>
                    <td><h4><a href="{MESSAGES->URL->READ}" class="{newclass}">{MESSAGES->subject}</a></h4><td>
                </tr>
            </table>
            <div class="info">
                {IF MESSAGES->recipient_count 1}
                    {LOOP MESSAGES->recipients}
                        <a href="{MESSAGES->recipients->URL->PROFILE}">{MESSAGES->recipients->display_name}</a>&nbsp;
                    {/LOOP MESSAGES->recipients}
                {ELSE}
                    {MESSAGES->recipient_count}&nbsp;{LANG->Recipients}&nbsp;
                {/IF}
                <br>
                {MESSAGES->date}
            </div>
        </div>
    {/LOOP MESSAGES}
    </table>
    <input type="submit" name="delete" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
    {INCLUDE "paging"}
{ELSE}
    <div class="generic">{LANG->PMFolderIsEmpty}</div>
{/IF}

