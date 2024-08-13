@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @include('admin.setting.form')

@stop

@push('after-scripts')
@include('admin.setting.script')

@endpush
