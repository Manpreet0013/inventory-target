@extends($layout)

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto mt-6">
    <div class="bg-white shadow-md rounded px-6 py-6 max-w-2xl flex gap-6">

        <!-- Update Profile -->
        <div class="flex-1">
            <h2 class="text-xl font-semibold mb-4">Update Profile</h2>

            <div id="formMessage" class="p-3 rounded text-white hidden mb-4"></div>

            <form id="profileForm" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold mb-1">Name</label>
                    <input type="text"
                           name="name"
                           value="{{ $user->name }}"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ $user->email }}"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div id="loader" class="hidden font-semibold">Saving...</div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="flex-1">
            @include('profile.partials.update-password-form')
        </div>

    </div>

    <!-- OPTIONAL: Delete Account -->
    <!-- <div class="bg-white shadow-md rounded px-6 py-6 max-w-2xl mt-6">
        @include('profile.partials.delete-user-form')
    </div> -->
</div>

<script>
const form = document.getElementById('profileForm');
const messageBox = document.getElementById('formMessage');
const loader = document.getElementById('loader');

form.addEventListener('submit', function (e) {
    e.preventDefault();
    messageBox.classList.add('hidden');
    loader.classList.remove('hidden');

    fetch("{{ route('profile.update') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(async response => {
        const data = await response.json();
        loader.classList.add('hidden');

        if (!response.ok) {
            let msg = '';
            if (response.status === 422) {
                Object.values(data.errors).forEach(err => { msg += err[0] + '<br>'; });
            } else {
                msg = data.message || 'Something went wrong';
            }
            messageBox.innerHTML = msg;
            messageBox.classList.remove('hidden', 'bg-green-500');
            messageBox.classList.add('bg-red-500');
            return;
        }

        messageBox.innerHTML = 'Profile updated successfully';
        messageBox.classList.remove('hidden', 'bg-red-500');
        messageBox.classList.add('bg-green-500');
    })
    .catch(() => {
        loader.classList.add('hidden');
        messageBox.innerHTML = 'Server error';
        messageBox.classList.remove('hidden', 'bg-green-500');
        messageBox.classList.add('bg-red-500');
    });
});
</script>

<style>
.hidden { display: none; }
#loader { margin: 5px 0; }
.bg-red-500 { background-color: #f56565; }
.bg-green-500 { background-color: #48bb78; }
</style>
@endsection
