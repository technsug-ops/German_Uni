@props(['errors' => null, 'title' => null])

@php
    $errors = $errors ?? $__env->getShared()['errors'] ?? null;
    $count = $errors && method_exists($errors, 'count') ? $errors->count() : 0;
@endphp

@if ($count > 0)
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4" role="alert" aria-labelledby="form-error-summary-title">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.732 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p id="form-error-summary-title" class="font-semibold text-red-900 mb-1">
                    {{ $title ?? trans_choice(':count error|:count errors prevented submission', $count, ['count' => $count]) }}
                </p>
                <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
