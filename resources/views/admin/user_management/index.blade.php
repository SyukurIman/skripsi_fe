@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($position == "Data User")
        @include('admin.user_management.table')
    @else
        @include('admin.user_management.form')
    @endif
@stop

@push('after-scripts')
@include('admin.user_management.script')

@endpush