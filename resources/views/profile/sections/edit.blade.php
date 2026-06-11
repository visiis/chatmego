<div class="p-6">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">编辑资料</h5>
        </div>
        <div class="card-body">
            <form id="edit-profile-form" method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <img src="{{ avatar_url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle img-fluid mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="custom-file">
                            <input type="file" name="avatar" class="custom-file-input" id="avatar-input" accept="image/*">
                            <label class="custom-file-label" for="avatar-input">更换头像</label>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">昵称</label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="signature">个性签名</label>
                                    <input type="text" name="signature" class="form-control" id="signature" value="{{ $user->signature }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">性别</label>
                                    <select name="gender" class="form-control" id="gender">
                                        <option value="0" {{ $user->gender == 0 ? 'selected' : '' }}>保密</option>
                                        <option value="1" {{ $user->gender == 1 ? 'selected' : '' }}>男</option>
                                        <option value="2" {{ $user->gender == 2 ? 'selected' : '' }}>女</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="age">年龄</label>
                                    <input type="number" name="age" class="form-control" id="age" value="{{ $user->age }}" min="1" max="150">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="height">身高 (cm)</label>
                                    <input type="number" name="height" class="form-control" id="height" value="{{ $user->height }}" min="100" max="250">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight">体重 (kg)</label>
                                    <input type="number" name="weight" class="form-control" id="weight" value="{{ $user->weight }}" min="30" max="200">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-4">保存更改</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>