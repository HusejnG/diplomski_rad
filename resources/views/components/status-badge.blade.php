@props(['status'])

@php
$colors = [
    'draft' => 'secondary',
    'calculated' => 'info',
    'submitted' => 'warning',
    'under_review' => 'warning',
    'approved' => 'success',
    'scheduled' => 'primary',
    'completed' => 'success',
    'rejected' => 'danger',
];
$labels = \App\Models\SolarProject::STATUS_LABELS;
$color = $colors[$status] ?? 'secondary';
@endphp

<span {{ $attributes->merge(['class' => "badge badge-status text-bg-$color"]) }}>
    {{ $labels[$status] ?? $status }}
</span>
