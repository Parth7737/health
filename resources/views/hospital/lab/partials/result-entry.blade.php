<div id="resultEntryPane" class="lab-pane" hidden>
    <div class="form-row cols-2-1" style="margin-bottom:16px">
        <div class="input-group">
            <span class="input-addon">🔍</span>
            <input class="form-control" placeholder="Enter Report Number" id="labResultSearch" autocomplete="off"/>
        </div>
        <button class="btn btn-primary" type="button" id="labResultSearchBtn">Load Sample</button>
    </div>

    <div id="labResultSearchHint" class="alert alert-blue mb-12">
        <span class="alert-icon">ℹ️</span>
        <div>Enter a <b>Report Number</b>, then click <b>Load Sample</b>. If multiple in-progress tests exist under same order, separate cards will appear below.</div>
    </div>

    <div id="labResultMatchList" class="d-grid gap-8 mb-12"></div>
    <div id="labSelectedResultEntry"></div>
</div>
