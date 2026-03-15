@extends('errors.layout')

@section('code', '429')
@section('title', 'Quá nhiều yêu cầu')
@section('message', 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn. Vui lòng đợi một lát rồi thử lại.')
@section('icon')
    <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5 14.25 2.25l-3 10.5h9L9.75 24l3-10.5h-9z"/></svg>
@endsection
