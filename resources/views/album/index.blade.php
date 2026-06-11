@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>{{ $user->name }} 的相册</h2>
                @if(auth()->id() == $user->id)
                <a href="{{ route('album.create') }}" class="btn btn-primary">创建相册</a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($albums->isEmpty())
    <div class="card text-center py-8">
        <div class="card-body">
            <i class="fas fa-images fa-4x text-muted mb-3"></i>
            <p class="text-muted">暂无相册</p>
            @if(auth()->id() == $user->id)
            <a href="{{ route('album.create') }}" class="btn btn-primary mt-3">创建第一个相册</a>
            @endif
        </div>
    </div>
    @else
    <div class="row">
        @foreach($albums as $album)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <a href="{{ route('album.show', ['userId' => $user->id, 'albumId' => $album->id]) }}" class="d-block">
                    <div class="album-cover" style="height: 180px; background-color: #f5f5f5; overflow: hidden;">
                        @if($album->photos->count() > 0)
                        <img src="{{ $album->photos->first()->thumbnail_url ?? $album->photos->first()->image_url }}" 
                             alt="{{ $album->name }}" 
                             class="w-100 h-100 object-cover"
                             style="transition: transform 0.3s ease;">
                        @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="fas fa-images fa-4x text-muted"></i>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="card-body">
                    <h5 class="card-title">{{ $album->name }}</h5>
                    @if($album->description)
                    <p class="card-text text-muted text-sm truncate">{{ $album->description }}</p>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center text-sm text-muted mb-3">
                        <span>{{ $album->photos()->count() }} 张图片</span>
                        <span>{{ $album->view_count }} 次浏览</span>
                    </div>

                    @if($album->privacy)
                    <span class="badge badge-success">公开</span>
                    @else
                    <span class="badge badge-warning">付费 {{ $album->price }} 金币</span>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('album.show', ['userId' => $user->id, 'albumId' => $album->id]) }}" 
                           class="btn btn-primary btn-sm">查看相册</a>
                        @if(auth()->id() == $user->id)
                        <a href="{{ route('album.edit', $album->id) }}" 
                           class="btn btn-secondary btn-sm ml-2">编辑</a>
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
