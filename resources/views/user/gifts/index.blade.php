@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('messages.gifts.title') }}</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- 虚拟礼物 -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">{{ __('messages.gifts.virtual_gifts') }}</h2>
        @if($virtualGifts->isEmpty())
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-500">{{ __('messages.gifts.no_gifts') }}</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($virtualGifts as $userGift)
                    <div class="bg-white rounded-lg shadow p-4 relative">
                        @if($userGift->gift->image)
                            <img src="{{ asset('storage/' . $userGift->gift->image) }}" 
                                 alt="{{ $userGift->gift->name }}" 
                                 class="w-full h-32 object-cover rounded mb-2">
                        @else
                            <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                        <h3 class="font-semibold text-sm">{{ $userGift->gift->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $userGift->gift->getTranslatedPriceTypeAttribute() }}: {{ $userGift->gift->price }}</p>
                        @if($userGift->quantity > 1)
                            <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                ×{{ $userGift->quantity }}
                            </span>
                        @endif
                        <button class="mt-2 w-full bg-blue-500 text-white text-sm py-1 rounded hover:bg-blue-600">
                            {{ __('messages.gifts.send_in_chat') }}
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- 实体礼物 -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">{{ __('messages.gifts.physical_gifts') }}</h2>
        @if($physicalGifts->isEmpty())
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-500">{{ __('messages.gifts.no_gifts') }}</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($physicalGifts as $userGift)
                    <div class="bg-white rounded-lg shadow p-4 relative">
                        @if($userGift->gift->image)
                            <img src="{{ asset('storage/' . $userGift->gift->image) }}" 
                                 alt="{{ $userGift->gift->name }}" 
                                 class="w-full h-32 object-cover rounded mb-2">
                        @else
                            <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                        <h3 class="font-semibold text-sm">{{ $userGift->gift->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $userGift->gift->getTranslatedPriceTypeAttribute() }}: {{ $userGift->gift->price }}</p>
                        @if($userGift->quantity > 1)
                            <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                ×{{ $userGift->quantity }}
                            </span>
                        @endif
                        @if(!$userGift->is_redeemed)
                            <a href="{{ route('user.gifts.redeem.create', $userGift) }}" 
                               class="mt-2 block w-full bg-green-500 text-white text-sm py-1 rounded hover:bg-green-600 text-center">
                                {{ __('messages.gifts.redeem') }}
                            </a>
                        @else
                            <span class="mt-2 block w-full bg-gray-300 text-gray-500 text-sm py-1 rounded text-center">
                                {{ __('messages.gifts.already_redeemed') }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- 兑换记录链接 -->
    <div class="mt-8">
        <a href="{{ route('user.gifts.history') }}" 
           class="text-blue-500 hover:text-blue-600">
            {{ __('messages.gifts.redeem_address') }} →
        </a>
    </div>
</div>
@endsection
