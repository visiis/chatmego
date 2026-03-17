@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">{{ __('messages.users.title') }}</h1>
    
    @if($users->isEmpty())
        <div class="alert alert-info text-center">
            {{ __('messages.users.no_users') }}
        </div>
    @else
        <div class="row">
            @foreach($users as $user)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($user->avatar)
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @else
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @endif
                                <h3 class="mt-3">{{ $user->name }}</h3>
                            </div>
                            
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.gender') }}:</strong> {{ $user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female') }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.age') }}:</strong> {{ $user->age }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.height') }}:</strong> {{ $user->height }} cm
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.weight') }}:</strong> {{ $user->weight }} kg
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="{{ route('profile', $user->id) }}" class="btn btn-primary">
                                    {{ __('messages.users.view_profile') }}
                                </a>
                                @if(auth()->id() != $user->id)
                                    <form action="{{ route('friends.request', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success mt-2">
                                            <i class="fas fa-user-plus"></i> 加入好友
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection