{{-- PARTIAL ACCEPT MODAL --}}
<div id="partialModal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-96 p-5">
        <h3 class="text-lg font-semibold mb-3">Accept Partial Target</h3>

        <input type="hidden" id="partialTargetId">

        <label class="block text-sm mb-1">Enter Value</label>
        <input type="number"
               id="partialValue"
               min="1"
               class="w-full border rounded px-3 py-2 mb-4">

        <div class="flex justify-end gap-2">
            <button onclick="closePartialModal()"
                    class="px-4 py-2 bg-gray-400 text-white rounded">
                Cancel
            </button>

            <button onclick="submitPartialAccept()"
                    class="px-4 py-2 bg-yellow-500 text-white rounded">
                Confirm
            </button>
        </div>
    </div>
</div>