<template>
  <div class="space-y-5">
    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
        <!-- Type Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Type</label>
          <select v-model="filters.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Types</option>
            <option value="residential">Residential</option>
            <option value="commercial">Commercial</option>
            <option value="industrial">Industrial</option>
          </select>
        </div>

        <!-- Location Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
          <input
            v-model="filters.location"
            type="text"
            placeholder="Search location..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
        </div>

        <!-- Price Range Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Price Range</label>
          <select v-model="filters.priceRange" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Any Price</option>
            <option value="0-5000">0 - 5,000 ETB</option>
            <option value="5000-10000">5,000 - 10,000 ETB</option>
            <option value="10000-20000">10,000 - 20,000 ETB</option>
            <option value="20000+">20,000+ ETB</option>
          </select>
        </div>

        <!-- Bedrooms Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Bedrooms</label>
          <select v-model="filters.bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Any</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3+</option>
          </select>
        </div>

        <!-- Bathrooms Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Bathrooms</label>
          <select v-model="filters.bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Any</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3+</option>
          </select>
        </div>

        <!-- Furnishing Filter -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Furnishing</label>
          <select v-model="filters.furnishing" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Any</option>
            <option value="furnished">Furnished</option>
            <option value="unfurnished">Unfurnished</option>
            <option value="semi-furnished">Semi-Furnished</option>
          </select>
        </div>
      </div>

      <!-- Sort & Clear Buttons -->
      <div class="flex gap-3 mt-4 pt-4 border-t border-gray-200">
        <select v-model="sort" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="">Sort By: Newest</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="featured">Most Viewed</option>
        </select>
        <button
          @click="clearFilters"
          class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
        >
          Clear Filters
        </button>
      </div>
    </div>

    <!-- Results Count -->
    <div class="flex items-center justify-between">
      <p class="text-sm text-gray-600">
        Showing <span class="font-semibold">{{ filteredProperties.length }}</span> properties
      </p>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
      <template v-for="property in filteredProperties" :key="property.id">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
          <!-- Image -->
          <div class="relative h-48 bg-gray-200 overflow-hidden">
            <img
              :src="property.image"
              :alt="property.name"
              class="w-full h-full object-cover hover:scale-110 transition-transform"
            />
            <div class="absolute top-3 right-3 flex gap-2">
              <!-- Favorite Button -->
              <button
                @click="toggleFavorite(property)"
                :class="[
                  'w-9 h-9 rounded-full flex items-center justify-center transition-colors',
                  property.favorite
                    ? 'bg-red-600 text-white'
                    : 'bg-white/90 text-gray-600 hover:bg-white'
                ]"
              >
                <svg class="w-5 h-5" :fill="property.favorite ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
              <!-- Featured Badge -->
              <span v-if="property.featured" class="px-2 py-1 bg-emerald-600 text-white text-xs font-bold rounded">
                Featured
              </span>
            </div>
            <div v-if="property.status" :class="property.statusClass" class="absolute bottom-3 left-3 px-3 py-1 rounded text-xs font-bold text-white">
              {{ property.status }}
            </div>
          </div>

          <!-- Details -->
          <div class="p-4">
            <div class="mb-2">
              <h3 class="font-bold text-gray-900 text-sm line-clamp-2">{{ property.name }}</h3>
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
            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
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

            <!-- CTA Button -->
            <button
              @click="viewDetails(property)"
              class="w-full px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors text-sm"
            >
              View Details
            </button>
          </div>
        </div>
      </template>

      <!-- Empty State -->
      <div v-if="filteredProperties.length === 0" class="col-span-full text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21l-7-5m0 0l-7 5m7-5v6m0-6L2.672 9.26m15.656 0l2.672-4.26m-2.672 4.26l-5.656-4.26m5.656 4.26l7-5m-7 5v10"/>
        </svg>
        <p class="text-gray-500 font-medium">No properties found matching your criteria</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const filters = ref({
  type: '',
  location: '',
  priceRange: '',
  bedrooms: '',
  bathrooms: '',
  furnishing: '',
})

