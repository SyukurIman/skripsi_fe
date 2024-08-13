@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($position == "Data Sesi")
        @include('admin.sesi.table')
    @endif
@stop

@push('after-scripts')
@include('admin.sesi.script')

@endpush
