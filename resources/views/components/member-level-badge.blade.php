@props(['user' => null, 'level' => null])

@php
    if (!$level && $user) {
        $level = $user->current_level;
    }
@endphp

@if($level)
    <span class="badge badge-level badge-level-{{ strtolower($level->name) }}" title="{{ $level->name }}">
        @if($level->icon)
            <span class="level-icon">{{ $level->icon }}</span>
        @endif
        <span class="level-name">{{ $level->name }}</span>
    </span>
@endif

<style>
    .badge-level {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
        color: #666;
    }
    
    .badge-level-青铜会员 {
        background: linear-gradient(135deg, #cd7f32 0%, #8b4513 100%);
        color: white;
    }
    
    .badge-level-白银会员 {
        background: linear-gradient(135deg, #c0c0c0 0%, #808080 100%);
        color: white;
    }
    
    .badge-level-黄金会员 {
        background: linear-gradient(135deg, #ffd700 0%, #daa520 100%);
        color: white;
    }
    
    .badge-level-钻石会员 {
        background: linear-gradient(135deg, #b9f2ff 0%, #00bfff 100%);
        color: white;
    }
    
    .badge-level-王者会员 {
        background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
    }
    
    .level-icon {
        font-size: 14px;
    }
    
    .level-name {
        white-space: nowrap;
    }
</style>
