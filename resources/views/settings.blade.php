@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('messages.settings.title') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf

                        <div class="form-group row mb-4">
                            <label for="language" class="col-md-4 col-form-label text-md-right">{{ __('messages.settings.language') }}</label>

                            <div class="col-md-6">
                                <select id="language" class="form-control" name="language" required>
                                    <option value="zh_TW" {{ session('locale', config('app.locale')) == 'zh_TW' ? 'selected' : '' }}>中文（繁體）</option>
                                    <option value="zh_CN" {{ session('locale', config('app.locale')) == 'zh_CN' ? 'selected' : '' }}>中文（简体）</option>
                                    <option value="en" {{ session('locale', config('app.locale')) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.settings.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection