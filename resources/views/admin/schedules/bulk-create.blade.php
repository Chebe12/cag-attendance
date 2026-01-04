@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="bulkScheduleManager()">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @if($editMode === 'edit')
                    Edit Bulk Schedules
                @else
                    Bulk Schedule Creation
                @endif
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                @if($editMode === 'edit')
                    Edit existing weekly schedules or add new ones
                @else
                    Create weekly schedules for multiple clients at once
                @endif
            </p>
        </div>
        <a href="{{ route('admin.schedule-categories.index') }}"
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
            Back to Categories
        </a>
    </div>

    <!-- Edit Mode Notice -->
    @if($editMode === 'edit')
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Editing Published Schedules</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>You are editing existing schedules. You can:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li>Add new clients and instructors</li>
                        <li>Remove instructors by clicking the X button on their tags</li>
                        <li>Delete entire client rows</li>
                        <li>Changes will be saved when you click "Publish Schedules"</li>
                        <li><strong>Removed schedules will be permanently deleted</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Category Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Category/Term <span class="text-red-500">*</span>
                </label>
                <select x-model="selectedCategoryId" @change="loadCategory()"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-base">
                    <option value="">-- Select a Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $category && $category->id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->start_date->format('M d, Y') }} - {{ $cat->end_date->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div x-show="selectedCategoryId" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Status</label>
                <div class="flex items-center gap-4 mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <span x-text="categoryInfo.name"></span>
                    </span>
                    <span class="text-sm text-gray-600" x-text="categoryInfo.period"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div x-show="selectedCategoryId" x-transition class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Weekly Schedule Grid</h2>
                <button @click="addClientRow()" type="button"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Client
                </button>
            </div>

            <!-- Legend -->
            <div class="flex gap-4 text-sm mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>
                    <span>Morning (8:30-11:00)</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-purple-100 border border-purple-300 rounded"></div>
                    <span>Afternoon (12:00-14:30)</span>
                </div>
            </div>
        </div>

        <!-- Scrollable Grid Container -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50 z-10" style="min-width: 250px;">
                            Client
                        </th>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <th colspan="2" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase border-l border-gray-300">
                                {{ ucfirst($day) }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase sticky right-0 bg-gray-50">
                            Actions
                        </th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 sticky left-0 bg-gray-100 z-10">
                            Session
                        </th>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <th class="px-1 py-2 text-center text-xs text-gray-500 bg-green-50 border-l border-gray-300">M</th>
                            <th class="px-1 py-2 text-center text-xs text-gray-500 bg-purple-50">A</th>
                        @endforeach
                        <th class="sticky right-0 bg-gray-100"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(row, rowIndex) in scheduleRows" :key="rowIndex">
                        <tr class="hover:bg-gray-50">
                            <!-- Client Name -->
                            <td class="px-4 py-3 sticky left-0 bg-white z-10 border-r border-gray-200">
                                <select x-model="row.client_id" @change="validateRow(rowIndex)"
                                        class="w-full text-base rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 py-2"
                                        :class="row.errors.client ? 'border-red-500' : ''">
                                    <option value="">-- Select Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                <p x-show="row.errors.client" class="text-xs text-red-600 mt-1" x-text="row.errors.client"></p>
                            </td>

                            <!-- Days and Sessions Grid -->
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $dayIndex => $day)
                                @foreach(['morning', 'afternoon'] as $sessionIndex => $session)
                                    @php
                                        $bgColor = $session === 'morning' ? 'bg-green-50' : 'bg-purple-50';
                                        $borderClass = $session === 'morning' ? 'border-l border-gray-300' : '';
                                    @endphp
                                    <td class="px-2 py-2 {{ $bgColor }} {{ $borderClass }}" style="min-width: 160px;">
                                        <!-- Custom Multi-Select with Tags -->
                                        <div class="relative" x-data="{ showDropdown: false }">
                                            <!-- Selected Tags -->
                                            <div class="min-h-[60px] border rounded-lg border-gray-300 p-1 bg-white cursor-pointer"
                                                 @click="showDropdown = !showDropdown"
                                                 :class="row.errors['{{ $day }}_{{ $session }}'] ? 'border-red-500' : ''">
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-for="instructorId in row.sessions['{{ $day }}']['{{ $session }}']" :key="instructorId">
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                            <span x-text="getInstructorName(instructorId)"></span>
                                                            <button @click.stop="removeInstructor(rowIndex, '{{ $day }}', '{{ $session }}', instructorId)"
                                                                    class="hover:bg-blue-200 rounded-full p-0.5">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    </template>
                                                    <span x-show="row.sessions['{{ $day }}']['{{ $session }}'].length === 0"
                                                          class="text-xs text-gray-400 p-1">Click to add</span>
                                                </div>
                                            </div>

                                            <!-- Dropdown -->
                                            <div x-show="showDropdown"
                                                 @click.away="showDropdown = false"
                                                 x-transition
                                                 class="absolute z-20 mt-1 w-48 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                                 style="display: none;">
                                                @foreach($instructors as $instructor)
                                                    <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                                        <input type="checkbox"
                                                               :checked="row.sessions['{{ $day }}']['{{ $session }}'].includes('{{ $instructor->id }}')"
                                                               @change="toggleInstructor(rowIndex, '{{ $day }}', '{{ $session }}', '{{ $instructor->id }}')"
                                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                        <span class="ml-2 text-sm">{{ $instructor->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div x-show="row.errors['{{ $day }}_{{ $session }}']" class="text-xs text-red-600 mt-1" x-text="row.errors['{{ $day }}_{{ $session }}']"></div>
                                    </td>
                                @endforeach
                            @endforeach

                            <!-- Actions -->
                            <td class="px-4 py-3 text-center sticky right-0 bg-white border-l border-gray-200">
                                <button @click="removeClientRow(rowIndex)" type="button"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="scheduleRows.length === 0">
                        <td colspan="16" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No clients added yet. Click "Add Client" to start creating schedules.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div x-show="selectedCategoryId && scheduleRows.length > 0" x-transition
         class="bg-white rounded-lg shadow-md p-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            <span x-text="scheduleRows.length"></span> client(s) configured
            @if($editMode === 'edit')
                <span class="text-blue-600 font-medium ml-2">• Editing Mode</span>
            @endif
        </div>
        <div class="flex gap-3">
            <button @click="saveSchedules('draft')" type="button"
                    :disabled="saving"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                <span x-show="!saving">
                    @if($editMode === 'edit')
                        Update as Draft
                    @else
                        Save as Draft
                    @endif
                </span>
                <span x-show="saving">Saving...</span>
            </button>
            <button @click="saveSchedules('published')" type="button"
                    :disabled="saving"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                <span x-show="!saving">
                    @if($editMode === 'edit')
                        Update & Publish
                    @else
                        Publish Schedules
                    @endif
                </span>
                <span x-show="saving">Publishing...</span>
            </button>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">How to use this interface:</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-1">
                    <p>1. Select a category/term from the dropdown above</p>
                    <p>2. Click "Add Client" to add rows for each client</p>
                    <p>3. For each client, click on a session cell to add instructors (you can add multiple instructors)</p>
                    <p>4. Click the X button on an instructor tag to remove them</p>
                    <p>5. M = Morning (8:30-11:00), A = Afternoon (12:00-14:30)</p>
                    <p>6. The system will warn you if an instructor is double-booked</p>
                    <p>7. Save as Draft to continue later, or Publish to activate schedules</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bulkScheduleManager() {
    return {
        selectedCategoryId: '{{ $category->id ?? '' }}',
        categoryInfo: {
            name: '{{ $category->name ?? '' }}',
            period: '{{ $category ? $category->start_date->format("M d, Y") . " - " . $category->end_date->format("M d, Y") : "" }}'
        },
        scheduleRows: [],
        saving: false,
        existingSchedules: @json($existingSchedules ?? []),
        instructors: @json($instructors->map(function($i) { return ['id' => $i->id, 'name' => $i->name]; })),

        init() {
            if (this.selectedCategoryId) {
                // Don't call loadCategory() here - it would cause infinite reload
                // Just load existing schedules if we already have a category
                this.loadExistingSchedules();
            }
        },

        loadCategory() {
            if (!this.selectedCategoryId) return;
            window.location.href = '{{ route("admin.schedules.bulk.create") }}?category_id=' + this.selectedCategoryId;
        },

        loadExistingSchedules() {
            // Load existing draft schedules if any
            if (Object.keys(this.existingSchedules).length > 0) {
                // Group by client
                const clientSchedules = {};
                for (const [key, instructorIds] of Object.entries(this.existingSchedules)) {
                    const [clientId, day, session] = key.split('_');
                    if (!clientSchedules[clientId]) {
                        clientSchedules[clientId] = this.createEmptyRow();
                        clientSchedules[clientId].client_id = clientId;
                    }
                    clientSchedules[clientId].sessions[day][session] = instructorIds;
                }
                this.scheduleRows = Object.values(clientSchedules);
            }
        },

        createEmptyRow() {
            const sessions = {};
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'].forEach(day => {
                sessions[day] = {
                    'morning': [],
                    'afternoon': []
                };
            });
            return {
                client_id: '',
                sessions: sessions,
                errors: {}
            };
        },

        addClientRow() {
            this.scheduleRows.push(this.createEmptyRow());
        },

        removeClientRow(index) {
            if (confirm('Remove this client row?')) {
                this.scheduleRows.splice(index, 1);
            }
        },

        getInstructorName(instructorId) {
            const instructor = this.instructors.find(i => i.id == instructorId);
            return instructor ? instructor.name : 'Unknown';
        },

        toggleInstructor(rowIndex, day, session, instructorId) {
            const row = this.scheduleRows[rowIndex];
            const index = row.sessions[day][session].indexOf(instructorId);

            if (index > -1) {
                row.sessions[day][session].splice(index, 1);
            } else {
                row.sessions[day][session].push(instructorId);
            }

            this.validateCell(rowIndex, day, session);
        },

        removeInstructor(rowIndex, day, session, instructorId) {
            const row = this.scheduleRows[rowIndex];
            const index = row.sessions[day][session].indexOf(instructorId);
            if (index > -1) {
                row.sessions[day][session].splice(index, 1);
                this.validateCell(rowIndex, day, session);
            }
        },

        async validateCell(rowIndex, day, session) {
            const row = this.scheduleRows[rowIndex];
            const instructorIds = row.sessions[day][session];

            if (instructorIds.length === 0) {
                delete row.errors[`${day}_${session}`];
                return;
            }

            // Check for conflicts via AJAX
            try {
                const response = await fetch('{{ route("admin.schedules.bulk.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        category_id: this.selectedCategoryId,
                        schedule: {
                            day_of_week: day,
                            session_time: session,
                            instructor_ids: instructorIds
                        }
                    })
                });

                const data = await response.json();
                if (!data.valid && data.conflicts.length > 0) {
                    row.errors[`${day}_${session}`] = `Conflict: ${data.conflicts.join(', ')}`;
                } else {
                    delete row.errors[`${day}_${session}`];
                }
            } catch (error) {
                console.error('Validation error:', error);
            }
        },

        validateRow(rowIndex) {
            const row = this.scheduleRows[rowIndex];
            if (!row.client_id) {
                row.errors.client = 'Please select a client';
            } else {
                delete row.errors.client;
            }
        },

        async saveSchedules(draftStatus) {
            if (!this.selectedCategoryId) {
                alert('Please select a category first');
                return;
            }

            // Validate all rows
            let hasErrors = false;
            this.scheduleRows.forEach((row, index) => {
                this.validateRow(index);
                if (Object.keys(row.errors).length > 0) {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                alert('Please fix the errors before saving');
                return;
            }

            // Prepare data
            const schedules = [];
            this.scheduleRows.forEach(row => {
                if (!row.client_id) return;

                Object.entries(row.sessions).forEach(([day, sessions]) => {
                    Object.entries(sessions).forEach(([session, instructorIds]) => {
                        if (instructorIds.length > 0) {
                            schedules.push({
                                client_id: row.client_id,
                                day_of_week: day,
                                session_time: session,
                                instructor_ids: instructorIds
                            });
                        }
                    });
                });
            });

            if (schedules.length === 0) {
                alert('Please add at least one schedule');
                return;
            }

            this.saving = true;

            try {
                const response = await fetch('{{ route("admin.schedules.bulk.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        category_id: this.selectedCategoryId,
                        schedules: schedules,
                        draft_status: draftStatus
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    window.location.href = data.redirect || '{{ route("admin.schedule-categories.index") }}';
                } else {
                    alert(data.message || 'Error saving schedules');
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('Network error. Please try again.');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endsection
