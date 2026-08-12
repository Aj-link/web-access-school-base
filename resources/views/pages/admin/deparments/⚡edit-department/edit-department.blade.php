<div class="select-none">
    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-foreground">Edit Department</h2>
            <p class="text-muted-foreground-1 mt-1">Update department information</p>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="update" class="space-y-6">
            <div>
                <label for="department_name" class="block text-sm font-medium text-foreground mb-2">
                    Department Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="department_name"
                    wire:model="department_name"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-foreground focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter department name e.g., College of Engineering">
                @error('department_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Update Department
                </button>
                <button type="button"
                    wire:click="cancel"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
