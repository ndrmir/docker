@if($errors->any())
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="alert alert-danger" role="alert">
                <span type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&#10006;</span>
                </span>
                    @foreach($errors->all() as $errorTxt)
                    <li>{{ $errorTxt }}</li>
                    @endforeach
                </ul>                
            </div>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="alert alert-success" role="alert">
                <span type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&#10006;</span>
                </span>
                {{ session()->get('success')}}
            </div>
        </div>
    </div>
@endif