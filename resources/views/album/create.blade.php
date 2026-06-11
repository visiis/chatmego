@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>创建相册</h2>
            
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('album.store') }}">
                @csrf
                
                <div class="form-group">
                    <label for="name">相册名称</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">相册描述</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>隐私设置</label>
                    <div class="form-check">
                        <input type="radio" name="privacy" id="privacy-public" value="1" checked class="form-check-input">
                        <label for="privacy-public" class="form-check-label">公开 - 所有人都可以查看</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="privacy" id="privacy-private" value="0" class="form-check-input">
                        <label for="privacy-private" class="form-check-label">隐藏 - 需要付费查看</label>
                    </div>
                </div>
                
                <div class="form-group" id="price-group" style="display: none;">
                    <label for="price">观看价格（金币）</label>
                    <input type="number" name="price" id="price" class="form-control" min="0" value="100">
                </div>
                
                <button type="submit" class="btn btn-primary">创建相册</button>
                <a href="{{ route('album.index') }}" class="btn btn-secondary ml-2">取消</a>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="privacy"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('price-group').style.display = this.value === '0' ? 'block' : 'none';
    });
});
</script>
@endsection
