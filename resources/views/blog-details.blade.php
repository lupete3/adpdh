@section('title', $post->title)

@extends('layouts.app')

@section('content')
    <livewire:blog-detail :post="$post" />
@endsection