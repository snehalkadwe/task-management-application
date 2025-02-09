<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Management') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <!-- Create Task Button -->
                <a href="{{ route('tasks.create') }}"
                    class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Task
                </a>
                <!-- Filter & Sorting Form -->
                <form method="GET" action="{{ route('tasks.index') }}" class="flex space-x-2"
                    onsubmit="clearEmptyParams(event)">
                    <!-- Status Filter -->
                    <select name="status" class="border px-3 py-2 rounded text-sm w-48">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in-progress" {{ request('status')=='in-progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed
                        </option>
                    </select>

                    <!-- Sorting Hidden Inputs -->
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'due_date') }}">
                    <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Filter
                    </button>
                </form>
            </div>
            <!-- Task Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border">
                                <a href="{{ route('tasks.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => request('sort_by') == 'title' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center space-x-1">
                                    <span>Title</span>
                                    @if(request('sort_order') == 'asc')
                                    <x-bi-arrow-up class="text-gray-600" />
                                    @else
                                    <x-bi-arrow-down class="text-gray-600" />
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 border">Description</th>
                            <th class="px-4 py-3 border">
                                <a href="{{ route('tasks.index', array_merge(request()->query(), ['sort_by' => 'due_date', 'sort_order' => request('sort_by') == 'due_date' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center space-x-1">
                                    <span>Due Date</span>
                                    @if(request('sort_order') == 'asc')
                                    <x-bi-arrow-up class="text-gray-600" />
                                    @else
                                    <x-bi-arrow-down class="text-gray-600" />
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 border">Status</th>
                            <th class="px-4 py-3 border">Update Status</th>
                            <th class="px-4 py-3 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $task->title }}</td>
                            <td class="px-4 py-3 border">
                                <p>{{ Str::limit($task->description, 15) }}</p>
                                <a class="text-indigo-600" href="{{ route('tasks.show', $task) }}">Read more</a>
                            </td>
                            <td class="px-4 py-3 border">{{ $task->due_date->format('j F, Y') }}</td>
                            <td class="px-4 py-3 border">
                                <span class="px-2 py-1 rounded text-white text-xs
                                    {{ $task->status == 'pending' ? 'bg-yellow-500' :
                                    ($task->status == 'in-progress' ? 'bg-blue-500' : 'bg-green-500') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border text-center">
                                <form action="{{ route('tasks.update.status', $task) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-white p-2 rounded text-sm">
                                        @if($task->status === 'pending')
                                        <x-bi-play-fill class="text-blue-700 text-lg" />
                                        @elseif($task->status === 'in-progress')
                                        <x-bi-clipboard-check class="text-blue-700 text-lg" />
                                        @else
                                        <x-bi-check-circle class="text-green-700 text-lg" />
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 border">
                                <a href="{{ route('tasks.edit', $task) }}"
                                    class="text-green-500 hover:text-green-700">Edit</a> |
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $tasks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Remove Empty Params -->
    <script>
        function clearEmptyParams(event) {
            event.preventDefault();
            let form = event.target;
            let url = new URL(form.action, window.location.origin);
            let params = new URLSearchParams(new FormData(form));

            // Remove empty query parameters
            for (let [key, value] of params.entries()) {
                if (!value) {
                    params.delete(key);
                }
            }
            window.location.href = url.pathname + '?' + params.toString();
        }
    </script>
</x-app-layout>