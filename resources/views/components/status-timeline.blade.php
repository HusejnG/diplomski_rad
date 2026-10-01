@props(['project'])

<div class="card rounded-4 shadow-sm mb-4">
    <div class="card-body p-4">
        <h3 class="h6 fw-bold mb-3">Historija statusa</h3>
        <ul class="list-unstyled mb-0 small">
            @forelse($project->statusChanges as $change)
                <li class="{{ $loop->last ? '' : 'border-bottom pb-2 mb-2' }}">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $change->to_status_label }}</strong>
                        <span class="text-muted">{{ $change->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @if($change->user)
                        <div class="text-muted">{{ $change->user->name }}</div>
                    @endif
                    @if($change->note)
                        <div class="mt-1">{{ $change->note }}</div>
                    @endif
                </li>
            @empty
                <li class="text-muted">Nema zapisa.</li>
            @endforelse
        </ul>
    </div>
</div>
