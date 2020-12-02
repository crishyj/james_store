{LOOP MESSAGES}
    <div class="list">
        <table cellpadding=0 cellspacing=0 border=0>
            <tr>
                <td valign=top>
                    {IF MESSAGES->new}
                        <div class="newind"></div>
                    {/IF}
                </td>
                <td valign=top><h4><a href="{MESSAGES->URL->READ}" class="{newclass}">{MESSAGES->subject}</a></h4></td>
            </tr>
        </table>
        <div class="info">
            {LANG->by} {MESSAGES->author}<br>
            {LANG->LastPost}: <?php echo phorum_relative_date($PHORUM["TMP"]["MESSAGES"]["raw_datestamp"]); ?>
        </div>
    </div>
{/LOOP MESSAGES}
{INCLUDE "paging"}
<div class="nav line">
    <div class="unit size1of3">
        <a href="{URL->INDEX}">{LANG->ForumList}</a>
    </div>
    <div class="unit size1of3 lastUnit">
        <a href="{URL->POST}">{LANG->NewTopic}</a>
    </div>
    <div class="unit size1of3 lastUnit">
        <a href="{URL->PM}">{LANG->PM}</a>
    </div>
</div>
