@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('messages.gifts.redeem_physical') }}</h1>
    
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <!-- 礼物信息 -->
            <div class="mb-6 pb-6 border-b">
                <div class="flex items-center space-x-4">
                    @if($userGift->gift->image)
                        <img src="{{ asset('storage/' . $userGift->gift->image) }}" 
                             alt="{{ $userGift->gift->name }}" 
                             class="w-24 h-24 object-cover rounded">
                    @else
                        <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold">{{ $userGift->gift->name }}</h2>
                        <p class="text-gray-600">{{ $userGift->gift->description }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('messages.gifts.quantity') }}: {{ $userGift->quantity }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- 兑换表单 -->
            <form action="{{ route('user.gifts.redeem.store', $userGift) }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('messages.gifts.recipient_name') }} *
                        </label>
                        <input type="text" 
                               name="recipient_name" 
                               id="recipient_name" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('recipient_name') }}">
                        @error('recipient_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('messages.gifts.phone') }} *
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('phone') }}">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('messages.gifts.address') }} *
                        </label>
                        <textarea name="address" 
                                  id="address" 
                                  rows="3" 
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition">
                        {{ __('messages.gifts.submit_redeem') }}
                    </button>
                    <a href="{{ route('user.gifts.index') }}" 
                       class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition text-center">
                        取消
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
