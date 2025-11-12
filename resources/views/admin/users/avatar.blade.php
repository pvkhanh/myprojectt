{{--
    Component: Avatar
    Hiển thị avatar của user với fallback là initials
    
    Usage:
    <x-avatar :user="$user" size="md" />
    
    Props:
    - user: User model instance (required)
    - size: 'xs' | 'sm' | 'md' | 'lg' | 'xl' (default: 'md')
    - border: boolean (default: true)
    - shadow: boolean (default: true)
    - class: additional CSS classes
--}}

@props([
    'user',
    'size' => 'md',
    'border' => true,
    'shadow' => true,
    'class' => ''
])

@php
    // Size mapping
    $sizes = [
        'xs' => ['dimension' => 30, 'font' => '14px', 'border' => 2],
        'sm' => ['dimension' => 40, 'font' => '16px', 'border' => 2],
        'md' => ['dimension' => 55, 'font' => '20px', 'border' => 3],
        'lg' => ['dimension' => 80, 'font' => '28px', 'border' => 3],
        'xl' => ['dimension' => 120, 'font' => '42px', 'border' => 4],
    ];
    
    $config = $sizes[$size] ?? $sizes['md'];
    $dimension = $config['dimension'];
    $fontSize = $config['font'];
    $borderWidth = $config['border'];
    
    // CSS classes
    $baseClasses = 'rounded-circle';
    $borderClass = $border ? "border border-{$borderWidth} border-primary" : '';
    $shadowClass = $shadow ? 'shadow-sm' : '';
    
    $commonClasses = "{$baseClasses} {$borderClass} {$shadowClass} {$class}";
    $commonStyle = "width:{$dimension}px; height:{$dimension}px; object-fit:cover;";
@endphp

@if($user->avatar)
    <img src="{{ $user->avatar_url }}" 
         alt="{{ $user->username }}"
         class="{{ $commonClasses }}"
         style="{{ $commonStyle }}"
         loading="lazy">
@else
    <div class="{{ $commonClasses }} bg-gradient-primary text-white d-flex align-items-center justify-content-center fw-bold"
         style="{{ $commonStyle }} font-size:{{ $fontSize }};"
         title="{{ $user->full_name }}">
        {{ $user->initials }}
    </div>
@endif