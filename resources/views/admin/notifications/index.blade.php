@extends('layouts.admin')

@section('title','Notifications')

@section('content')

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">📌 Notifications</h5>
        <span class="badge bg-secondary">
            {{ $notifications->total() }} Total
        </span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <form action="{{ route('admin.notifications.bulkDelete') }}" method="POST" id="bulkForm">
                @csrf
                @method('DELETE')

                <div class="p-3 border-bottom">
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Delete selected notifications?')">
                        🗑 Delete Selected
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($notifications as $note)
                            <tr>

                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $note->id }}" class="checkbox">
                                </td>

                                <td>
                                    {{ $note->data['message'] ?? 'No message' }}
                                </td>

                                <td>
                                    @if(!$note->read_at)
                                        <span class="badge bg-primary">New</span>
                                    @else
                                        <span class="badge bg-success">Read</span>
                                    @endif
                                </td>

                                <td>
                                    <small class="text-muted">
                                        {{ $note->created_at->diffForHumans() }}
                                    </small>
                                </td>

                                <td>
                                    @if(!$note->read_at)
                                        <a href="{{ route('admin.notifications.read', $note->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Read
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-success" disabled>
                                            Read
                                        </button>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No notifications found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </form>

        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>


{{-- Select All Script --}}
<script>
document.getElementById('selectAll').addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('.checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

@endsection
