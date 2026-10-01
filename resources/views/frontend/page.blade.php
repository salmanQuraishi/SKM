@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title . ' - AutoLogos')
@section('meta_description', $page->meta_description)

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $page->title }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5">
                <h2 class="fw-bold brand-font text-uppercase mb-4 border-bottom pb-3">{{ $page->title }}</h2>
                <div class="cms-page-content leading-relaxed text-secondary font-sm">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
