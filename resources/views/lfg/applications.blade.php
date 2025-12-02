@extends('layouts.app')

@section('title', 'Başvurular - ' . $post->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📋 Başvurular</h1>
            <p class="text-gray-600">İlan: <span class="font-semibold">{{ $post->title }}</span></p>
        </div>

        @if($applications->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Henüz başvuru yok</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($applications as $application)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($application->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">{{ $application->user->name }}</h3>
                                @if($application->user->profile)
                                    <p class="text-sm text-gray-600">
                                        {{ $application->user->profile->rank ?? 'Rütbe belirtilmemiş' }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $application->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $application->status === 'pending' ? '⏳ Bekliyor' : '' }}
                            {{ $application->status === 'accepted' ? '✅ Kabul Edildi' : '' }}
                            {{ $application->status === 'rejected' ? '❌ Reddedildi' : '' }}
                        </span>
                    </div>

                    @if($application->message)
                    <div class="mt-4 bg-gray-50 p-3 rounded">
                        <p class="text-sm text-gray-700">{{ $application->message }}</p>
                    </div>
                    @endif

                    @if($application->status === 'pending')
                    <div class="mt-4 flex space-x-3">
                        <form action="{{ route('lfg.applications.accept', $application->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                ✅ Kabul Et
                            </button>
                        </form>
                        <form action="{{ route('lfg.applications.reject', $application->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                ❌ Reddet
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('lfg.show', $post->id) }}" 
                class="inline-block bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                ← İlana Dön
            </a>
        </div>
    </div>
</div>
@endsection
