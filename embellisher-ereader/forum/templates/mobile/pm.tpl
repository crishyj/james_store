{IF PM_SHOW_FOLDERS}

    <h2>{LANG->Folders}</h2>

    {LOOP PM_FOLDERS}
        <div class="list">
            <h4><a href="{PM_FOLDERS->url}">{PM_FOLDERS->name}</a></h4>
            <div class="info">
                &nbsp;{IF PM_FOLDERS->total}&nbsp;({PM_FOLDERS->total}){/IF}{IF PM_FOLDERS->new}&nbsp;(<span class="new">{PM_FOLDERS->new} {LANG->newflag}</span>){/IF}
            </div>
        </div>
    {/LOOP PM_FOLDERS}

    <br>
    <div class="information"><a href="{URL->PM_FOLDERS}">{LANG->EditFolders}</a></div>
    <br>

{ELSE}

    <a id="folder-link" href="{MOD_MOBILE->URL->PM_SHOW_FOLDER_LIST}">{LANG->Folders}</a>

    {IF ERROR}<div class="attention">{ERROR}</div>{/IF}
    {IF OKMSG}<div class="information">{OKMSG}</div>{/IF}
    {INCLUDE PM_TEMPLATE}

{/IF}

<div class="nav line">
    <div class="unit size1of3">
        <a href="{URL->PM_SEND}">{LANG->SendPM}</a>
    </div>
    <div class="unit size1of3">
        <a href="{URL->BUDDIES}">{LANG->Buddies}</a>
    </div>
    <div class="unit size1of3 lastUnit">
        <a href="{URL->INDEX}">{LANG->Forums}</a>
    </div>
</div>

