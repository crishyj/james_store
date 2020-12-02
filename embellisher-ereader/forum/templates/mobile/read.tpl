{LOOP MESSAGES}

    <div class="message">

        {IF MESSAGES->message_id MESSAGES->thread}
        {ELSE}
            <h2>{MESSAGES->subject}</h2>
        {/IF}
        <div class="list">
            <div class="info">
                {LANG->by} {MESSAGES->author}<br>
                {LANG->Posted}: <?php echo phorum_relative_date($PHORUM["TMP"]["MESSAGES"]["raw_datestamp"]); ?>
            </div>
        </div>

        <div class="body">
            {MESSAGES->body}
        </div>

        <div class="message-options">
            {IF MESSAGES->edit 1}
                {IF MODERATOR false}
                    <a href="{MESSAGES->URL->EDIT}">{LANG->EditPost}</a>
                {/IF}
            {/IF}
            <a href="{MESSAGES->URL->REPLY}" rel="nofollow">{LANG->Reply}</a>
            <a href="{MESSAGES->URL->QUOTE}" rel="nofollow">{LANG->QuoteMessage}</a>
            {IF MESSAGES->URL->REPORT}<a class="icon icon-exclamation" href="{MESSAGES->URL->REPORT}">{LANG->Report}</a>{/IF}
        </div>


    </div>
{/LOOP MESSAGES}

{INCLUDE "paging"}

<div class="nav line">
    <div class="unit size1of3">
        <a href="{URL->INDEX}">{LANG->ForumList}</a>
    </div>
    <div class="unit size1of3">
        <a href="{URL->LIST}">{LANG->MessageList}</a>
    </div>
    <div class="unit size1of3 lastUnit">
        <a href="{URL->POST}">{LANG->NewTopic}</a>
    </div>
</div>

