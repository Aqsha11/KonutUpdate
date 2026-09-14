@extends('errors::layout')

@section('code', '403')

@section('title', __('Akses Ditolak'))

@section('message', __(
    'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan kembali ke beranda atau hubungi administrator.'
))