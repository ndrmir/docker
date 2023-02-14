@extends('layouts.app')

@section('content')
    <div class="container">
        @include('leadLink.includes.result_message')        
        <div class="row justify-content-center">            
            <div class="col-md-12">
                <form method="POST" action="{{ route('leadLink.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Сохранить связаные сущности сделок</button>                
                </form>               
            </div>
        </div>       
    </div>
@endsection