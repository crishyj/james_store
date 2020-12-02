<h2>{PMLOCATION}</h2>
{IF MESSAGECOUNT}
    {LOOP MESSAGES}
        <div class="list">
            <table cellpadding=0 cellspacing=0 border=0>
                <tr>
                    <td align=center valign=top class="check">
                        <input type="checkbox" name="checked[]" value="{MESSAGES->pm_message_id}" /><br>
                        {IF NOT MESSAGES->read_flag}
                            <div class="newind"></div>
                        {/IF}
                    </td>
                    <td valign=top><h4><a href="{MESSAGES->URL->READ}" class="{newclass}">{MESSAGES->subject}</a></h4><td>
                </tr>
            </table>
            <div class="info">
                {LANG->by} {MESSAGES->author}<br>
                {MESSAGES->date}
            </div>
        </div>
    {/LOOP MESSAGES}
    {IF PM_USERFOLDERS}
        <select name="target_folder" style="vertical-align: middle;">
            <option value=""> {LANG->PMSelectAFolder}</option>
            {LOOP PM_FOLDERS}
                {IF NOT PM_FOLDERS->id FOLDER_ID}
                    {IF NOT PM_FOLDERS->is_outgoing}
                        <option value="{PM_FOLDERS->id}"> {PM_FOLDERS->name}</option>
                    {/IF}
                {/IF}
            {/LOOP PM_FOLDERS}
        </select>
        <input type="submit" name="move" value="{LANG->PMMoveToFolder}" />
    {/IF}
    <input type="submit" name="delete" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
    {INCLUDE "paging"}
{ELSE}

    <div class="generic">{LANG->PMFolderIsEmpty}</div>

{/IF}

