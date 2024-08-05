@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4 text-white">Book a Room</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-200 text-red-800 rounded-md">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- @dd($user) --}}

    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf
        <input type="hidden" name="user_email" value="{{$user->email}}">

        <!-- Room Type -->
        <div class="mb-4">
            <label for="room_type_id" class="block text-sm font-medium text-gray-200">Room Type</label>
            <select id="room_type_id" name="room_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @foreach ($roomTypes as $roomType)
                    <option value="{{ $roomType->id }}">{{ $roomType->room_type }} (Capacity: {{ $roomType->capacity }})</option>
                @endforeach
            </select>
            @error('room_type_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Start Date -->
        <div class="mb-4">
            <label for="start_date" class="block text-sm font-medium text-gray-200">Check In</label>
            <input type="date" id="start_date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('start_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- End Date -->
        <div class="mb-4">
            <label for="end_date" class="block text-sm font-medium text-gray-200">Check Out</label>
            <input type="date" id="end_date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('end_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
{{--        <input type="hidden" id="user_uuid" name="user_uuid" value="9ca350c8-8d2a-405e-814f-0db56bf1f24b    ">--}}
        <!-- End Date -->
        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium text-gray-200">Amount</label>
            <input type="number" id="amount" name="amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('amount')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm">Book Now</button>
        </div>
    </form>
</div>
@endsection
