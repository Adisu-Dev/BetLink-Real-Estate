<template>
  <div class="max-w-5xl mx-auto space-y-6 pb-16">
    <!-- Top Navigation & Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <router-link
            to="/owner/properties"
            class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
          </router-link>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
          {{ isEdit ? 'Edit Property Listing' : 'Create New Property Listing' }}
        </h1>
        <p class="text-xs sm:text-sm text-gray-600 mt-0.5">
          {{ isEdit ? 'Update details, pricing, location hierarchy, and photos for this listing.' : 'List your Ethiopian real estate property on the BetLink marketplace in Draft status.' }}
        </p>
      </div>

      <div v-if="isEdit && propertyStatus" class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-sm">
        <span class="text-xs font-medium text-gray-500">Current Status:</span>
        <StatusBadge :status="propertyStatus" />
      </div>
    </div>

    <!-- Initial Loading Skeleton for Edit Mode -->
    <div v-if="initialLoading" class="bg-white rounded-2xl border border-gray-200 p-8 space-y-6 animate-pulse shadow-sm">
      <div class="h-6 bg-gray-200 rounded w-1/3"></div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="h-10 bg-gray-200 rounded"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
      </div>
      <div class="h-24 bg-gray-200 rounded"></div>
      <div class="grid grid-cols-3 gap-6">
        <div class="h-10 bg-gray-200 rounded"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- Main Property Form -->
    <form v-else @submit.prevent="handleSubmit" class="space-y-6">
      <!-- General Error Alert -->
      <div
        v-if="generalError"
        class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-medium flex items-start gap-2.5"
      >
        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <div>
          <p class="font-bold">Please correct the highlighted errors before submitting:</p>
          <p class="mt-0.5">{{ generalError }}</p>
        </div>
      </div>

      <!-- Section 1: Basic Information -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">1. Basic Information</h2>
          <p class="text-xs text-gray-500">Provide the title, classification, and detailed description.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
          <!-- Property Title -->
          <div class="md:col-span-2">
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Property Title <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.title"
              @focus="$event.target.select()"
              type="text"
              placeholder="e.g., Luxury 3-Bedroom Furnished Apartment in Bole Medhanealem"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              :class="{ 'border-red-400 bg-red-50/20': errors.title }"
              required
            />
            <p v-if="errors.title" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.title }}</p>
          </div>

          <!-- Listing Type -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Listing Type <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.listing_type"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              :class="{ 'border-red-400 bg-red-50/20': errors.listing_type }"
              required
            >
              <option value="sale">For Sale</option>
              <option value="rent">For Rent</option>
              <option value="short_rent">Short Stay</option>
            </select>
            <p v-if="errors.listing_type" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.listing_type }}</p>
          </div>

          <!-- Property Type (Dynamic from API) -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Property Type <span class="text-red-500">*</span>
            </label>
            <select
              v-model.number="form.property_type_id"
              @change="handlePropertyTypeChange"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              :class="{ 'border-red-400 bg-red-50/20': errors.property_type_id }"
              required
            >
              <option :value="null" disabled>Select Property Type</option>
              <option v-for="pt in propertyTypes" :key="pt.id" :value="pt.id">
                {{ pt.name }}
              </option>
            </select>
            <p v-if="errors.property_type_id" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.property_type_id }}</p>
          </div>

          <!-- Category (Dynamic from API) -->
          <div v-if="filteredCategories.length > 0">
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Category
            </label>
            <select
              v-model.number="form.category_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option :value="null">None / General</option>
              <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Description <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.description"
              @focus="$event.target.select()"
              rows="4"
              placeholder="Provide comprehensive details about the property, rooms, compound, amenities, water reservoir, backup generator, neighborhood security, and nearby landmarks..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              :class="{ 'border-red-400 bg-red-50/20': errors.description }"
              required
            ></textarea>
            <p v-if="errors.description" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.description }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Minimum 20 characters.</p>
          </div>
        </div>
      </div>

      <!-- Section 2: Pricing & Terms -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">2. Pricing & Currency</h2>
          <p class="text-xs text-gray-500">Set the listing price, price unit, and negotiability.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 text-xs">
          <!-- Price -->
          <div class="sm:col-span-2">
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Price (ETB) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input
                v-model.number="form.price"
                @focus="$event.target.select()"
                type="number"
                min="0"
                step="any"
                placeholder="e.g., 25000"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                :class="{ 'border-red-400 bg-red-50/20': errors.price }"
                required
              />
              <span class="absolute right-3.5 top-2.5 font-bold text-gray-400 text-sm">ETB</span>
            </div>
            <p v-if="errors.price" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.price }}</p>
          </div>

          <!-- Price Type -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Price Unit</label>
            <select
              v-model="form.price_type"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="total">Total Amount</option>
              <option value="per_month">Per Month</option>
              <option value="per_night">Per Night</option>
              <option value="per_sqm">Per Sqm</option>
            </select>
          </div>

          <!-- Negotiable Checkbox -->
          <div class="flex items-center pt-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.negotiable"
                type="checkbox"
                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"
              />
              <span class="text-xs font-semibold text-gray-700">Price is Negotiable</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Section 3: Location Hierarchy Cascade -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">3. Location Hierarchy (Ethiopia)</h2>
          <p class="text-xs text-gray-500">Dynamic cascade: Select City/Region, Sub-City/Zone, and Neighborhood.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 text-xs">
          <!-- City / Region -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              City / Region <span class="text-red-500">*</span>
            </label>
            <select
              v-model.number="form.city_id"
              @change="handleCityChange"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              :class="{ 'border-red-400 bg-red-50/20': errors.city_id }"
              required
            >
              <option :value="null" disabled>Select City</option>
              <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <p v-if="errors.city_id" class="text-red-600 text-[11px] mt-1 font-medium">{{ errors.city_id }}</p>
          </div>

          <!-- Sub-City / Zone -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Sub-City / Zone
              <span v-if="loadingSubCities" class="text-emerald-600 text-[10px] ml-1">(Loading...)</span>
            </label>
            <select
              v-model.number="form.sub_city_id"
              @change="handleSubCityChange"
              :disabled="!subCities.length || loadingSubCities"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-100 disabled:text-gray-400"
            >
              <option :value="null">{{ subCities.length ? 'Select Sub-City' : 'No Sub-Cities Available' }}</option>
              <option v-for="sc in subCities" :key="sc.id" :value="sc.id">{{ sc.name }}</option>
            </select>
          </div>

          <!-- Neighborhood / Kebele -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Neighborhood / Area
              <span v-if="loadingNeighborhoods" class="text-emerald-600 text-[10px] ml-1">(Loading...)</span>
            </label>
            <select
              v-model.number="form.neighborhood_id"
              :disabled="!neighborhoods.length || loadingNeighborhoods"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-100 disabled:text-gray-400"
            >
              <option :value="null">{{ neighborhoods.length ? 'Select Neighborhood' : 'No Neighborhoods Available' }}</option>
              <option v-for="nh in neighborhoods" :key="nh.id" :value="nh.id">{{ nh.name }}</option>
            </select>
          </div>

          <!-- Street Address / Landmark -->
          <div class="sm:col-span-2">
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Street Address / Landmark</label>
            <input
              v-model="form.street"
              type="text"
              placeholder="e.g., Near Atlas Hotel / Bole Medhanealem Road"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Kebele / House No -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kebele / House No</label>
            <input
              v-model="form.kebele"
              type="text"
              placeholder="e.g., Kebele 03, House 412"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
        </div>
      </div>

      <!-- Section 4: Specifications -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">4. Property Specifications</h2>
          <p class="text-xs text-gray-500">Dimensions, rooms, structure, and furnishing status.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5 text-xs">
          <!-- Bedrooms -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bedrooms</label>
            <input
              v-model.number="form.bedrooms"
              type="number"
              min="0"
              placeholder="0"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Bathrooms -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bathrooms</label>
            <input
              v-model.number="form.bathrooms"
              type="number"
              min="0"
              placeholder="0"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Area Size -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Area Size</label>
            <div class="flex gap-1.5">
              <input
                v-model.number="form.area"
                type="number"
                min="0"
                step="any"
                placeholder="120"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
              <select
                v-model="form.area_unit"
                class="px-2 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none"
              >
                <option value="sqm">m²</option>
                <option value="sqft">sqft</option>
                <option value="hectare">ha</option>
              </select>
            </div>
          </div>

          <!-- Furnishing -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Furnishing</label>
            <select
              v-model="form.furnished"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
              <option value="unfurnished">Unfurnished</option>
              <option value="semi_furnished">Semi-Furnished</option>
              <option value="furnished">Fully Furnished</option>
            </select>
          </div>

          <!-- Parking Spaces -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Parking Spaces</label>
            <input
              v-model.number="form.parking_spaces"
              type="number"
              min="0"
              placeholder="1"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Floor Number -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Floor Number</label>
            <input
              v-model.number="form.floor_number"
              type="number"
              placeholder="e.g., 3"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Total Floors -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Total Building Floors</label>
            <input
              v-model.number="form.total_floors"
              type="number"
              placeholder="e.g., 8"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <!-- Year Built -->
          <div>
            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Year Built</label>
            <input
              v-model.number="form.year_built"
              type="number"
              min="1950"
              :max="new Date().getFullYear()"
              placeholder="2024"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
        </div>
      </div>

      <!-- Section 5: Amenities (Dynamic from API) -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">5. Amenities & Facilities</h2>
          <p class="text-xs text-gray-500">Select all amenities available on-site.</p>
        </div>

        <div v-if="amenities.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 text-xs">
          <label
            v-for="amenity in amenities"
            :key="amenity.id"
            class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/40 cursor-pointer transition-colors"
            :class="{ 'bg-emerald-50/60 border-emerald-400 font-semibold': form.amenity_ids.includes(amenity.id) }"
          >
            <input
              v-model="form.amenity_ids"
              type="checkbox"
              :value="amenity.id"
              class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500"
            />
            <span class="text-gray-800">{{ amenity.name }}</span>
          </label>
        </div>
        <p v-else class="text-xs text-gray-400">Loading amenities...</p>
      </div>

      <!-- Section 6: Key Features (Dynamic Name/Value Rows) -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">6. Additional Custom Features</h2>
            <p class="text-xs text-gray-500">Add dynamic feature-value pairs (e.g., View: Mountain View, Water Source: Borehole).</p>
          </div>
          <button
            type="button"
            @click="addFeatureRow"
            class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold transition-colors flex items-center gap-1"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Feature
          </button>
        </div>

        <div v-if="form.features.length > 0" class="space-y-2.5">
          <div
            v-for="(feature, index) in form.features"
            :key="index"
            class="flex items-center gap-3"
          >
            <input
              v-model="feature.feature"
              type="text"
              placeholder="Feature (e.g., Water Supply, View, Security)"
              class="flex-1 px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
            <input
              v-model="feature.value"
              type="text"
              placeholder="Value (e.g., 24/7 Borehole + City, Mountain View)"
              class="flex-1 px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
            <button
              type="button"
              @click="removeFeatureRow(index)"
              class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
              title="Remove Feature"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
        <p v-else class="text-xs text-gray-400 italic">No custom features added. Click "+ Add Feature" to add custom highlights.</p>
      </div>

      <!-- Section 7: Property Image Management -->
      <!-- In Edit Mode: Dedicated Reusable Image Manager with Upload, Reorder, Delete, Set Primary -->
      <div v-if="isEdit" class="space-y-4">
        <OwnerPropertyImageManager
          :property-id="propertyId"
          :initial-images="existingImages"
          @update:images="existingImages = $event"
        />
      </div>

      <!-- In Create Mode: Initial Photo Picker -->
      <div v-else class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
          <h2 class="text-base font-bold text-gray-900">7. Property Photos</h2>
          <p class="text-xs text-gray-500">Select initial photos for your property (JPG, PNG, WEBP up to 5MB each, max 20 photos).</p>
        </div>

        <div>
          <label
            for="createInitialImagesInput"
            class="border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer transition-colors bg-gray-50/50 hover:bg-emerald-50/10 text-center"
          >
            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm font-semibold text-gray-700">Click to select photos for this listing</p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG, WEBP up to 5MB each</p>
            <input
              id="createInitialImagesInput"
              type="file"
              multiple
              accept="image/jpeg,image/png,image/jpg,image/webp"
              class="hidden"
              @change="handleCreateImageSelection"
            />
          </label>
        </div>

        <div v-if="createSelectedPreviews.length > 0" class="space-y-3">
          <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Selected Photos ({{ createSelectedPreviews.length }})</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
            <div
              v-for="(prev, i) in createSelectedPreviews"
              :key="i"
              class="relative group rounded-xl overflow-hidden border border-gray-200 aspect-square bg-gray-100"
            >
              <img :src="prev" alt="Preview" class="w-full h-full object-cover" />
              <button
                type="button"
                @click="removeCreateSelectedImage(i)"
                class="absolute top-1.5 right-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shadow"
              >
                ✕
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
        <router-link
          to="/owner/properties"
          class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-colors"
        >
          Cancel
        </router-link>

        <button
          type="submit"
          :disabled="isSubmitting"
          class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors disabled:opacity-50 flex items-center gap-2 shadow-sm"
        >
          <svg v-if="isSubmitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ isSubmitting ? 'Saving Property...' : (isEdit ? 'Save Changes' : 'Create Property') }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { propertyService } from '@/services/propertyService'
