{IF ERROR}<div class="attention">{ERROR}</div>{/IF}
{IF OKMSG}<div class="information">{OKMSG}</div>{/IF}

{IF LOGGEDIN}
    {IF NOT FULLY_LOGGEDIN}
        <div class="information">{LANG->PeriodicLogin}</div>
    {/IF}
{/IF}

<div class="pad">

    {IF NOT LOGGEDIN}
        <br>
        <div class="information"><a href="{URL->REGISTER}">{LANG->NotRegistered}</a></div>
        <br>
    {/IF}

    <h4>{LANG->LoginTitle}</h4>
    <form action="{URL->ACTION}" method="post">
        {POST_VARS}
        {LANG->Username}:<br />
        <input type="text" id="username" name="username" size="30" value="{LOGIN->username}" /><br />
        <br />
        {LANG->Password}:<br />
        <input type="password" id="password" name="password" size="30" value="" /><br />
        <br />
        <input type="submit" value="{LANG->Submit}" />
    </form>
    <br>
    <br>

    <h4>{LANG->LostPassword}</h4>
    <form action="{URL->ACTION}" method="post">
        {POST_VARS}
        {LANG->LostPassInfo}<br />
        <br />
        <input type="text" name="lostpass" size="30" value="" /><br />
        <br />
        <input type="submit" value="{LANG->Submit}" />
    </form>
</div>
