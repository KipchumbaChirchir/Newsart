@extends('layout.app')
@section('title')
    M-Pesa Transactions
@endsection
@section('content')
    <div class="text-white justify-center px-6 py-6">
        {{-- @if (\Session::has('success'))
            <div class="alert alert-success">
                <ul>
                    <li>{!! \Session::get('success') !!}</li>
                </ul>
            </div>
        @endif --}}

        <form action="{{route('access.token')}}" method="post">
            @csrf
            <button type="submit" class="text-sm rounded bg-gray-300 px-2">Access Token</button>
        </form>
    </div>
@endsection
