<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.title') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Image Loading Styles -->
    <link rel="stylesheet" href="{{ asset('css/image-loading.css') }}">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    CMG
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/home') }}">{{ __('messages.nav.discover') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('discover.cards') }}">💕 卡片</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chats') }}">{{ __('messages.nav.chat') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('friends') }}">{{ __('messages.nav.friends') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('messages.auth.login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('messages.auth.register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- 通知图标 -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if(Auth::check())
                                        <?php $unreadCount = \App\Models\Notification::unreadCount(Auth::user()->id); ?>
                                        @if($unreadCount > 0)
                                            <span class="position-absolute top-0 right-0 transform translate-x-1 -translate-y-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                            </span>
                                        @endif
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                    <div class="p-2">
                                        <h6 class="dropdown-header">通知</h6>
                                        @if(Auth::check())
                                            <?php $notifications = \App\Models\Notification::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->take(10)->with('fromUser')->get(); ?>
                                            @if($notifications->isEmpty())
                                                <p class="text-center text-gray-500 py-4">暂无通知</p>
                                            @else
                                                @foreach($notifications as $notification)
                                                    <div class="notification-item p-2 border-bottom last:border-b-0 @if(!$notification->read_at) bg-gray-50 @endif" data-notification-id="{{ $notification->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $notification->fromUser ? $notification->fromUser->avatar : asset('images/default-avatar.png') }}" 
                                                                class="rounded-circle" width="40" height="40">
                                                            <div class="ml-3 flex-1">
                                                                <p class="mb-0">
                                                                    <strong>{{ $notification->fromUser ? $notification->fromUser->name : '系统' }}</strong>
                                                                    @if($notification->type == 'friend_request')
                                                                        向您发送了好友请求
                                                                    @elseif($notification->type == 'message')
                                                                        给您发送了消息
                                                                    @elseif($notification->type == 'like')
                                                                        赞了您
                                                                    @elseif($notification->type == 'comment')
                                                                        评论了您
                                                                    @endif
                                                                </p>
                                                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </li>
                            
                            <!-- 用户下拉菜单 -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('membership.index') }}">{{ __('messages.nav.membership') }}</a>
                                    <a class="dropdown-item" href="{{ route('user.gifts.index') }}">{{ __('messages.gifts.nav_gifts') }}</a>
                                    <a class="dropdown-item" href="{{ route('album.index') }}">我的相册</a>
                                    <a class="dropdown-item" href="{{ route('profile') }}">{{ __('messages.nav.profile') }}</a>
                                    <a class="dropdown-item" href="{{ route('settings') }}">{{ __('messages.nav.settings') }}</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}">
                                        {{ __('messages.nav.logout') }}
                                    </a>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Image Loading Script -->
    <script src="{{ asset('js/image-loading.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
