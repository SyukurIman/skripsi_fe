@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($type == "index")
        @include('admin.artikel.table')
    @else
        @include('admin.artikel.form')
    @endif
@stop

@push('after-scripts')
@include('admin.artikel.script')

@endpush