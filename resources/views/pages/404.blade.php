@extends('layouts.master')
@section('title', 'Page Not Found')
@section('content')

<section class="min-h-[70vh] flex items-center">
  <div class="container mx-auto px-4 text-center">
    <h1 class="text-7xl font-extrabold text-gray-900">404</h1>
    <p class="mt-4 text-lg text-gray-600">Oops! The page you’re looking for doesn’t exist.</p>
    <a href="{{ route('home') }}" class="mt-6 inline-flex items-center px-6 py-3 rounded-xl bg-orange-600 text-white hover:bg-orange-700">
      Back to Home
    </a>
  </div>
</section>
@endsection
