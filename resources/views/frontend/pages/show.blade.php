@extends('frontend.master')

@section('content')
<main>
    <div class="container">
        <br><br>
        <h3 class="text-center">{{ $page->title }}</h3>
        {!! $page->text !!}
    </div>
</main>
@endsection