{IF PREVIEW}

    <div class="information">
        {LANG->PreviewExplain}
    </div>

    <div class="message preview">

        <h2>{PREVIEW->subject}</h2>

        <div class="list">
            <table cellpadding=0 cellspacing=0 border=0 class="header">
                <tr>
                    <th>{LANG->From}:&nbsp;</th>
                    <td><a href="{PREVIEW->URL->PROFILE}">{PREVIEW->author}</a></td>
                </tr>
                <tr>
                    <th>{LANG->To}:&nbsp;</th>
                    <td>
                        {IF PREVIEW->show_recipient_list}
                            {LOOP PREVIEW->recipients}
                                <a href="{PREVIEW->recipients->URL->PROFILE}">{PREVIEW->recipients->display_name}</a>
                                {IF USER->user_id PREVIEW->user_id}
                                    {IF NOT PREVIEW->recipients->read_flag}({LANG->PMUnread}){/IF}
                                {/IF}
                            {/LOOP PREVIEW->recipients}
                        {ELSE}
                            {PREVIEW->recipient_count} {LANG->TotalRecipients}
                        {/IF}
                    </td>
                </tr>
                <tr>
                    <th>{LANG->Date}:&nbsp;</th>
                    <td>{PREVIEW->date}</td>
                </tr>
            </table>
        </div>

        <div class="body">
            {PREVIEW->message}
        </div>

    </div>

{/IF}

<div class="pad">
    <form action="{URL->ACTION}" method="post">
        {POST_VARS}
        <input type="hidden" name="action" value="post" />
        <input type="hidden" name="hide_userselect" value="{HIDE_USERSELECT}" />

        <div class="generic">

            <small>

                To:<br />
                {! Show user selection}
                {IF SHOW_USERSELECTION}
                    <div class="phorum-pmuserselection">
                        {IF USERS}
                            <select id="userselection" name="to_id" size="1" align="middle">
                                <option value=""> {LANG->PMSelectARecipient}</option>
                                {LOOP USERS}
                                    <option value="{USERS->user_id}" <?php if (isset($_POST['to_id']) && $_POST['to_id'] == $PHORUM['TMP']['USERS']['user_id']) echo 'selected="selected"'?>>{USERS->display_name}</option>
                                {/LOOP USERS}
                            </select>
                        {ELSE}
                            <input type="text" id="userselection" name="to_name" value="<?php if (isset($_POST['to_name'])) echo htmlspecialchars($_POST['to_name'])?>" />
                        {/IF}
                        <input type="submit" name="rcpt_add" value="{LANG->PMAddRecipient}" />
                        {! Always show recipient list on a separate line}
                        {IF RECIPIENT_COUNT}<br style="clear:both" />{/IF}
                    </div>
                {/IF}
                {! Display the current list of recipients}
                {LOOP MESSAGE->recipients}
                    <div class="phorum-recipientblock">
                        {MESSAGE->recipients->display_name}
                        <input type="hidden" name="recipients[{MESSAGE->recipients->user_id}]" value="1" />
                        <input type="submit" value="Delete" name="del_rcpt::{MESSAGE->recipients->user_id}" />
                    </div>
                {/LOOP MESSAGE->recipients}
                <br />

                {LANG->Subject}:<br />
                <input type="text" name="subject" id="subject" size="50" value="{MESSAGE->subject}" /><br />
                <br />

                {LANG->Options}:<br />
                <input type="checkbox" id="keep" name="keep" value="1"{IF MESSAGE->keep} checked="checked" {/IF} /><label for="keep"> {LANG->KeepCopy}</label><br />
                <br />

                {LANG->Message}:
                <div id="post-body">
                    <textarea name="message" id="body" class="body" rows="15" cols="50">{MESSAGE->message}</textarea>
                </div>

            </small>

        </div>

        <div id="post-buttons">

          <input name="preview" type="submit" value=" {LANG->Preview} " />
          <input name="post" type="submit" value=" {LANG->PostPM} " />
          <br>
        </div>

    </form>

</div>
