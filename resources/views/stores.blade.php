@extends('master')

@section('body')
    @include('partials.store-header')
    <main role="main">

        <section class="jumbotron text-center">
            <div class="container">
                <h1 class="jumbotron-heading">Choose a store to manage</h1>
                <p class="lead text-muted">Before accessing the store dashboard, you should select a store to manage.</p>
                <p>
                    <a href="#" class="btn btn-primary my-2">Main call to action</a>
                    <a href="{{ route('logout') }}" class="btn btn-secondary my-2">Logout</a>
                </p>
            </div>
        </section>

        <div class="album py-5 bg-light">
            <div class="container">

                <div class="row">
                    @if(!empty($stores))
                        @foreach($stores as $store)
                        <div class="col-md-4">
                            <div class="card mb-4 box-shadow">
                                <img class="card-img-top" src="{{ $store->coverPhoto }}" alt="Card image cap">
                                <div class="card-body">
                                    <p class="card-text">{{ $store->name . ' - ' . $store->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="{{ route('manage_store', ['storeId' => $store->storeId]) }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                                        </div>
                                        <small class="text-muted">
                                            Last login:
                                            @if(!empty($store->lastLogin))
                                                {{ (new \Illuminate\Support\Carbon($store->lastLogin))->diffForHumans() }}
                                            @else
                                                Never
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <h2>No stores yet!</h2>
                    @endif
                </div>
            </div>
        </div>

    </main>

    @include('partials.store-footer')
@endsection