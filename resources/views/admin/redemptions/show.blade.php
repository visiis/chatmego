@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📦 兑换申请详情</h1>
        <a href="{{ route('admin.redemptions.index') }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">申请信息</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">申请 ID</th>
                            <td>{{ $redemption->id }}</td>
                        </tr>
                        <tr>
                            <th>用户</th>
                            <td>{{ $redemption->user->name }} ({{ $redemption->user->email }})</td>
                        </tr>
                        <tr>
                            <th>礼物名称</th>
                            <td>{{ $redemption->gift->name }}</td>
                        </tr>
                        <tr>
                            <th>兑换数量</th>
                            <td><span class="badge bg-info">{{ $redemption->quantity }}</span></td>
                        </tr>
                        <tr>
                            <th>收件人姓名</th>
                            <td>{{ $redemption->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>手机号</th>
                            <td>{{ $redemption->phone }}</td>
                        </tr>
                        <tr>
                            <th>收件人手机号</th>
                            <td>{{ $redemption->recipient_phone ?? '未填写' }}</td>
                        </tr>
                        <tr>
                            <th>详细地址</th>
                            <td>{{ $redemption->address }}</td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td>{{ $redemption->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>更新时间</th>
                            <td>{{ $redemption->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">状态管理</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.redemptions.update-status', $redemption) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label">当前状态</label>
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
                            <span class="badge bg-{{ $statusBadges[$redemption->status] }} mb-2">
                                {{ $statusLabels[$redemption->status] }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">更新状态</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $redemption->status == 'pending' ? 'selected' : '' }}>待处理</option>
                                <option value="processing" {{ $redemption->status == 'processing' ? 'selected' : '' }}>处理中</option>
                                <option value="shipped" {{ $redemption->status == 'shipped' ? 'selected' : '' }}>已发货</option>
                                <option value="completed" {{ $redemption->status == 'completed' ? 'selected' : '' }}>已完成</option>
                                <option value="cancelled" {{ $redemption->status == 'cancelled' ? 'selected' : '' }}>已取消</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">后台备注</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ $redemption->admin_notes }}</textarea>
                            <small class="text-muted">可选，用于记录处理信息</small>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">更新状态</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
