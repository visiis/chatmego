@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">{{ __('messages.membership.title') }}</h2>
    
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
                    <h4 class="card-title mb-3">{{ __('messages.membership.current_membership') }}</h4>
                    
                    @if($membershipInfo['has_membership'])
                        <div class="mb-3">
                            <span class="badge" style="background-color: {{ $membershipInfo['plan']->badge_color }}; font-size: 1.2rem; padding: 10px 20px;">
                                {{ $membershipInfo['plan']->icon }} {{ $membershipInfo['plan']->name }}
                            </span>
                        </div>
                        <p class="text-muted mb-2">
                            {{ __('messages.membership.expires_at') }}：{{ $membershipInfo['ends_at']->format('Y-m-d H:i') }}
                        </p>
                        <p class="text-primary">
                            {{ __('messages.membership.days_remaining', ['days' => $membershipInfo['days_remaining']]) }}
                        </p>
                        <hr>
                        <form id="cancel-form" action="{{ route('membership.cancel') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmCancel()">
                            {{ __('messages.membership.cancel_auto_renewal') }}
                        </button>
                    @else
                        <div class="mb-3">
                            <span class="badge bg-secondary" style="font-size: 1.2rem; padding: 10px 20px;">
                                {{ __('messages.membership.basic_member') }}
                            </span>
                        </div>
                        <p class="text-muted">
                            {{ __('messages.membership.no_membership') }}
                        </p>
                        <a href="#plans" class="btn btn-primary">
                            {{ __('messages.membership.available_plans') }}
                        </a>
                    @endif
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted">{{ __('messages.nav.points') }}</small>
                            <h4 class="mb-0 text-warning">💰 {{ $user->coins }}</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('messages.nav.points') }}</small>
                            <h4 class="mb-0 text-info">💎 {{ $user->points }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 活跃度兑换 -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">💱 {{ __('messages.membership.convert_points_to_coins') }}</h5>
                    <p class="small text-muted mb-3">
                        100 {{ __('messages.nav.activity_points') }} = 1 💰
                    </p>
                    <form action="{{ route('membership.convertPoints') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.membership.convert_amount') }}</label>
                            <input type="number" name="points" class="form-control" 
                                   min="100" step="100" 
                                   max="{{ $user->points }}" 
                                   placeholder="{{ __('messages.membership.enter_activity_points') }}" required>
                            <small class="text-muted">
                                {{ __('messages.nav.activity_points') }}: {{ $user->points }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <small class="text-success">
                                <i class="fas fa-gift"></i> {{ __('messages.membership.will_get_coins') }}: <span id="coins-preview">0</span> 💰
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            {{ __('messages.membership.convert_now') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- 右侧：会员计划 -->
        <div class="col-lg-8">
            <h4 class="mb-3">{{ __('messages.membership.available_plans') }}</h4>
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
                                        💰 {{ $plan['price'] }} {{ __('messages.nav.points') }}
                                    </h2>
                                    <small class="text-muted">
                                        ≈ ¥{{ $plan['price_yuan'] }}
                                    </small>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        {{ __('messages.membership.days') }}: {{ $plan['duration_days'] }}
                                    </li>
                                    @foreach($plan['privileges'] as $privilege)
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $privilege }}
                                        </li>
                                    @endforeach
                                </ul>
                                
                                @if($membershipInfo['has_membership'] && $membershipInfo['plan']->code === $plan['code'])
                                    <div class="alert alert-info mb-3">
                                        <small><i class="fas fa-info-circle"></i> {{ __('messages.membership.currently_using') }}</small>
                                    </div>
                                @endif
                                
                                <form action="{{ route('membership.purchase') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan['id'] }}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label small">{{ __('messages.membership.purchase_duration') }}</label>
                                        <select name="months" class="form-select form-select-sm" onchange="updatePrice(this, {{ $plan['price'] }}, {{ $plan['duration_days'] }})">
                                            <option value="1">1 {{ __('messages.membership.month') }} ({{ $plan['duration_days'] }} {{ __('messages.membership.days') }})</option>
                                            <option value="3">3 {{ __('messages.membership.months') }} ({{ $plan['duration_days'] * 3 }} {{ __('messages.membership.days') }})</option>
                                            <option value="6">6 {{ __('messages.membership.months') }} ({{ $plan['duration_days'] * 6 }} {{ __('messages.membership.days') }})</option>
                                            <option value="12">12 {{ __('messages.membership.months') }} ({{ $plan['duration_days'] * 12 }} {{ __('messages.membership.days') }})</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">{{ __('messages.membership.required_coins') }}:</span>
                                            <span class="text-primary fw-bold" id="price-{{ $plan['id'] }}">{{ $plan['price'] }} 💰</span>
                                        </div>
                                        <input type="hidden" name="total_price" id="hidden-price-{{ $plan['id'] }}" value="{{ $plan['price'] }}">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100" 
                                            {{ $user->coins < $plan['price'] ? 'disabled' : '' }}
                                            id="btn-{{ $plan['id'] }}">
                                        {{ $user->coins < $plan['price'] ? '💰 ' . __('messages.membership.insufficient_coins') : '🛒 ' . __('messages.membership.buy_now') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- 订阅历史队列 -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 {{ __('messages.membership.subscription_queue') }}</h5>
                </div>
                <div class="card-body">
                    @if($subscriptionHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.membership.plan') }}</th>
                                    <th>{{ __('messages.membership.start_time') }}</th>
                                    <th>{{ __('messages.membership.end_time') }}</th>
                                    <th>{{ __('messages.membership.status') }}</th>
                                    <th>{{ __('messages.membership.price') }}</th>
                                    <th>{{ __('messages.membership.notes') }}</th>
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
                                            <span class="badge bg-success">{{ __('messages.membership.active') }}</span>
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-secondary">{{ __('messages.membership.expired') }}</span>
                                        @elseif($subscription->status === 'cancelled')
                                            <span class="badge bg-warning">{{ __('messages.membership.cancelled') }}</span>
                                        @endif
                                    </td>
                                    <td>💰 {{ $subscription->price_paid }}</td>
                                    <td class="text-muted small">{{ $subscription->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">{{ __('messages.membership.no_subscription_records') }}</p>
                    @endif
                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ __('messages.membership.stacking_info') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCancel() {
    if (confirm('{{ __('messages.membership.confirm_cancel') }}')) {
        document.getElementById('cancel-form').submit();
    }
}

// 活跃度兑换金币预览
const pointsInput = document.querySelector('input[name="points"]');
if (pointsInput) {
    pointsInput.addEventListener('input', function() {
        const points = parseInt(this.value) || 0;
        const coins = Math.floor(points / 100);
        document.getElementById('coins-preview').textContent = coins;
    });
}

// 更新价格和按钮状态
function updatePrice(selectElement, basePrice, baseDays) {
    const months = parseInt(selectElement.value);
    const planId = selectElement.closest('form').querySelector('input[name="plan_id"]').value;
    
    // 计算总价和总天数
    const totalPrice = basePrice * months;
    const totalDays = baseDays * months;
    
    // 更新价格显示
    document.getElementById(`price-${planId}`).textContent = `${totalPrice} 💰`;
    document.getElementById(`hidden-price-${planId}`).value = totalPrice;
    
    // 更新按钮状态
    const userCoins = {{ $user->coins }};
    const btn = document.getElementById(`btn-${planId}`);
    
    if (userCoins < totalPrice) {
        btn.disabled = true;
        btn.innerHTML = '💰 {{ __('messages.membership.insufficient_coins') }}';
    } else {
        btn.disabled = false;
        btn.innerHTML = '🛒 {{ __('messages.membership.buy_now') }}';
    }
}
</script>

<form id="cancel-form" action="{{ route('membership.cancel') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection
