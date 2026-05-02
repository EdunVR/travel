@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Search Results</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Search Results</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Search results for: <strong>{{ $query }}</strong></h3>
                        <div class="card-tools">
                            <span class="badge badge-info">{{ $results->count() }} results found</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($results->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No results found for "{{ $query }}"
                            </div>
                        @else
                            <div class="list-group">
                                @foreach($results as $result)
                                    <a href="{{ $result['url'] }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <i class="{{ $result['icon'] }} mr-2"></i>
                                                {{ $result['title'] }}
                                            </h5>
                                            <small>
                                                <span class="badge badge-secondary">{{ ucfirst($result['type']) }}</span>
                                            </small>
                                        </div>
                                        <p class="mb-1 text-muted">{{ $result['subtitle'] }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
