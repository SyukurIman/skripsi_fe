@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($type == "index")
        @include('admin.paket.table')
    @else
        @include('admin.paket.form')
    @endif
@stop

@push('after-scripts')
@include('admin.paket.script')

@endpush