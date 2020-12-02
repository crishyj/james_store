{IF ERROR}<div class="attention">{ERROR}</div>{/IF}
{IF OKMSG}
    <div class="information">
        <p align>
        {OKMSG}
        </p>
        {IF URL->CLICKHERE}
            <p><a href="{URL->CLICKHERE}">{CLICKHEREMSG}</a></p>
        {/IF}
        {IF URL->REDIRECT}
            <p><a href="{URL->REDIRECT}">{BACKMSG}</a></p>
        {/IF}
    </div>
{/IF}

