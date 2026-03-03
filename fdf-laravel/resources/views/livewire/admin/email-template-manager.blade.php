<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Email Templates</h1>
        <p class="text-sm text-gray-600">Manage editable notification messages for enrollment and registration flows.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Template</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($templates as $template)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $template->name }}</p>
                                <p class="text-xs text-gray-500">{{ $template->key }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $template->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $template->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="edit({{ $template->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            @if($selectedTemplateId)
                <h2 class="text-lg font-semibold text-gray-900">Edit Template</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $name }}</p>
                @if($description)
                    <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
                @endif

                <form wire:submit="save" class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Subject *</label>
                        <input wire:model="subject" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Body *</label>
                        <textarea wire:model="body" rows="14" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Use placeholders like <code>@{{user_name}}</code>, <code>@{{course_title}}</code>, <code>@{{course_price}}</code>, <code>@{{payment_instructions}}</code>, <code>@{{event_title}}</code>.
                        </p>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input wire:model="is_active" type="checkbox" class="rounded border-gray-300">
                            Enable this template
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save Template</button>
                        <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">Cancel</button>
                    </div>
                </form>
            @else
                <h2 class="text-lg font-semibold text-gray-900">Template Editor</h2>
                <p class="mt-2 text-sm text-gray-600">Select any template from the list to edit its subject and message body.</p>
            @endif
        </div>
    </div>
</div>
