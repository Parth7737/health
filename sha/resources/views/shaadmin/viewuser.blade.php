@extends('layouts.shaadmin.app')
@section('title','User Info | '.$user->name)
@section('content')
<div class="card mb-6 ps-0 border border-primary">
    <div class="card-body">
        <div class="row row-cols-3">
            <div class="col">
                <div class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                    @if(@$user->avatar)
                    <div class="position-relative image-overlay">
                        <img src="{{ asset('public/storage/'.@$user->avatar) }}" width="80" height="80" alt="{{ @$user->name }}" class="mb-3 rounded-circle shadow-lg border border-light">
                    </div>
                    @endif
                    <span class="number-3 mb-2">{{ @$user->name }}</span>
                    <span class="number-2">{{ ucfirst(@$user->designation) }} | {{ ucfirst(@$user->entity_type) }}</span>
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label for="">Email</label>
                    <p><strong>{{ @$user->email }}</strong></p>
                    <label for="">User ID</label>
                    <p><strong>{{ @$user->userid }}</strong></p>
                    <label for="">Mobile No</label>
                    <p><strong>{{ @$user->mobile_no }}</strong></p>
                    <label for="">Gender</label>
                    <p><strong>{{ ucfirst(@$user->gender) }}</strong></p>
                    <label for="">Age</label>
                    <p><strong>{{ @$user->age }}</strong></p>
                    <label for="">State</label>
                    <p><strong>{{ @$user->state }}</strong></p>
                    <label for="">District</label>
                    <p><strong>{{ @$user->district }}</strong></p>
                    
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label for="">Nature of Employment</label>
                    <p><strong>{{ @$user->nature_of_employment }}</strong></p>
                    <label for="">Designation</label>
                    <p><strong>{{ @$user->designation }}</strong></p>
                    <label for="">Entity Type</label>
                    <p><strong>{{ @$user->entity_type }}</strong></p>
                    <label for="">Entity Name</label>
                    <p><strong>{{ ucfirst(@$user->entity_name) }}</strong></p>
                    <label for="">Parent Entity</label>
                    <p><strong>{{ @$user->parent_entity }}</strong></p>                    
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-right mt-4">
                <a href="@if(auth()->user()->role->name == 'ISA Admin') {{ route('isaadmin.dashboard') }} @else {{ route('shaadmin.dashboard') }} @endif" class="btn btn-secondary px-4">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection