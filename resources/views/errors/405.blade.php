@extends('errors::layout')

@section('code', '405')

@section('title', __('Metode Tidak Diizinkan'))

@section('message', __(
    'Metode permintaan yang digunakan tidak diizinkan untuk halaman ini. Silakan kembali ke beranda.'
))