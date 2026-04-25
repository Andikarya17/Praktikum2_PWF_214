<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-100 tracking-tight">Category List</h2>
                            <p class="text-sm text-gray-400 mt-1">Manage your product categories</p>
                        </div>
                        <div class="flex gap-2">
                            <x-add-product :url="route('category.create')" name="Category" />
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-6 px-4 py-3 bg-green-900/30 border border-green-800 text-green-300 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto rounded-lg border border-gray-700">
                        <table class="min-w-full divide-y divide-gray-700 text-sm">
                            <thead class="bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-300 uppercase tracking-wider w-16">NO</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-300 uppercase tracking-wider">NAME</th>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-300 uppercase tracking-wider">TOTAL PRODUCT</th>
                                    <th class="px-6 py-3 text-center font-semibold text-gray-300 uppercase tracking-wider w-32">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @forelse ($categories as $category)
                                    <tr class="hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 text-gray-400">#{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-medium text-white">{{ $category->name }}</td>
                                        <td class="px-6 py-4 text-gray-300">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->products_count > 0 ? 'bg-indigo-900/40 text-indigo-300' : 'bg-gray-700 text-gray-400' }}">
                                                {{ $category->products_count }} products
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('category.edit', $category) }}" class="p-1.5 rounded-md text-gray-400 hover:text-amber-400 hover:bg-amber-900/50 transition" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('category.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-md text-gray-400 hover:text-red-400 hover:bg-red-900/50 transition" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-500 mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            No categories found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
