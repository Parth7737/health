<style>
    .chat-user {
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 2px;
        color: #007bff;
    }
</style>
@foreach($chats as $key => $value)
    @if($value->added_by != auth()->user()->id)
        <div class="chat-message received">
            <div class="chat-text">
                <span aria-hidden="true" data-icon="tail-in" class="_amk7"><svg viewBox="0 0 8 13" height="13" width="8" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 8 13"><title>tail-in</title><path opacity="0.13" fill="#e9ecef" d="M1.533,3.568L8,12.193V1H2.812 C1.042,1,0.474,2.156,1.533,3.568z"></path><path fill="#e9ecef" d="M1.533,2.568L8,11.193V0L2.812,0C1.042,0,0.474,1.156,1.533,2.568z"></path></svg></span>
                <span><strong>{{@$value->user->role->name}}</strong></span>
                <span>{{$value->content}}</span>
                <small class="date">{{date('G:i A', strtotime($value->created_at))}}</small>
            </div>
        </div>
    @endif
    @if($value->added_by == auth()->user()->id)
        <div class="chat-message sent">
            <div class="chat-text">
                <span aria-hidden="true" data-icon="tail-out" class="_amk7"><svg viewBox="0 0 8 13" height="13" width="8" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 8 13"><title>tail-out</title><path opacity="0.13" fill="#007bff" d="M5.188,1H0v11.193l6.467-8.625 C7.526,2.156,6.958,1,5.188,1z"></path><path fill="#007bff" d="M5.188,0H0v11.193l6.467-8.625C7.526,1.156,6.958,0,5.188,0z"></path></svg></span>
                <span><strong>{{@$value->user->role->name}}</strong></span>
                <span>{{$value->content}}</span>
                <small class="date">{{date('G:i A', strtotime($value->created_at))}}</small>
            </div>
        </div>
    @endif
@endforeach