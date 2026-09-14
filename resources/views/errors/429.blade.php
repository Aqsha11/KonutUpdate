@extends('errors::layout')

@section('code', '429')

@section('title', __('Terlalu Banyak Permintaan'))

@section('message', __(
    'Terlalu banyak permintaan dalam waktu singkat. Silakan tunggu sebentar lalu coba lagi.'
))