import { locationService } from '@/services/locationService'
import { useToast } from '@/composables/useToast'
import StatusBadge from '@/components/dashboard/StatusBadge.vue'
import OwnerPropertyImageManager from '@/components/property/OwnerPropertyImageManager.vue'

const router = useRouter()
const route = useRoute()
const { success, error } = useToast()

const isEdit = computed(() => !!route.params.id)
const propertyId = computed(() => route.params.id)

const initialLoading = ref(false)
const isSubmitting = ref(false)
const generalError = ref(null)
const propertyStatus = ref(null)

const loadingSubCities = ref(false)
const loadingNeighborhoods = ref(false)

const propertyTypes = ref([])
const allCategories = ref([])
const amenities = ref([])
const cities = ref([])
const subCities = ref([])
const neighborhoods = ref([])
const existingImages = ref([])

const createSelectedFiles = ref([])
const createSelectedPreviews = ref([])

const form = reactive({
  title: '',
  description: '',
  property_type_id: null,
  category_id: null,
  listing_type: 'sale',
  price: null,
  price_type: 'total',
  currency: 'ETB',
  negotiable: false,
  city_id: null,
  sub_city_id: null,
  neighborhood_id: null,
  street: '',
  kebele: '',
  bedrooms: 2,
  bathrooms: 2,
  area: 120,
  area_unit: 'sqm',
  floor_number: 1,
  total_floors: 5,
  year_built: 2024,
  furnished: 'unfurnished',
  parking_spaces: 1,
  amenity_ids: [],
  features: [],
})

