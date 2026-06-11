@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2>编辑相册</h2>
            
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('album.update', $album->id) }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">相册名称</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $album->name }}" required>
                </div>
                
                <div class="form-group">
                    <label for="description">相册描述</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ $album->description }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>隐私设置</label>
                    <div class="form-check">
                        <input type="radio" name="privacy" id="privacy-public" value="1" {{ $album->privacy ? 'checked' : '' }} class="form-check-input">
                        <label for="privacy-public" class="form-check-label">公开 - 所有人都可以查看</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="privacy" id="privacy-private" value="0" {{ !$album->privacy ? 'checked' : '' }} class="form-check-input">
                        <label for="privacy-private" class="form-check-label">隐藏 - 需要付费查看</label>
                    </div>
                </div>
                
                <div class="form-group" id="price-group" style="display: {{ !$album->privacy ? 'block' : 'none' }}">
                    <label for="price">观看价格（金币）</label>
                    <input type="number" name="price" id="price" class="form-control" min="0" value="{{ $album->price }}">
                </div>
                
                <button type="submit" class="btn btn-primary">保存修改</button>
                <a href="{{ route('album.show', ['userId' => auth()->id(), 'albumId' => $album->id]) }}" class="btn btn-secondary ml-2">取消</a>
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
