@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">会员中心</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row">
        <!-- 左侧：当前会员信息 -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 class="card-title mb-3">当前会员状态</h4>
                    
                    @if($membershipInfo['has_membership'])
                        <div class="mb-3">
                            <span class="badge" style="background-color: {{ $membershipInfo['plan']->badge_color }}; font-size: 1.2rem; padding: 10px 20px;">
                                {{ $membershipInfo['plan']->icon }} {{ $membershipInfo['plan']->name }}
                            </span>
                        </div>
                        <p class="text-muted mb-2">
                            有效期至：{{ $membershipInfo['ends_at']->format('Y-m-d H:i') }}
                        </p>
                        <p class="text-primary">
                            剩余天数：<strong>{{ $membershipInfo['days_remaining'] }} 天</strong>
                        </p>
                        <hr>
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmCancel()">
                            取消自动续费
                        </button>
                    @else
                        <div class="mb-3">
                            <span class="badge bg-secondary" style="font-size: 1.2rem; padding: 10px 20px;">
                                普通会员
                            </span>
                        </div>
                        <p class="text-muted">
                            您还不是付费会员，购买会员可享受更多特权
                        </p>
                        <a href="#plans" class="btn btn-primary">
                            查看会员计划
                        </a>
                    @endif
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted">当前金币</small>
                            <h4 class="mb-0 text-warning">💰 {{ $user->coins }}</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">当前活跃度</small>
                            <h4 class="mb-0 text-info">💎 {{ $user->points }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 活跃度兑换 -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">💱 活跃度兑换金币</h5>
                    <p class="small text-muted mb-3">
                        兑换比例：100 活跃度 = 1 金币
                    </p>
                    <form action="{{ route('membership.convertPoints') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">兑换数量（100 的倍数）</label>
                            <input type="number" name="points" class="form-control" 
                                   min="100" step="100" 
                                   max="{{ $user->points }}" 
                                   placeholder="输入要兑换的活跃度" required>
                            <small class="text-muted">
                                可用活跃度：{{ $user->points }}
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            立即兑换
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- 右侧：会员计划 -->
        <div class="col-lg-8">
            <div class="row" id="plans">
                @foreach($availablePlans as $plan)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100" style="border-top: 4px solid {{ $plan['badge_color'] }};">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <span class="badge" style="background-color: {{ $plan['badge_color'] }}; font-size: 1.5rem; padding: 12px 24px;">
                                        {{ $plan['icon'] }} {{ $plan['name'] }}
                                    </span>
                                </div>
                                
                                <div class="text-center mb-3">
                                    <h2 class="text-primary mb-0">
                                        💰 {{ $plan['price'] }} 金币
                                    </h2>
                                    <small class="text-muted">
                                        ≈ ¥{{ $plan['price_yuan'] }} 元
                                    </small>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        有效期：{{ $plan['duration_days'] }} 天
                                    </li>
                                    @foreach($plan['privileges'] as $privilege)
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $privilege }}
                                        </li>
                                    @endforeach
                                </ul>
                                
                                @if($membershipInfo['has_membership'] && $membershipInfo['plan']->code === $plan['code'])
                                    <button class="btn btn-secondary w-100" disabled>
                                        当前会员
                                    </button>
                                @else
                                    <form action="{{ route('membership.purchase') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                                        <button type="submit" class="btn btn-primary w-100" 
                                                {{ $user->coins < $plan['price'] ? 'disabled' : '' }}>
                                            @if($user->coins < $plan['price'])
                                                💰 金币不足
                                            @else
                                                🛒 立即购买
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- 订阅历史队列 -->
    @if($subscriptionHistory->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 会员订阅队列（最近 10 条）</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>会员计划</th>
                                    <th>开始时间</th>
                                    <th>到期时间</th>
                                    <th>状态</th>
                                    <th>价格</th>
                                    <th>备注</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptionHistory as $subscription)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: {{ $subscription->plan->badge_color }};">
                                            {{ $subscription->plan->icon }} {{ $subscription->plan->name }}
                                        </span>
                                    </td>
                                    <td>{{ $subscription->starts_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $subscription->ends_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($subscription->status === 'active')
                                            <span class="badge bg-success">有效</span>
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-secondary">已过期</span>
                                        @elseif($subscription->status === 'cancelled')
                                            <span class="badge bg-warning">已取消</span>
                                        @endif
                                    </td>
                                    <td>💰 {{ $subscription->price_paid }}</td>
                                    <td class="text-muted small">{{ $subscription->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        <strong>叠加模式说明：</strong> 购买的会员将按顺序加入队列，当前会员到期后自动切换到下一个会员。
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function confirmCancel() {
    if (confirm('确定要取消会员自动续费吗？\n\n取消后，当前会员权益将持续到有效期结束，但不会自动续费。')) {
        document.getElementById('cancel-form').submit();
    }
}
</script>

<form id="cancel-form" action="{{ route('membership.cancel') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection
