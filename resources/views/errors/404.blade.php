@extends('errors::layout')

@section('code', '404')

@section('title', __('Halaman Tidak Ditemukan'))

@section('message', __(
    'Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tautannya salah. Silakan kembali ke beranda atau coba halaman sebelumnya.'
))