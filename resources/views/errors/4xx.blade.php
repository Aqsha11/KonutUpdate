@extends('errors::layout')

@section('code', ($exception->getStatusCode() ?? '4xx'))

@section('title', __('Terjadi Kesalahan'))

@section('message', __(
    'Permintaan Anda tidak dapat diproses. Silakan muat ulang halaman atau kembali ke beranda.'
))