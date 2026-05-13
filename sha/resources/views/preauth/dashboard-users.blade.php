
@php 
use App\Models\PreauthRegister;
@endphp
<div class="row grid-view-box" style="display: {{ $list_view?'none':'flex' }};">
@foreach ($users as $user)
    <div class="col-md-6 col-lg-4">
        <div class="ditel-card p-3" data-bs-toggle="modal" data-bs-target="#bigModal">
            <h5 class="mb-3">{{ @$user->benificiary->name }}</h5>
            <p class="mb-2">{{ @$user->benificiary->age }} yr | {{ @$user->benificiary->gender }} | 
                <span>Program ID:</span><strong>{{ @$user->benificiary->card_id }}</strong>
            </p>
            <p class="mb-2">Register ID: <strong>{{ $user->register_id }}</strong></p>
            <p class="mb-2">Register Date:<strong>{{ date("d/m/Y",strtotime($user->created_at)) }}</strong></p>
            <div class="balance-card p-2">
                <div class="d-block">
                    <h6>Claim Utilized: 50000</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-gradient-new" role="progressbar"
                            style="width: 100%;" aria-valuenow="50" aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
                <div class="icons d-flex">
                <div class="theme-color"> {{ $user->status_label }}</div>
                    <a href="{{ route('preauth.preauth-request',[$user->id]) }}" class="angle-right">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 320 512">
                            <path
                                d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z" />
                        </svg>
                    </a>
                    @if($user->status == 0)
                        <button class="angle-right" onclick="cancelRegistration('{{ $user->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path
                                    d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                            </svg>
                        </button>
                    @elseif(in_array($user->status,[PreauthRegister::STATUS_PREAUTH_PENDING]))
                        <button class="angle-right" onclick="cancelPreauth('{{ $user->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path
                                    d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
<div class="row list-view-box" style="display: {{ $list_view?'flex':'none' }};">
@foreach ($users as $user)
    <div class="col-md-12 mb-4">
        <div class="ditel-card p-3">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">{{ @$user->benificiary->name }}</h5>
                    <p class="mb-2">{{ @$user->benificiary->age }} yr | {{ @$user->benificiary->gender }} | <span>Program ID:</span><strong>{{ @$user->benificiary->card_id }}</strong></p>
                </div>
                <div class="col-md-4">
                    <p class="mb-2">Register ID: <strong>{{ @$user->register_id }}</strong></p>
                    <p class="mb-2">Register Date:<strong>{{ date("d/m/Y",strtotime($user->created_at)) }}</strong></p>
                </div>
                <div class="col-md-4">
                    <div class="balance-card p-2">
                        <div class="d-block">
                            <h6>Claim Utilized:50000</h6>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-gradient-new" role="progressbar"
                                    style="width: 100%;" aria-valuenow="50" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="icons d-flex">
                            <div class="theme-color"> {{ $user->status_label }}</div>
                            <a href="{{ route('preauth.preauth-request',[$user->id]) }}" class="angle-right">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 320 512">
                                    <path
                                        d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z" />
                                </svg>
                            </a>
                            @if($user->status == 0)
                                <button class="angle-right" onclick="cancelRegistration('{{ $user->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512">
                                        <path
                                            d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                    </svg>
                                </button>
                                @elseif(in_array($user->status,[PreauthRegister::STATUS_PREAUTH_PENDING]))
                                <button class="angle-right" onclick="cancelPreauth('{{ $user->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512">
                                        <path
                                            d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>