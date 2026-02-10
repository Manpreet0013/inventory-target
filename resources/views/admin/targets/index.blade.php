@extends('layouts.admin')

@section('title','Assign Target')

@section('content')
<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🎯 Assign Target</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">Back</a>
                </div>

                <div class="card-body">

                    <!-- Message Box -->
                    <div id="formMessage" class="alert d-none"></div>

                    <form id="targetForm">
                        @csrf

                        <div class="row g-3">

                            <!-- Product -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Product</label>
                                <select name="product_id" id="product_id" class="form-select">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-start="{{ $product->created_at->format('Y-m-d') }}"
                                                data-end="{{ $product->expiry_date }}"
                                                data-type="{{ $product->type }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Executive -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Executive</label>
                                <select name="executive_id" class="form-select">
                                    @foreach($executives as $exe)
                                        <option value="{{ $exe->id }}">{{ $exe->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Target Type -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Type</label>
                                <select name="target_type" class="form-select">
                                    <option value="box">Box</option>
                                    <option value="amount">Amount</option>
                                </select>
                            </div>

                            <!-- Target Value -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Value</label>
                                <input type="number" name="target_value" class="form-control" placeholder="Enter Target Value">
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>

                        </div>

                        <!-- Loader -->
                        <div id="loader" class="mt-3 text-primary fw-semibold d-none">
                            Saving... Please wait
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                Assign Target
                            </button>
                            <button type="reset" class="btn btn-secondary px-4">
                                Reset
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
const targetForm = document.getElementById('targetForm');
const messageBox = document.getElementById('formMessage');
const loader = document.getElementById('loader');
const productSelect = document.getElementById('product_id');
const startDateInput = document.getElementById('start_date');
const endDateInput = document.getElementById('end_date');

function showMessage(msg,type='success'){
    messageBox.classList.remove('d-none','alert-success','alert-danger');
    messageBox.classList.add(type==='success'?'alert-success':'alert-danger');
    messageBox.innerHTML = msg;
}

function updateDates(){
    const selected = productSelect.options[productSelect.selectedIndex];
    const start = selected.dataset.start;
    const end   = selected.dataset.end;
    const type  = selected.dataset.type;
    const today = new Date().toISOString().split('T')[0];

    startDateInput.min = start;
    startDateInput.max = end;
    endDateInput.min   = start;
    endDateInput.max   = end;

    startDateInput.value = start;
    endDateInput.value   = end;

    if(type==='expiry' && end < today){
        showMessage('❌ This product is expired. Target cannot be assigned.','danger');
        targetForm.querySelector('button[type="submit"]').disabled = true;
    }else{
        messageBox.classList.add('d-none');
        targetForm.querySelector('button[type="submit"]').disabled = false;
    }
}

updateDates();
productSelect.addEventListener('change',updateDates);


targetForm.addEventListener('submit',function(e){
    e.preventDefault();

    loader.classList.remove('d-none');
    messageBox.classList.add('d-none');

    fetch('/admin/targets/store',{
        method:'POST',
        headers:{
            'X-CSRF-TOKEN':document.querySelector('input[name=_token]').value,
            'Accept':'application/json'
        },
        body:new FormData(this)
    })
    .then(async res=>{
        const data = await res.json();
        loader.classList.add('d-none');

        if(!res.ok){
            let msg='';
            if(data.errors){
                Object.values(data.errors).forEach(e=>msg+=e[0]+'<br>');
            }else{
                msg=data.message||'Error occurred';
            }
            showMessage(msg,'danger');
            return;
        }

        showMessage(data.message,'success');

        setTimeout(()=>{
            window.location.href=`/admin/product-listing/${data.product_id}`;
        },1000);

        targetForm.reset();
    })
    .catch(()=>{
        loader.classList.add('d-none');
        showMessage('Server error','danger');
    });
});
</script>

@endsection