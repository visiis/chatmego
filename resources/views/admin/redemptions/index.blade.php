@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📦 礼物兑换申请管理</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">返回首页</a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户</th>
                            <th>礼物名称</th>
                            <th>数量</th>
                            <th>收件人</th>
                            <th>手机号</th>
                            <th>地址</th>
                            <th>状态</th>
                            <th>申请时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redemptions as $redemption)
                            <tr>
                                <td>{{ $redemption->id }}</td>
                                <td>{{ $redemption->user->name }}</td>
                                <td>{{ $redemption->gift->name }}</td>
                                <td><span class="badge bg-info">{{ $redemption->quantity }}</span></td>
                                <td>{{ $redemption->recipient_name }}</td>
                                <td>{{ $redemption->phone }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $redemption->address }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'pending' => '待处理',
                                            'processing' => '处理中',
                                            'shipped' => '已发货',
                                            'completed' => '已完成',
                                            'cancelled' => '已取消'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusBadges[$redemption->status] }}">
                                        {{ $statusLabels[$redemption->status] }}
                                    </span>
                                </td>
                                <td>{{ $redemption->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.redemptions.show', $redemption) }}" 
                                       class="btn btn-sm btn-primary">
                                        查看详情
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>暂无兑换申请</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($redemptions->hasPages())
                <div class="mt-4">
                    {{ $redemptions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
