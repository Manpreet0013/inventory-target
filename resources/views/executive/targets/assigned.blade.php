@extends('layouts.executive')

@section('title','Assigned Targets')

@section('content')

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($assignedTargets as $target)
        @include('executive.components.target-card',['target'=>$target])
    @empty
        <p class="text-gray-500">No assigned targets found.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $assignedTargets->links() }}
</div>
@endsection
