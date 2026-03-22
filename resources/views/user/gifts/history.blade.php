@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">兑换记录</h1>
    
    @if($redemptions->isEmpty())
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-500">暂无兑换记录</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($redemptions as $redemption)
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @if($redemption->gift->image)
                                <img src="{{ asset('storage/' . $redemption->gift->image) }}" 
                                     alt="{{ $redemption->gift->name }}" 
                                     class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold">{{ $redemption->gift->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    收件人：{{ $redemption->recipient_name }} | 
                                    电话：{{ $redemption->phone }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    地址：{{ $redemption->address }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($redemption->status === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($redemption->status === 'shipped')
                                    bg-blue-100 text-blue-800
                                @elseif($redemption->status === 'delivered')
                                    bg-green-100 text-green-800
                                @endif
                            ">
                                {{ $redemption->getTranslatedStatusAttribute() }}
                            </span>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ $redemption->created_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- 分页 -->
        <div class="mt-6">
            {{ $redemptions->links() }}
        </div>
    @endif
    
    <div class="mt-6">
        <a href="{{ route('user.gifts.index') }}" 
           class="text-blue-500 hover:text-blue-600">
            ← 返回我的礼物
        </a>
    </div>
</div>
@endsection
