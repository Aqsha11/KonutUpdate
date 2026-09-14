@extends('errors::layout')

@section('code', ($exception->getStatusCode() ?? '5xx'))

@section('title', __('Terjadi Kesalahan Server'))

@section('message', __(
    'Maaf, terjadi gangguan teknis pada server kami. Silakan coba lagi beberapa saat atau kembali ke beranda.'
))