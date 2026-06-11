<div class="p-6">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">个人资料</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ avatar_url($user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle img-fluid mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <div class="col-md-8">
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->signature }}</p>
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">性别</span>
                                <span>{{ $user->gender == 1 ? '男' : ($user->gender == 2 ? '女' : '保密') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">年龄</span>
                                <span>{{ $user->age }} 岁</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">身高</span>
                                <span>{{ $user->height }} cm</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">体重</span>
                                <span>{{ $user->weight }} kg</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">会员等级</span>
                                <span>{{ $user->membershipLevel?->name ?? '普通会员' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">金币</span>
                                <span class="text-primary font-weight-bold">{{ $user->coins }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>