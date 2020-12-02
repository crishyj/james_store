<div class="message">

    <h2>{MESSAGE->subject}</h2>

    <div class="list">
        <table cellpadding=0 cellspacing=0 border=0 class="header">
            <tr>
                <th>{LANG->From}:&nbsp;</th>
                <td><a href="{MESSAGE->URL->PROFILE}">{MESSAGE->author}</a></td>
            </tr>
            <tr>
                <th>{LANG->To}:&nbsp;</th>
                <td>
                    {IF MESSAGE->show_recipient_list}
                        {LOOP MESSAGE->recipients}
                            <a href="{MESSAGE->recipients->URL->PROFILE}">{MESSAGE->recipients->display_name}</a>
                            {IF USER->user_id MESSAGE->user_id}
                                {IF NOT MESSAGE->recipients->read_flag}({LANG->PMUnread}){/IF}
                            {/IF}
                        {/LOOP MESSAGE->recipients}
                    {ELSE}
                        {MESSAGE->recipient_count} {LANG->TotalRecipients}
                    {/IF}
                </td>
            </tr>
            <tr>
                <th>{LANG->Date}:&nbsp;</th>
                <td>{MESSAGE->date}</td>
            </tr>
        </table>
    </div>

    <div class="body">
        {MESSAGE->message}
    </div>

</div>

<form action="{URL->ACTION}" method="post">
    {POST_VARS}
    <input type="hidden" name="action" value="list" />
    <input type="hidden" name="folder_id" value="{FOLDER_ID}" />
    <input type="hidden" name="pm_id" value="{MESSAGE->pm_message_id}" />
    {IF FOLDER_IS_INCOMING}
      {IF PM_USERFOLDERS}
        <span style="white-space: nowrap; float:right">
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
          <input type="submit" name="move_message" value="{LANG->PMMoveToFolder}" />
        </span>
      {/IF}
    {/IF}
    <input type="submit" name="close_message" value="{LANG->PMCloseMessage}" />
    {IF NOT MESSAGE->user_id USERINFO->user_id}
        <input type="submit" name="reply" value="{LANG->PMReply}" />
        {IF NOT MESSAGE->recipient_count 1}
            <input type="submit" name="reply_to_all" value="{LANG->PMReplyToAll}" />
        {/IF}
    {/IF}
    <input type="submit" name="delete_message" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
</form>
<br>
<br>
