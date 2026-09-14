@extends('errors::layout')

@section('code', '500')

@section('title', __('Terjadi Kesalahan'))

@section('message', __(
    'Maaf, terjadi gangguan teknis pada server kami. Silakan coba lagi beberapa saat atau kembali ke beranda.'
))