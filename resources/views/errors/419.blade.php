@extends('errors::layout')

@section('code', '419')

@section('title', __('Sesi Berakhir'))

@section('message', __(
    'Sesi halaman telah berakhir karena terlalu lama tidak aktif. Silakan muat ulang halaman atau kembali ke beranda.'
))