<li class="dropdown-menu-header border-bottom py-50">
    <div class="dropdown-header d-flex align-items-center py-2">
        <h6 class="mb-0 me-auto">Notification</h6>
        <div class="d-flex align-items-center">
            <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">{{$unreadCount}}
                New</span>
            <a href="javascript:void(0)" class="btn btn-text-secondary rounded-pill btn-icon dropdown-notifications-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="ri-mail-open-line text-heading ri-20px"></i></a>
        </div>
    </div>
</li>
<li class="dropdown-notifications-list scrollable-container">
    <ul class="list-group list-group-flush">  
        @foreach($notifications as $key => $value)                              
            <li class="list-group-item list-group-item-action dropdown-notifications-item {{@$value->is_read ? 'marked-as-read' : ''}}">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                            <img src="{{asset('public/storage/'.@$value->hospital->image)}}" alt="" class="rounded-circle">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 small">{{$value->message}}</h6>                        
                        <small class="text-muted">{{$value->created_at->diffForHumans()}}</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <!-- <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a> -->
                        <a href="javascript:void(0)" onclick="markAsRead('{{$value->id}}', this);" class="dropdown-notifications-archive"><span class="ri-close-line ri-20px"></span></a>
                    </div>
                </div>
            </li>
        @endforeach                              
    </ul>
</li>
<li class="border-top">
    <div class="d-grid p-4">
        <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
            <small class="align-middle">View all notifications</small>
        </a>
    </div>
</li>