const sort = ref('')

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
    furnishing: 'furnished',
    featured: true,
    status: 'Available',
    statusClass: 'bg-emerald-600',
    favorite: false,
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
    furnishing: 'semi-furnished',
    featured: false,
    status: null,
    statusClass: '',
    favorite: false,
  },
  {
    id: 3,
    name: 'Nifas Silk Studio',
    location: 'Nifas Silk, Addis Ababa',
    price: 8000,
    beds: 1,
    baths: 1,
    area: 50,
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop',
    type: 'residential',
    furnishing: 'unfurnished',
    featured: false,
    status: null,
    statusClass: '',
    favorite: false,
  },
  {
    id: 4,
    name: 'Piazza Contemporary Suite',
    location: 'Piazza, Addis Ababa',
    price: 18000,
    beds: 2,
    baths: 1,
    area: 100,
    image: 'https://images.unsplash.com/photo-1534080564897-61f3b3f786d7?w=400&h=300&fit=crop',
    type: 'residential',
    furnishing: 'furnished',
    featured: true,
    status: 'Hot',
    statusClass: 'bg-red-600',
    favorite: false,
  },
  {
    id: 5,
    name: 'Summit Residence Villa',
    location: 'Summit, Addis Ababa',
    price: 35000,
    beds: 4,
    baths: 3,
    area: 350,
    image: 'https://images.unsplash.com/photo-1512216548029-956a92485718?w=400&h=300&fit=crop',
    type: 'residential',
    furnishing: 'furnished',
    featured: false,
    status: null,
    statusClass: '',
    favorite: false,
  },
  {
    id: 6,
    name: 'Industrial Warehouse',
    location: 'Bole Medhanealem, Addis Ababa',
    price: 12000,
    beds: 0,
    baths: 1,
    area: 500,
    image: 'https://images.unsplash.com/photo-1565950621951-122b2acc801b?w=400&h=300&fit=crop',
    type: 'industrial',
    furnishing: 'unfurnished',
    featured: false,
    status: null,
    statusClass: '',
    favorite: false,
  },
])

const filteredProperties = computed(() => {
  let result = properties.value

  if (filters.value.type) {
    result = result.filter(p => p.type === filters.value.type)
  }

  if (filters.value.location) {
    result = result.filter(p =>
      p.location.toLowerCase().includes(filters.value.location.toLowerCase())
    )
  }

  if (filters.value.priceRange) {
    const [min, max] = filters.value.priceRange.split('-').map(v => v.replace('+', '') * 1)
    result = result.filter(p => p.price >= min && (max ? p.price <= max : true))
  }

  if (filters.value.bedrooms) {
    const beds = parseInt(filters.value.bedrooms)
    result = result.filter(p => beds === 3 ? p.beds >= 3 : p.beds === beds)
  }

  if (filters.value.bathrooms) {
    const baths = parseInt(filters.value.bathrooms)
    result = result.filter(p => baths === 3 ? p.baths >= 3 : p.baths === baths)
  }

  if (filters.value.furnishing) {
    result = result.filter(p => p.furnishing === filters.value.furnishing)
  }

  // Sort
  if (sort.value === 'price-asc') {
    result.sort((a, b) => a.price - b.price)
  } else if (sort.value === 'price-desc') {
    result.sort((a, b) => b.price - a.price)
  } else if (sort.value === 'featured') {
    result.sort((a, b) => (b.featured ? 1 : 0) - (a.featured ? 1 : 0))
  }

  return result
})

const clearFilters = () => {
  filters.value = { type: '', location: '', priceRange: '', bedrooms: '', bathrooms: '', furnishing: '' }
  sort.value = ''
}

const toggleFavorite = (property) => {
  property.favorite = !property.favorite
}

const viewDetails = (property) => {
  alert(`View details for ${property.name}`)
}
</script>
