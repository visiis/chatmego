@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📋 兑换记录</h2>
        <a href="{{ route('user.gifts.index') }}" class="btn btn-outline-primary">
            ← 返回我的礼物
        </a>
    </div>
    
    @if($redemptions->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">暂无兑换记录</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">礼物</th>
                                <th scope="col">数量</th>
                                <th scope="col">收件人</th>
                                <th scope="col">手机号</th>
                                <th scope="col" style="max-width: 200px;">地址</th>
                                <th scope="col">状态</th>
                                <th scope="col">兑换时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redemptions as $redemption)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($redemption->gift->image)
                                                <img src="{{ image_url($redemption->gift->image) }}" 
                                                     alt="{{ $redemption->gift->name }}" 
                                                     class="rounded me-3"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-gift text-muted"></i>
                                                </div>
                                            @endif
                                            <span class="fw-medium">{{ $redemption->gift->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $redemption->quantity }}</span>
                                    </td>
                                    <td>{{ $redemption->recipient_name }}</td>
                                    <td>{{ $redemption->phone }}</td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $redemption->address }}">
                                            {{ $redemption->address }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => '待处理', 'color' => 'warning'],
                                                'processing' => ['label' => '处理中', 'color' => 'info'],
                                                'shipped' => ['label' => '已发货', 'color' => 'primary'],
                                                'completed' => ['label' => '已完成', 'color' => 'success'],
                                                'cancelled' => ['label' => '已取消', 'color' => 'secondary'],
                                            ];
                                            $config = $statusConfig[$redemption->status] ?? ['label' => $redemption->status, 'color' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $config['color'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $redemption->created_at->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- 分页 -->
        @if($redemptions->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $redemptions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
