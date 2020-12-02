{IF ERROR}<div class="attention">{ERROR}</div>{/IF}
{IF OKMSG}<div class="information">{OKMSG}</div>{/IF}

{IF PREVIEW}

    <div class="message preview">

        <h2>{PREVIEW->subject}</h2>
        <div class="list">
            <div class="info">
                {LANG->by} {PREVIEW->author}<br>
                {LANG->Posted}: <?php echo phorum_relative_date($PHORUM["TMP"]["PREVIEW"]["raw_datestamp"]); ?>
            </div>
        </div>

        <div class="body">
            {PREVIEW->body}
        </div>

    </div>

{/IF}

<div class="pad">
    <form id="post-form" name="post_form" action="{URL->ACTION}" method="post">
        {POST_VARS}
        <div class="generic">
            <small>
                {IF MODE "moderation"}
                    {LANG->YourName}:<br/>
                {ELSE}
                    {LANG->Author}:<br />
                {/IF}
                {IF OPTION_ALLOWED->edit_author}
                    <input type="text" name="author" size="30" value="{POSTING->author}" />
                {ELSE}
                    <big><strong>{POSTING->author}</strong></big><br />
                {/IF}
                <br/>
                {IF MODE "post" OR MODE "reply"}
                    {IF NOT LOGGEDIN}
                        {LANG->YourEmail}:<br />
                        <input type="text" name="email" size="30" value="{POSTING->email}" /><br />
                        <br />
                    {/IF}
                {ELSEIF MODE "moderation"}
                    {IF POSTING->user_id 0}
                        {LANG->Email}:<br />
                        <input type="text" name="email" size="30" value="{POSTING->email}" /><br />
                        <br />
                    {/IF}
                {/IF}
                {LANG->Subject}:<br />
                <input type="text" name="subject" id="subject" size="50" value="{POSTING->subject}" /><br />
                <br />
            </small>

            <small>{LANG->Message}:</small><br>
            <div id="post-body">
                <textarea name="body" id="body">{POSTING->body}</textarea>
            </div>

        </div>

        <div id="post-buttons">
            <input type="submit" name="preview" value=" {LANG->Preview} " />
            <input type="submit" name="finish" value=" {POSTING->submitbutton_text} " />
            {IF SHOW_CANCEL_BUTTON}
                <input type="submit" name="cancel" onclick="return confirm('{LANG->CancelConfirm}')" value=" {LANG->Cancel} " />
            {/IF}

        </div>

        {IF POSTING->user_id}
            <br>
            <small>{LANG->Options}:</small><br />
            {IF OPTION_ALLOWED->subscribe}
                <input type="checkbox" id="subscription-follow" name="subscription_follow" value="1" {IF POSTING->subscription}checked="checked"{/IF} {IF OPTION_ALLOWED->subscribe_mail}onclick="phorum_subscription_displaystate()"{/IF} /> <label for="subscription-follow"><small>{LANG->FollowThread}</small></label><br />
                {IF OPTION_ALLOWED->subscribe_mail}
                    <div id="subscription-mail-div">
                        <img src="{URL->TEMPLATE}/images/tree-L.gif" border="0" alt="tree-L" />
                        <input type="checkbox" id="subscription-mail" name="subscription_mail" value="1" {IF POSTING->subscription "message"}checked="checked"{/IF} /> <label for="subscription-mail"><small>{LANG->EmailReplies}</small></label>
                    </div>

                    <script type="text/javascript">
                        // <![CDATA[
                        function phorum_subscription_displaystate() {
                            if (document.getElementById) {
                                var f = document.getElementById('subscription-follow');
                                var d = document.getElementById('subscription-mail-div');
                                var e = document.getElementById('subscription-mail');
                                d.style.display  = f.checked ? 'block' : 'none';
                            }
                        }

                        // Setup initial display state for subscription options.
                        phorum_subscription_displaystate();
                        // ]]>
                    </script>
                {/IF}
            {/IF}

            <input type="checkbox" id="show-signature" name="show_signature" value="1" {IF POSTING->show_signature} checked="checked"{/IF} /> <label for="show-signature"><small>{LANG->AddSig}</small></label><br />
            <br/>

        {/IF}

        {IF SHOW_SPECIALOPTIONS}
            <div id="post-moderation">
                <small>
                    {LANG->Special}:<br />
                    {IF OPTION_ALLOWED->sticky}
                        <input type="checkbox" name="sticky"
                            id="phorum_sticky" value="1"
                            {IF POSTING->special "sticky"}checked="checked"{/IF} />
                        <label for="phorum_sticky">{LANG->MakeSticky}</label>
                        <br />
                    {/IF}
                    <input type="checkbox" id="allow-reply" name="allow_reply" value="1" {IF POSTING->allow_reply} checked="checked"{/IF} /> <label for="allow-reply">{LANG->AllowReplies}</label>
                </small>
            </div>
            <br>
        {/IF}

    </form>

</div>

{IF MODERATED}
    <div class="notice">{LANG->ModeratedForum}</div>
{/IF}

<div class="nav line">
    <div class="unit size1of3">
        <a href="{URL->INDEX}">{LANG->ForumList}</a>
    </div>
    <div class="unit size1of3">
        <a href="{URL->LIST}">{LANG->MessageList}</a>
    </div>
    <div class="unit size1of3 lastUnit">
        <a href="{URL->PM}">{LANG->PM}</a>
    </div>
</div>
