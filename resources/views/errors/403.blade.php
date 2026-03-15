@extends('errors.layout')

@section('code', '403')
@section('title', 'Truy cập bị từ chối')
@section('message', 'Bạn không có quyền truy cập trang này. Vui lòng đăng nhập hoặc liên hệ quản trị viên nếu bạn cho rằng đây là lỗi.')
@section('icon')
    <svg class="mx-auto h-24 w-24" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
@endsection
