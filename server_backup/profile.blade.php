@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $user->id == auth()->id() ? __('messages.profile.title') : $user->name . '\'s ' . __('messages.profile.title') }}
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($user->id == auth()->id())
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                    @endif

                    <!-- 基本信息 -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h5 class="mb-3">{{ __('messages.profile.title') }}</h5>
                        
                        <div class="form-group row mb-4">
                            <label class="col-md-4 col-form-label text-md-right">头像</label>
                            <div class="col-md-6">
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
                                </div>
                                @if($user->id == auth()->id())
                                    <div class="custom-file">
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                                        @error('avatar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row mb-4">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" @if($user->id != auth()->id()) disabled @endif required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.email') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" @if($user->id != auth()->id()) disabled @endif required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.phone') }}</label>
                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}" @if($user->id != auth()->id()) disabled @endif>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 个人信息 -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h5 class="mb-3">个人信息</h5>
                        
                        <div class="form-group row mb-4">
                            <label for="gender" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.gender') }}</label>
                            <div class="col-md-6">
                                <select id="gender" class="form-control @error('gender') is-invalid @enderror" name="gender" @if($user->id != auth()->id()) disabled @endif required>
                                    <option value="">{{ __('messages.profile.select') }}</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.profile.male') }}</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.profile.female') }}</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="age" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.age') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="age" type="number" min="18" max="120" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ old('age', $user->age) }}" @if($user->id != auth()->id()) disabled @endif placeholder="18">
                                    <div class="input-group-append">
                                        <span class="input-group-text">岁</span>
                                    </div>
                                </div>
                                @error('age')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="height" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.height') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="height" type="number" min="100" max="250" class="form-control @error('height') is-invalid @enderror" name="height" value="{{ old('height', $user->height) }}" @if($user->id != auth()->id()) disabled @endif placeholder="170">
                                    <div class="input-group-append">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>
                                @error('height')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="weight" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.weight') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="weight" type="number" min="30" max="300" class="form-control @error('weight') is-invalid @enderror" name="weight" value="{{ old('weight', $user->weight) }}" @if($user->id != auth()->id()) disabled @endif placeholder="60">
                                    <div class="input-group-append">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                                @error('weight')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 兴趣爱好 -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h5 class="mb-3">兴趣爱好</h5>
                        
                        <div class="form-group row mb-4">
                            <label for="hobbies" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.hobbies') }}</label>
                            <div class="col-md-6">
                                <textarea id="hobbies" class="form-control @error('hobbies') is-invalid @enderror" name="hobbies" rows="3" @if($user->id != auth()->id()) disabled @endif>{{ old('hobbies', $user->hobbies) }}</textarea>
                                @error('hobbies')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="specialty" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.specialty') }}</label>
                            <div class="col-md-6">
                                <textarea id="specialty" class="form-control @error('specialty') is-invalid @enderror" name="specialty" rows="3" @if($user->id != auth()->id()) disabled @endif>{{ old('specialty', $user->specialty) }}</textarea>
                                @error('specialty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 爱情宣言 -->
                    <div class="mb-4">
                        <h5 class="mb-3">爱情宣言</h5>
                        
                        <div class="form-group row mb-4">
                            <label for="love_declaration" class="col-md-4 col-form-label text-md-right">{{ __('messages.profile.love_declaration') }}</label>
                            <div class="col-md-6">
                                <textarea id="love_declaration" class="form-control @error('love_declaration') is-invalid @enderror" name="love_declaration" rows="4" @if($user->id != auth()->id()) disabled @endif>{{ old('love_declaration', $user->love_declaration) }}</textarea>
                                @error('love_declaration')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if ($user->id == auth()->id())
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.profile.update') }}
                                </button>
                            </div>
                        </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection