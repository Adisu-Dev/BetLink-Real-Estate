<template>
  <div class="space-y-5">
    <!-- Create New Search Button -->
    <div class="flex justify-end">
      <button
        @click="showNewSearch = true"
        class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Save New Search
      </button>
    </div>

    <!-- Saved Searches List -->
    <div class="space-y-3">
      <template v-for="search in searches" :key="search.id">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex-1">
              <h3 class="font-bold text-gray-900 text-lg">{{ search.name }}</h3>
              <p class="text-sm text-gray-600 mt-1">
                {{ search.description }}
              </p>
              <div class="flex flex-wrap gap-2 mt-3">
                <span v-for="tag in search.tags" :key="tag" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded">
                  {{ tag }}
                </span>
              </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
              <div class="text-right">
                <p class="text-xs text-gray-500">Last saved</p>
                <p class="font-semibold text-gray-900">{{ search.lastSaved }}</p>
              </div>
              <div class="flex gap-2">
                <!-- Alert Toggle -->
                <label class="relative flex items-center cursor-pointer">
                  <input v-model="search.alertEnabled" type="checkbox" class="hidden peer" />
                  <div class="w-11 h-6 bg-gray-300 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
                  <span class="absolute left-1 top-0.5 w-5 h-5 bg-white rounded-full transition-all peer-checked:translate-x-5" title="Alert toggle"></span>
                </label>

                <!-- Action Button -->
                <div class="relative group">
                  <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                  </button>
                  <div class="absolute right-0 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-lg hidden group-hover:block z-10">
                    <button
                      @click="editSearch(search)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Edit
                    </button>
                    <button
                      @click="deleteSearch(search.id)"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t border-gray-100"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- View Search Results Button -->
          <button
            @click="viewResults(search)"
            class="w-full px-4 py-2 border border-emerald-600 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-colors"
          >
            View {{ search.resultCount }} Results
          </button>
        </div>
      </template>

      <!-- Empty State -->
      <div v-if="searches.length === 0" class="text-center py-12 bg-white rounded-lg border border-gray-200">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-gray-500 font-medium">No saved searches yet</p>
        <p class="text-sm text-gray-400 mt-2">Start searching for properties and save your search criteria</p>
      </div>
    </div>

    <!-- New Search Modal -->
    <div v-if="showNewSearch" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md mx-4 space-y-4 max-h-96 overflow-y-auto">
        <h2 class="text-lg font-bold text-gray-900">Save Search Criteria</h2>

        <!-- Search Name -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Search Name *</label>
          <input
            v-model="newSearch.name"
            type="text"
            placeholder="e.g., Bole 2BR Apartments"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
          <input
            v-model="newSearch.location"
            type="text"
            placeholder="e.g., Bole, Addis Ababa"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
        </div>

        <!-- Price Range -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Min Price</label>
            <input
              v-model="newSearch.minPrice"
              type="number"
              placeholder="5000"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Max Price</label>
            <input
              v-model="newSearch.maxPrice"
              type="number"
              placeholder="20000"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
        </div>

        <!-- Bedrooms -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Bedrooms</label>
          <input
            v-model="newSearch.bedrooms"
            type="number"
            placeholder="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
        </div>

        <!-- Enable Alerts -->
        <label class="flex items-center gap-3 cursor-pointer">
          <input v-model="newSearch.alertEnabled" type="checkbox" class="w-4 h-4" />
          <span class="text-sm font-medium text-gray-700">Send me alerts for new listings</span>
        </label>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4">
          <button
            @click="saveNewSearch"
            class="flex-1 px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors"
          >
            Save Search
          </button>
          <button
            @click="showNewSearch = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const showNewSearch = ref(false)

const newSearch = ref({
  name: '',
  location: '',
  minPrice: '',
  maxPrice: '',
  bedrooms: '',
  alertEnabled: true,
})

const searches = ref([
  {
    id: 1,
    name: 'Bole 2BR Apartments',
    description: 'Furnished 2-bedroom apartments in Bole area',
    tags: ['Bole', '2BR', '10K-20K ETB'],
    lastSaved: '3 days ago',
    resultCount: 12,
    alertEnabled: true,
  },
  {
    id: 2,
    name: 'Commercial Offices',
    description: 'Office spaces for rent in Addis Ababa CBD',
    tags: ['Commercial', 'CBD', '25K+ ETB'],
    lastSaved: '1 week ago',
    resultCount: 8,
    alertEnabled: false,
  },
  {
    id: 3,
    name: 'Affordable Studios',
    description: 'Studio apartments under 10,000 ETB monthly',
    tags: ['Studio', 'Affordable', '<10K ETB'],
    lastSaved: '2 weeks ago',
    resultCount: 24,
    alertEnabled: true,
  },
])

const editSearch = (search) => {
  alert(`Edit search: ${search.name}`)
}

const deleteSearch = (id) => {
  if (confirm('Delete this saved search?')) {
    const idx = searches.value.findIndex(s => s.id === id)
    if (idx !== -1) {
      searches.value.splice(idx, 1)
    }
  }
}

const viewResults = (search) => {
  alert(`View ${search.resultCount} results for: ${search.name}`)
}

const saveNewSearch = () => {
  if (!newSearch.value.name.trim()) {
    alert('Please enter a search name')
    return
  }
  searches.value.unshift({
    id: Math.max(...searches.value.map(s => s.id), 0) + 1,
    name: newSearch.value.name,
    description: `${newSearch.value.location || 'Any location'} • ${newSearch.value.bedrooms || 'Any size'}`,
    tags: [
      newSearch.value.location,
      newSearch.value.bedrooms ? `${newSearch.value.bedrooms}BR` : null,
      newSearch.value.minPrice && newSearch.value.maxPrice
        ? `${newSearch.value.minPrice}-${newSearch.value.maxPrice} ETB`
        : null,
    ].filter(Boolean),
    lastSaved: 'Just now',
    resultCount: Math.floor(Math.random() * 30) + 5,
    alertEnabled: newSearch.value.alertEnabled,
  })
  newSearch.value = { name: '', location: '', minPrice: '', maxPrice: '', bedrooms: '', alertEnabled: true }
  showNewSearch.value = false
  alert('Search saved successfully!')
}
</script>
