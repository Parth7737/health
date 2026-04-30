@extends('layouts.admin.app')
@section('title', 'Hospital Permission')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Assign Permission To {{$hospital->name}}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">
                            <svg class="stroke-icon">
                            <use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hospitals.index') }}" class="text-white">Hospitals</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection