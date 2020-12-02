{IF TOTALPAGES}
<div class="line paging">
    <div class="unit">
        <strong>{LANG->Pages}:</strong>
        {LOOP PAGES}
            {IF PAGES->pageno CURRENTPAGE}<strong class="current-page">{PAGES->pageno}</strong>
            {ELSE}<a href="{PAGES->url}">{PAGES->pageno}</a>{/IF}
        {/LOOP PAGES}
    </div>
    <div class="unit lastUnit">
        {IF URL->FIRSTPAGE}<a href="{URL->FIRSTPAGE}">{LANG->FirstPage}</a>{/IF}
        {IF URL->PREVPAGE}<a href="{URL->PREVPAGE}">{LANG->PrevPage}</a>{/IF}
        {IF URL->NEXTPAGE}<a href="{URL->NEXTPAGE}">{LANG->NextPage}</a>{/IF}
    </div>
</div>
{/IF}
