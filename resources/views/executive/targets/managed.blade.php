@extends('layouts.executive')

@section('title','Managed Targets')

@section('content')

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($targets as $target)
        @include('executive.components.target-card',['target'=>$target])
    @empty
        <p class="text-gray-500">No managed targets found.</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $targets->links() }}
</div>
@endsection
