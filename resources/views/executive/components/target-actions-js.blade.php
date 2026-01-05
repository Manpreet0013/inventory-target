<script>
let partialTargetId = null;

function acceptTarget(id) {
    if (!confirm('Accept full target?')) return;

    fetch(`/executive/target/${id}/accept`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.success) location.reload();
    });
}

function rejectTarget(id) {
    if (!confirm('Reject this target?')) return;

    fetch(`/executive/target/${id}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.success) location.reload();
    });
}

function openPartialModal(targetId, maxValue) {
    partialTargetId = targetId;
    document.getElementById('partialValue').value = '';
    document.getElementById('partialValue').max = maxValue;

    document.getElementById('partialModal').classList.remove('hidden');
    document.getElementById('partialModal').classList.add('flex');
}

function closePartialModal() {
    document.getElementById('partialModal').classList.add('hidden');
}

function submitPartialAccept() {
    const value = document.getElementById('partialValue').value;

    if (!value || value <= 0) {
        alert('Enter valid value');
        return;
    }

    fetch(`/executive/target/${partialTargetId}/accept-partial`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ value })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.success) location.reload();
    });
}
</script>
