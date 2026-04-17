<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Header --}}
                    <div class="flex items-center gap-4 mb-6">
                        <a href="{{ route('product.index') }}"
                            class="p-1.5 rounded-md text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Add Product
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Fill in the details to add a new
                                product</p>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-300 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('product.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Wireless Headphones"
                                class="w-full px-4 py-2.5 rounded-lg border text-sm
                                {{ $errors->has('name') ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700' }}
                                text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantity & Price --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="quantity"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}"
                                    placeholder="0" min="0"
                                    class="w-full px-4 py-2.5 rounded-lg border text-sm
                                    {{ $errors->has('quantity') ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700' }}
                                    text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                @error('quantity')
                                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Price (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="price" name="price" value="{{ old('price') }}"
                                    placeholder="0" min="0" step="0.01"
                                    class="w-full px-4 py-2.5 rounded-lg border text-sm
                                    {{ $errors->has('price') ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700' }}
                                    text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                @error('price')
                                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- User --}}
                        <div>
                            <label for="user_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Owner <span class="text-red-500">*</span>
                            </label>
                            <select id="user_id" name="user_id"
                                class="w-full px-4 py-2.5 rounded-lg border text-sm
                                {{ $errors->has('user_id') ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700' }}
                                text-gray-900 dark:text-gray-100
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">Select Owner</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('product.index') }}"
                                class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                                Save Product
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>