<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
                <div class="py-4 px-6 text-gray-900">
                    <h3 class="text-2xl font-semibold mb-2">{{ $task->title }}</h3>
                    <div class="text-sm text-gray-600">
                        <span><strong>Due Date:</strong> {{ $task->due_date }}</span>
                        <span class="ml-4"><strong>Status:</strong> {{ $task->status }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="py-4 px-6 text-gray-900">
                        <strong>Description:</strong> {{ $task->description }}
                    </p>
                </div>

                <div class="border-t border-gray-200 mt-4 px-6 py-4 flex justify-between ">
                    <div class="w-50 inline-flex justify-center py-2 px-4 border border-transparent rounded-md
                        shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none
                        focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <a href="{{ route('tasks.index') }}" class="text-white">Back to Task
                            List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
