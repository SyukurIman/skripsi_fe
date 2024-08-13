@extends('admin.layout.app')

@push('before-styles')
@endpush

@section('content')
    @if($type == "index")
        @include('admin.order.table')
    @else
        @include('admin.order.invoice')
    @endif
@stop

@push('after-scripts')
@include('admin.order.script')

@endpush