const errors = reactive({
  title: '',
  description: '',
  property_type_id: '',
  listing_type: '',
  price: '',
  city_id: '',
})

// Categories filtered by selected property_type_id
const filteredCategories = computed(() => {
  if (!form.property_type_id) return allCategories.value
  return allCategories.value.filter(c => c.property_type_id === form.property_type_id)
})

// Feature Row Handlers
const addFeatureRow = () => {
  form.features.push({ feature: '', value: '' })
}

const removeFeatureRow = (index) => {
  form.features.splice(index, 1)
}

// Create Mode: Image Selection
const handleCreateImageSelection = (event) => {
  const files = Array.from(event.target.files || [])
  files.forEach(file => {
    if (file.size > 5 * 1024 * 1024) {
      error('File too large', `${file.name} exceeds the 5MB limit.`)
      return
    }
    createSelectedFiles.value.push(file)

    const reader = new FileReader()
    reader.onload = (e) => {
      createSelectedPreviews.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  })
}

const removeCreateSelectedImage = (index) => {
  createSelectedFiles.value.splice(index, 1)
  createSelectedPreviews.value.splice(index, 1)
}

// Master Data Loaders
const loadMasterData = async () => {
  try {
    const [typesRes, catsRes, amenRes, citiesRes] = await Promise.all([
      propertyService.getPropertyTypes().catch(() => ({ data: [] })),
      propertyService.getCategories().catch(() => ({ data: [] })),
      propertyService.getAmenities().catch(() => ({ data: [] })),
      locationService.getCities().catch(() => ({ data: [] })),
    ])

    propertyTypes.value = typesRes.data?.data || typesRes.data || []
    allCategories.value = catsRes.data?.data || catsRes.data || []
    amenities.value = amenRes.data?.data || amenRes.data || []
    cities.value = citiesRes.data?.data || citiesRes.data || []

    // Set defaults if creating and not set
    if (!isEdit.value) {
      if (propertyTypes.value.length > 0 && !form.property_type_id) {
        form.property_type_id = propertyTypes.value[0].id
      }
      if (cities.value.length > 0 && !form.city_id) {
        form.city_id = cities.value[0].id
        await handleCityChange()
      }
    }
  } catch (err) {
    console.error('Failed to load form master data:', err)
  }
}

// Property Type Change Handler
const handlePropertyTypeChange = () => {
  if (form.category_id) {
    const valid = filteredCategories.value.some(c => c.id === form.category_id)
    if (!valid) {
      form.category_id = null
    }
  }
}

// Location Cascade: City Change Handler
const handleCityChange = async () => {
  subCities.value = []
  neighborhoods.value = []
  form.sub_city_id = null
  form.neighborhood_id = null

  if (!form.city_id) return

  loadingSubCities.value = true
  try {
    const res = await locationService.getSubCities(form.city_id)
    subCities.value = res.data?.data || res.data || []
  } catch (err) {
    console.error('Failed to load sub-cities:', err)
  } finally {
    loadingSubCities.value = false
  }
}

// Location Cascade: Sub-City Change Handler
const handleSubCityChange = async () => {
  neighborhoods.value = []
  form.neighborhood_id = null

  if (!form.sub_city_id) return

  loadingNeighborhoods.value = true
  try {
    const res = await locationService.getNeighborhoods(form.sub_city_id)
    neighborhoods.value = res.data?.data || res.data || []
  } catch (err) {
    console.error('Failed to load neighborhoods:', err)
  } finally {
    loadingNeighborhoods.value = false
  }
}

// Load Existing Property Data for Edit Mode
const loadPropertyData = async () => {
  if (!isEdit.value) return

  initialLoading.value = true
  try {
    const res = await propertyService.getOwnerProperty(propertyId.value)
    const property = res.data?.data || res.data

    if (property) {
      propertyStatus.value = property.status
      form.title = property.title || ''
      form.description = property.description || ''
      form.property_type_id = property.property_type_id || property.property_type?.id || null
      form.category_id = property.category_id || property.category?.id || null
      form.listing_type = property.listing_type || 'sale'
      form.price = property.price ? Number(property.price) : null
      form.price_type = property.price_type || 'total'
      form.currency = property.currency || 'ETB'
      form.negotiable = !!property.negotiable
      form.bedrooms = property.bedrooms !== null ? Number(property.bedrooms) : null
      form.bathrooms = property.bathrooms !== null ? Number(property.bathrooms) : null
      form.area = property.area ? Number(property.area) : null
      form.area_unit = property.area_unit || 'sqm'
      form.floor_number = property.floor_number !== null ? Number(property.floor_number) : null
      form.total_floors = property.total_floors !== null ? Number(property.total_floors) : null
      form.year_built = property.year_built ? Number(property.year_built) : null
      form.furnished = property.furnished || 'unfurnished'
      form.parking_spaces = property.parking_spaces !== null ? Number(property.parking_spaces) : 0

      // Address hierarchy
      const addr = property.address
      if (addr) {
        form.city_id = addr.city_id || addr.city?.id || null
        if (form.city_id) {
          const scRes = await locationService.getSubCities(form.city_id)
          subCities.value = scRes.data?.data || scRes.data || []
          form.sub_city_id = addr.sub_city_id || addr.sub_city?.id || null

          if (form.sub_city_id) {
            const nhRes = await locationService.getNeighborhoods(form.sub_city_id)
            neighborhoods.value = nhRes.data?.data || nhRes.data || []
            form.neighborhood_id = addr.neighborhood_id || addr.neighborhood?.id || null
          }
        }
        form.street = addr.street || ''
        form.kebele = addr.kebele || ''
      }

      // Amenities
      if (Array.isArray(property.amenities)) {
        form.amenity_ids = property.amenities.map(a => a.id)
      }

      // Features
      if (Array.isArray(property.features) && property.features.length > 0) {
        form.features = property.features.map(f => ({
          feature: f.feature || '',
          value: f.value || '',
        }))
      }

      // Images
      if (Array.isArray(property.images)) {
        existingImages.value = property.images
      }
    }
  } catch (err) {
    console.error('Failed to load property for edit:', err)
    error('Failed to load property details.')
    router.push('/owner/properties')
  } finally {
    initialLoading.value = false
  }
}

// Form Submit Handler
const handleSubmit = async () => {
  generalError.value = null
  Object.keys(errors).forEach(k => { errors[k] = '' })

  if (!form.title || form.title.trim().length < 5) {
    errors.title = 'Title must be at least 5 characters.'
    return
  }
  if (!form.description || form.description.trim().length < 20) {
    errors.description = 'Description must be at least 20 characters.'
    return
  }
  if (!form.property_type_id) {
    errors.property_type_id = 'Please select a property type.'
    return
  }
  if (!form.price || form.price <= 0) {
    errors.price = 'Please enter a valid price.'
    return
  }
  if (!form.city_id) {
    errors.city_id = 'Please select a city/region.'
    return
  }

  isSubmitting.value = true

  try {
    const validFeatures = form.features
      .filter(f => f.feature && f.feature.trim().length > 0)
      .map(f => ({ feature: f.feature.trim(), value: (f.value || '').trim() }))

    const payload = {
      property_type_id: form.property_type_id,
      category_id: form.category_id || null,
      title: form.title.trim(),
      description: form.description.trim(),
      listing_type: form.listing_type,
      price: form.price,
      price_type: form.price_type,
      currency: form.currency,
      negotiable: !!form.negotiable,
      city_id: form.city_id,
      sub_city_id: form.sub_city_id || null,
      neighborhood_id: form.neighborhood_id || null,
      street: form.street ? form.street.trim() : null,
      kebele: form.kebele ? form.kebele.trim() : null,
      bedrooms: form.bedrooms !== null && form.bedrooms !== '' ? Number(form.bedrooms) : null,
      bathrooms: form.bathrooms !== null && form.bathrooms !== '' ? Number(form.bathrooms) : null,
      area: form.area !== null && form.area !== '' ? Number(form.area) : null,
      area_unit: form.area_unit || 'sqm',
      floor_number: form.floor_number !== null && form.floor_number !== '' ? Number(form.floor_number) : null,
      total_floors: form.total_floors !== null && form.total_floors !== '' ? Number(form.total_floors) : null,
      year_built: form.year_built !== null && form.year_built !== '' ? Number(form.year_built) : null,
      furnished: form.furnished || 'unfurnished',
      parking_spaces: form.parking_spaces !== null && form.parking_spaces !== '' ? Number(form.parking_spaces) : 0,
      amenity_ids: form.amenity_ids || [],
      features: validFeatures,
    }

    if (isEdit.value) {
      await propertyService.updateProperty(propertyId.value, payload)
      success('Property Updated', `Listing '${form.title}' was updated successfully.`)
      router.push('/owner/properties')
    } else {
      const res = await propertyService.createProperty(payload)
      const newProperty = res.data?.data || res.data

      // Upload selected initial photos if any
      if (createSelectedFiles.value.length > 0 && newProperty?.id) {
        const formData = new FormData()
        createSelectedFiles.value.forEach(file => {
          formData.append('images[]', file)
        })
        try {
          await propertyService.uploadImages(newProperty.id, formData)
        } catch (imgErr) {
          console.error('Initial image upload warning:', imgErr)
        }
      }

      success('Property Created', `Listing '${form.title}' created in Draft status.`)
      router.push('/owner/properties')
    }
  } catch (err) {
    console.error('Failed to submit property:', err)
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const errBag = err.response.data.errors
      Object.keys(errBag).forEach(key => {
        if (errors[key] !== undefined) {
          errors[key] = Array.isArray(errBag[key]) ? errBag[key][0] : errBag[key]
        }
      })
      generalError.value = Object.values(errBag).flat().join(', ')
    } else {
      generalError.value = err.response?.data?.message || 'An error occurred while saving the property.'
    }
    error(generalError.value)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  await loadMasterData()
  if (isEdit.value) {
    await loadPropertyData()
  }
})
</script>
