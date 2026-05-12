<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Careers — {{ $hospital->name }}</title>
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/fontawesome.css') }}">
    <style>
        body { background: #eef2f7; font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; color: #0d1b2a; }
        .careers-wrap { max-width: 960px; margin: 0 auto; padding: 24px 16px 48px; }
        .careers-hero { background: #fff; border: 1px solid #ccd8e8; border-radius: 12px; padding: 22px 20px; margin-bottom: 18px; }
        .careers-hero h1 { font-size: 22px; font-weight: 800; margin: 0 0 6px; color: #4a148c; }
        .careers-hero p { margin: 0; color: #5a7894; font-size: 14px; }
        .vac-card { background: #fff; border: 1px solid #ccd8e8; border-radius: 12px; padding: 16px 18px; margin-bottom: 12px; }
        .vac-card h2 { font-size: 16px; font-weight: 700; margin: 0 0 8px; }
        .vac-meta { font-size: 13px; color: #5a7894; margin-bottom: 10px; }
        .vac-badge { display: inline-flex; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; background: #fff3e0; color: #e65100; }
        .btn-apply { border: 1px solid #2e7d32; background: #2e7d32; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 14px; font-size: 13px; }
        .btn-apply:hover { background: #1b5e20; color: #fff; }
        .empty-state { text-align: center; padding: 32px; color: #5a7894; }
    </style>
</head>
<body>
<div class="careers-wrap">
    <div class="careers-hero">
        <h1><i class="fa fa-user-plus" style="color:#2e7d32"></i> Careers</h1>
        <p>{{ $hospital->name }} — open positions. Apply online below.</p>
    </div>

    @forelse($vacancies as $v)
        @php
            $title = $v->designation->name ?? $v->title;
            $dept = $v->department->name ?? 'General';
            $from = optional($v->open_from)->format('d M Y');
            $till = optional($v->open_till)->format('d M Y');
        @endphp
        <div class="vac-card">
            <h2>{{ $title }}</h2>
            <div class="vac-meta">
                <span class="vac-badge">{{ $dept }}</span>
                @if($from || $till)
                    <span style="margin-left:8px">Open: {{ $from ?: '—' }} &ndash; {{ $till ?: '—' }}</span>
                @endif
            </div>
            @if(!empty($v->description))
                <div style="font-size:14px;white-space:pre-wrap;margin-bottom:12px">{{ $v->description }}</div>
            @endif
            <button type="button" class="btn btn-apply careers-apply-btn" data-id="{{ $v->id }}" data-title="{{ e($title) }}">
                <i class="fa fa-paper-plane"></i> Apply
            </button>
        </div>
    @empty
        <div class="vac-card empty-state">No open vacancies at the moment. Please check again later.</div>
    @endforelse
</div>

<div class="modal fade" id="careersApplyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="careersApplyModalTitle">Apply</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="careersApplyForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="vacancy_id" id="careersVacancyId" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full name</label>
                        <input type="text" name="full_name" class="form-control" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" maxlength="25">
                    </div>
                    <div class="form-group">
                        <label>Cover letter (optional)</label>
                        <textarea name="cover_letter" class="form-control" rows="4" maxlength="5000"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Resume (PDF / DOC, max 5MB)</label>
                        <input type="file" name="resume" class="form-control-file" accept=".pdf,.doc,.docx">
                    </div>
                    <div id="careersApplyAlert" class="alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="careersApplySubmit">Submit application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('public/front/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>
(function () {
    var applyUrl = @json($applyUrl);
    function openApplyModal() {
        var el = document.getElementById('careersApplyModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        }
    }
    function hideApplyModal() {
        var el = document.getElementById('careersApplyModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            var inst = window.bootstrap.Modal.getInstance(el);
            if (inst) { inst.hide(); }
        }
    }
    $('.careers-apply-btn').on('click', function () {
        var id = $(this).data('id');
        var title = $(this).data('title') || 'Position';
        $('#careersVacancyId').val(id);
        $('#careersApplyModalTitle').text('Apply — ' + title);
        $('#careersApplyForm')[0].reset();
        $('#careersVacancyId').val(id);
        $('#careersApplyAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');
        openApplyModal();
    });
    $('#careersApplyForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#careersApplySubmit');
        var $alert = $('#careersApplyAlert');
        $alert.addClass('d-none');
        var fd = new FormData(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: applyUrl,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res && res.status) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success').text(res.message || 'Submitted.');
                    $('#careersApplyForm')[0].reset();
                    $('#careersVacancyId').val('');
                    setTimeout(function () { hideApplyModal(); }, 1200);
                } else {
                    $alert.removeClass('d-none alert-success').addClass('alert-danger').text((res && res.message) || 'Failed.');
                }
            },
            error: function (xhr) {
                var msg = 'Unable to submit.';
                if (xhr.status === 422 && xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        var first = Object.values(xhr.responseJSON.errors)[0];
                        msg = Array.isArray(first) ? first[0] : first;
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                }
                $alert.removeClass('d-none alert-success').addClass('alert-danger').text(msg);
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });
})();
</script>
</body>
</html>
