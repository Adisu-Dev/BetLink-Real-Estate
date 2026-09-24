<template>
  <div class="space-y-5">
    <!-- Filter & Sort Bar -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center justify-between gap-3 flex-wrap">
      <div class="flex items-center gap-3">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search favorites..."
          class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
        />
        <select v-model="filterBy" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="">All</option>
          <option value="residential">Residential</option>
          <option value="commercial">Commercial</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <p class="text-sm text-gray-600">
          <span class="font-semibold">{{ filteredProperties.length }}</span> saved
        </p>
      </div>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <template v-for="property in filteredProperties" :key="property.id">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
          <!-- Image with Actions -->
          <div class="relative h-48 bg-gray-200">
            <img
              :src="property.image"
              :alt="property.name"
              class="w-full h-full object-cover"
            />
            <div class="absolute top-3 right-3 flex gap-2">
              <button
                @click="removeFavorite(property.id)"
                class="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-colors"
              >
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                  <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
              <span v-if="property.featured" class="px-2 py-1 bg-emerald-600 text-white text-xs font-bold rounded">
                Featured
              </span>
            </div>
          </div>

          <!-- Details -->
          <div class="p-4">
            <div class="mb-2">
              <h3 class="font-bold text-gray-900">{{ property.name }}</h3>
              <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                {{ property.location }}
              </p>
            </div>

            <!-- Price -->
            <div class="mb-3 pb-3 border-b border-gray-100">
              <p class="text-lg font-bold text-emerald-600">ETB {{ property.price.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">/month</p>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-3 gap-2 mb-4 text-center text-sm">
              <div>
                <p class="text-xs text-gray-500">Beds</p>
                <p class="font-semibold text-gray-900">{{ property.beds }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Baths</p>
                <p class="font-semibold text-gray-900">{{ property.baths }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Area</p>
                <p class="font-semibold text-gray-900">{{ property.area }}m²</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
              <button
                @click="viewDetails(property)"
                class="flex-1 px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors text-sm"
              >
                View
              </button>
              <button
                @click="compareProperty(property)"
                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors text-sm"
              >
                Compare
              </button>
            </div>
          </div>
        </div>
      </template>

      <!-- Empty State -->
      <div v-if="filteredProperties.length === 0" class="col-span-full text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <p class="text-gray-500 font-medium">No saved properties</p>
        <p class="text-sm text-gray-400 mt-2">Start adding your favorite properties to see them here</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const filterBy = ref('')

const properties = ref([
  {
    id: 1,
    name: 'Bole Luxury Apartment',
    location: 'Bole, Addis Ababa',
    price: 15000,
    beds: 2,
    baths: 2,
    area: 120,
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop',
    type: 'residential',
    featured: true,
  },
  {
    id: 2,
    name: 'CMC Commercial Office',
    location: 'CMC, Addis Ababa',
    price: 25000,
    beds: 0,
    baths: 2,
    area: 250,
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400&h=300&fit=crop',
    type: 'commercial',
    featured: false,
  },
  {
    id: 3,
    name: 'Piazza Contemporary Suite',
    location: 'Piazza, Addis Ababa',
    price: 18000,
    beds: 2,
    baths: 1,
    area: 100,
    image: 'https://images.unsplash.com/photo-1534080564897-61f3b3f786d7?w=400&h=300&fit=crop',
    type: 'residential',
    featured: true,
  },
  {
    id: 4,
    name: 'Summit Residence Villa',
    location: 'Summit, Addis Ababa',
    price: 35000,
    beds: 4,
    baths: 3,
    area: 350,
    image: 'https://images.unsplash.com/photo-1512216548029-956a92485718?w=400&h=300&fit=crop',
    type: 'residential',
    featured: false,
  },
])

const filteredProperties = computed(() => {
  let result = properties.value

  if (searchQuery.value) {
    result = result.filter(p =>
      p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.location.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (filterBy.value) {
    result = result.filter(p => p.type === filterBy.value)
  }

  return result
})

const removeFavorite = (id) => {
  const idx = properties.value.findIndex(p => p.id === id)
  if (idx !== -1) {
    properties.value.splice(idx, 1)
    alert('Property removed from favorites')
  }
}

const viewDetails = (property) => {
  alert(`View details for ${property.name}`)
}

const compareProperty = (property) => {
  alert(`Add ${property.name} to comparison`)
}
</script>
