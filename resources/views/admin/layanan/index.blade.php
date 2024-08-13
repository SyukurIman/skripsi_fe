@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($position == "Data Layanan")
        @include('admin.layanan.table')
    @else
        @include('admin.layanan.form')
    @endif
@stop

@push('after-scripts')
@include('admin.layanan.script')

@endpush