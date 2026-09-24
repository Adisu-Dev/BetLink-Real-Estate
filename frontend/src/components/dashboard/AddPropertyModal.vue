<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <!-- Modal Dialog -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-300 dark:border-slate-800 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
      
      <!--  Header -->
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/80 dark:bg-slate-800/80">
        <div class="flex items-center gap-2.5">
          <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            {{ isEditing ? 'Edit Property Listing' : 'Add Property Listing' }}
          </h2>
          <span
            v-if="isEditing && isDirty"
            class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"
          >
            ● Unsaved Changes
          </span>
          <span
            v-else-if="isEditing && !isDirty"
            class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-300 dark:border-slate-700"
          >
            No Changes
          </span>
        </div>
        <button
          type="button"
          @click="closeModal"
          class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Step Progress Bar -->
      <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 shrink-0">
        <div 
          class="bg-slate-900 dark:bg-white h-full transition-all duration-300 rounded-r-full"
          :style="{ width: `${(currentStep / 4) * 100}%` }"
        ></div>
      </div>

      <!-- Form Content (Scrollable) -->
      <div class="p-6 overflow-y-auto flex-1 space-y-5">
        
        <!-- STEP 1: Basic Info -->
        <div v-if="currentStep === 1" class="space-y-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                Property Title <span class="text-rose-500">*</span>
              </label>
              <button
                v-if="form.title"
                type="button"
                @click="form.title = ''"
                class="text-[11px] font-semibold text-slate-400 hover:text-rose-500 transition-colors cursor-pointer"
              >
                Clear
              </button>
            </div>
            <div class="relative">
              <input
                v-model="form.title"
                @focus="$event.target.select()"
                type="text"
                required
                placeholder="e.g., Modern 3-Bedroom Apartment in Bole Atlas"
                :class="[
                  'w-full px-3.5 py-2.5 pr-8 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-xl focus:outline-none transition-colors',
                  errors.title
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                    : 'border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              />
              <button
                v-if="form.title"
                type="button"
                @click="form.title = ''"
                class="absolute right-2.5 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer"
              >
                <X class="w-3.5 h-3.5" />
              </button>
            </div>
            <p v-if="errors.title" class="mt-1 text-xs text-red-500 font-medium">{{ errors.title }}</p>
          </div>

          <!-- Transaction Type Dropdown -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
              Listing Transaction Type <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.listing_type"
              required
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors cursor-pointer"
            >
              <option value="sale">For Sale</option>
              <option value="rent">For Rent</option>
              <option value="short_rent">Short Stay</option>
            </select>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                Property Description <span class="text-rose-500">*</span>
              </label>
              <button
                v-if="form.description"
                type="button"
                @click="form.description = ''"
                class="text-[11px] font-semibold text-slate-400 hover:text-rose-500 transition-colors cursor-pointer"
              >
                Clear
              </button>
            </div>
            <textarea
              v-model="form.description"
              @focus="$event.target.select()"
              rows="4"
              required
              placeholder="Describe property features, views, security, power backup, and surrounding amenities..."
              :class="[
                'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs sm:text-sm rounded-xl focus:outline-none transition-colors',
                errors.description
                  ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                  : 'border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
              ]"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-xs text-red-500 font-medium">{{ errors.description }}</p>
          </div>
        </div>

        <!-- STEP 2: Location & Address -->
        <div v-else-if="currentStep === 2" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- City Input with Datalist of major Ethiopian cities -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                City <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.city_name"
                list="city-options"
                @focus="$event.target.select()"
                type="text"
                required
                placeholder="e.g. Addis Ababa, Hawassa, Adama..."
                :class="[
                  'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs rounded-xl focus:outline-none transition-colors',
                  errors.city_name
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                    : 'border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              />
              <datalist id="city-options">
                <option value="Addis Ababa" />
                <option value="Hawassa" />
                <option value="Adama" />
                <option value="Bahir Dar" />
                <option value="Bishoftu" />
                <option value="Dire Dawa" />
                <option value="Gondar" />
                <option value="Mekelle" />
                <option value="Jimma" />
                <option value="Dessie" />
              </datalist>
              <p v-if="errors.city_name" class="mt-1 text-xs text-red-500 font-medium">{{ errors.city_name }}</p>
            </div>

            <!-- Sub-City / Zone Flexible Input with Datalist -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Sub-City / Zone <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.sub_city_name"
                list="subcity-options"
                @focus="$event.target.select()"
                type="text"
                required
                placeholder="e.g. Bole, Kirkos, Tabor, Menaharia..."
                :class="[
                  'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs rounded-xl focus:outline-none transition-colors',
                  errors.sub_city_name
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                    : 'border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              />
              <datalist id="subcity-options">
                <option value="Bole" />
                <option value="Kirkos" />
                <option value="Yeka" />
                <option value="Arada" />
                <option value="Lideta" />
                <option value="Kolfe Keranio" />
                <option value="Nifas Silk-Lafto" />
                <option value="Gulele" />
                <option value="Akaki Kaliti" />
                <option value="Addis Ketema" />
                <option value="Lemi Kura" />
                <option value="Tabor" />
                <option value="Menaharia" />
                <option value="Hayk Dar" />
                <option value="Belay Zeleke" />
                <option value="Fasilo" />
                <option value="Gish Abay" />
                <option value="Boku" />
                <option value="Lugo" />
              </datalist>
              <p v-if="errors.sub_city_name" class="mt-1 text-xs text-red-500 font-medium">{{ errors.sub_city_name }}</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
              District / Area / Street
            </label>
            <input
              v-model="form.street"
              @focus="$event.target.select()"
              type="text"
              placeholder="e.g. Cameroon Street, Atlas, Edna Mall, Woreda 03, CMC Phase 2"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
            />
          </div>
        </div>

        <!-- STEP 3: Specs & Pricing -->
        <div v-else-if="currentStep === 3" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Price (ETB) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model.number="form.price"
                @focus="$event.target.select()"
                type="number"
                min="1"
                required
                placeholder="e.g. 4500000"
                :class="[
                  'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border text-slate-900 dark:text-white text-xs font-bold rounded-xl focus:outline-none transition-colors',
                  errors.price
                    ? 'border-red-500 focus:ring-2 focus:ring-red-500'
                    : 'border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400'
                ]"
              />
              <p v-if="errors.price" class="mt-1 text-xs text-red-500 font-medium">{{ errors.price }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Area (m²)
              </label>
              <input
                v-model.number="form.area"
                @focus="$event.target.select()"
                type="number"
                min="1"
                placeholder="e.g. 145"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Bedrooms
              </label>
              <input
                v-model.number="form.bedrooms"
                @focus="$event.target.select()"
                type="number"
                min="0"
                max="20"
                placeholder="e.g. 3"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                Bathrooms
              </label>
              <input
                v-model.number="form.bathrooms"
                @focus="$event.target.select()"
                type="number"
                min="0"
                max="20"
                placeholder="e.g. 2"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-colors"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2">
              Publishing Intent & Market Visibility
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label
                :class="[
                  'flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                  form.status === 'active'
                    ? 'bg-slate-50 dark:bg-slate-800/80 border-slate-900 dark:border-white ring-1 ring-slate-900 dark:ring-white'
                    : 'bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 hover:border-slate-400'
                ]"
              >
                <input type="radio" v-model="form.status" value="active" class="mt-0.5 cursor-pointer" />
                <div>
                  <span class="text-xs font-bold text-slate-900 dark:text-white block">Publish to Marketplace</span>
                  <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-snug">
                    Immediately live on public pages for guests & buyers; queued for Admin Verification.
                  </span>
                </div>
              </label>

              <label
                :class="[
                  'flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                  form.status === 'draft'
                    ? 'bg-slate-50 dark:bg-slate-800/80 border-slate-900 dark:border-white ring-1 ring-slate-900 dark:ring-white'
                    : 'bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700 hover:border-slate-400'
                ]"
              >
                <input type="radio" v-model="form.status" value="draft" class="mt-0.5 cursor-pointer" />
                <div>
                  <span class="text-xs font-bold text-slate-900 dark:text-white block">Save as Draft</span>
                  <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-snug">
                    Kept private in your owner dashboard. Not visible to guests or public search.
                  </span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- STEP 4: Photo Gallery & Uploads -->
        <div v-else-if="currentStep === 4" class="space-y-4">
          <!-- Drag & Drop / File Browser Zone -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
              Property Photos <span class="text-rose-500">*</span>
            </label>

            <!-- Hidden File Input -->
            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/png,image/webp,image/jpg"
              multiple
              @change="handleFileUpload"
              class="hidden"
            />

            <!-- Drag & Drop Card -->
            <div
              @click="$refs.fileInputRef && $refs.fileInputRef.click()"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleFileDrop"
              :class="[
                'border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2',
                isDragging
                  ? 'border-slate-900 dark:border-white bg-slate-100 dark:bg-slate-800'
                  : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600 bg-slate-50/50 dark:bg-slate-800/40'
              ]"
            >
              <div v-if="isUploading" class="flex flex-col items-center gap-2 py-2">
                <Loader2 class="w-8 h-8 text-slate-900 dark:text-white animate-spin" />
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Uploading photo from device...</p>
              </div>
              <template v-else>
                <div class="w-11 h-11 rounded-2xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-300 shadow-2xs">
                  <UploadCloud class="w-5 h-5" />
                </div>
                <div>
                  <p class="text-xs font-bold text-slate-900 dark:text-white">
                    Click to browse files <span class="text-slate-400 font-normal">or drag & drop</span>
                  </p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Upload from laptop or phone (JPG, PNG, WEBP up to 10MB)
                  </p>
                </div>
              </template>
            </div>
          </div>

          <!-- Uploaded Preview Grid -->
          <div v-if="form.images.length > 0" class="space-y-2 pt-2">
            <div class="flex items-center justify-between text-xs text-slate-500">
              <span class="font-bold text-slate-900 dark:text-white">Uploaded Photos ({{ form.images.length }})</span>
              <span class="text-[11px] text-slate-400">First photo is used as the cover listing image</span>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
              <div
                v-for="(img, idx) in form.images"
                :key="idx"
                class="relative aspect-video rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 group bg-slate-100 dark:bg-slate-800 shadow-2xs"
              >
                <img :src="img" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button
                    type="button"
                    @click="removeImage(idx)"
                    class="p-1.5 rounded-lg bg-rose-600 text-white text-xs hover:bg-rose-700 cursor-pointer transition-colors"
                    title="Remove photo"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
                <span v-if="idx === 0" class="absolute bottom-1 left-1 bg-slate-900 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-xs">
                  Primary Cover
                </span>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-4 text-xs text-slate-400 dark:text-slate-500 font-medium">
            No photos added yet. Browse and select property photos above.
          </div>
        </div>

      </div>

      <!-- Modal Footer Controls -->
      <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/80 dark:bg-slate-800/80">
        <button
          v-if="currentStep > 1"
          type="button"
          @click="currentStep--"
          class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
        >
          Back
        </button>
        <div v-else></div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
          >
            Cancel
          </button>

          <button
            v-if="currentStep < 4"
            type="button"
            @click="goToNextStep"
            class="px-5 py-2 text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl transition-colors cursor-pointer"
          >
            Continue
          </button>

          <button
            v-else
            type="button"
            :disabled="isSubmitting || (isEditing && !isDirty)"
            @click="submitProperty"
            class="px-6 py-2 text-xs font-bold rounded-xl transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200"
          >
            {{ isSubmitting ? 'Saving...' : (isEditing && !isDirty ? 'No Changes' : 'Save') }}
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { X, Trash2, UploadCloud, Loader2 } from 'lucide-vue-next'
import { ownerService } from '@/services/ownerService'
import { useToastStore } from '@/stores/toast'

const props = defineProps({
  isOpen: Boolean,
  property: Object
})

const emit = defineEmits(['close', 'saved'])
const toastStore = useToastStore()

const currentStep = ref(1)
const isSubmitting = ref(false)
const isEditing = ref(false)
const initialSnapshot = ref('')

const fileInputRef = ref(null)
const isDragging = ref(false)
const isUploading = ref(false)

const errors = reactive({
  title: '',
  description: '',
  city_name: '',
  sub_city_name: '',
  price: ''
})

const form = reactive({
  id: null,
  title: '',
  description: '',
  listing_type: 'sale',
  city_name: '',
  sub_city_name: '',
  street: '',
  price: null,
  area: null,
  bedrooms: null,
  bathrooms: null,
  status: 'active',
  images: []
})

function clearErrors() {
  errors.title = ''
  errors.description = ''
  errors.city_name = ''
  errors.sub_city_name = ''
  errors.price = ''
}

function takeSnapshot() {
  initialSnapshot.value = JSON.stringify({
    title: form.title?.trim() || '',
    description: form.description?.trim() || '',
    listing_type: form.listing_type || 'sale',
    city_name: form.city_name?.trim() || '',
    sub_city_name: form.sub_city_name || '',
    street: form.street?.trim() || '',
    price: Number(form.price || 0),
    area: Number(form.area || 0),
    bedrooms: Number(form.bedrooms || 0),
    bathrooms: Number(form.bathrooms || 0),
    status: form.status || 'active',
    images: [...form.images]
  })
}

const isDirty = computed(() => {
  if (!isEditing.value) return true
  const currentSnapshot = JSON.stringify({
    title: form.title?.trim() || '',
    description: form.description?.trim() || '',
    listing_type: form.listing_type || 'sale',
    city_name: form.city_name?.trim() || '',
    sub_city_name: form.sub_city_name || '',
    street: form.street?.trim() || '',
    price: Number(form.price || 0),
    area: Number(form.area || 0),
    bedrooms: Number(form.bedrooms || 0),
    bathrooms: Number(form.bathrooms || 0),
    status: form.status || 'active',
    images: [...form.images]
  })
  return currentSnapshot !== initialSnapshot.value
})

watch(() => props.isOpen, (open) => {
  if (open) {
    currentStep.value = 1
    clearErrors()
    if (props.property) {
      isEditing.value = true
      Object.assign(form, {
        id: props.property.id,
        title: props.property.title || '',
        description: props.property.description || '',
        listing_type: props.property.listing_type || 'sale',
        city_name: props.property.address?.city?.name || props.property.city_name || props.property.city || '',
        sub_city_name: props.property.address?.subCity?.name || props.property.sub_city_name || props.property.sub_city || '',
        street: props.property.address?.street || props.property.street || '',
        price: Number(props.property.price || 0),
        area: props.property.area ? Number(props.property.area) : null,
        bedrooms: props.property.bedrooms !== undefined ? Number(props.property.bedrooms) : null,
        bathrooms: props.property.bathrooms !== undefined ? Number(props.property.bathrooms) : null,
        status: props.property.status || 'active',
        images: props.property.images?.map(i => i.url || i) || (props.property.image ? [props.property.image] : [])
      })
      takeSnapshot()
    } else {
      isEditing.value = false
      Object.assign(form, {
        id: null,
        title: '',
        description: '',
        listing_type: 'sale',
        city_name: '',
        sub_city_name: '',
        street: '',
        price: null,
        area: null,
        bedrooms: null,
        bathrooms: null,
        status: 'active',
        images: []
      })
      takeSnapshot()
    }
  }
})

function closeModal() {
  emit('close')
}

function goToNextStep() {
  clearErrors()
  let hasError = false

  if (currentStep.value === 1) {
    if (!form.title || !form.title.trim()) {
      errors.title = 'Property title is required.'
      hasError = true
    } else if (form.title.trim().length < 3) {
      errors.title = 'Title must be at least 3 characters.'
      hasError = true
    }

    if (!form.description || !form.description.trim()) {
      errors.description = 'Property description is required.'
      hasError = true
    } else if (form.description.trim().length < 10) {
      errors.description = 'Description should be at least 10 characters.'
      hasError = true
    }

    if (hasError) {
      toastStore.error('Please fill all required fields before continuing.')
      return
    }
  }

  if (currentStep.value === 2) {
    const rawCity = (form.city_name || '').trim()
    const rawSubCity = (form.sub_city_name || '').trim()

    if (!rawCity) {
      errors.city_name = 'City name is required.'
      hasError = true
    } else if (/^\d+$/.test(rawCity)) {
      errors.city_name = 'City name cannot be only numbers. Please enter a valid name.'
      hasError = true
    } else if (rawCity.length < 2) {
      errors.city_name = 'City must be at least 2 characters.'
      hasError = true
    } else if (!/[a-zA-Z\u1200-\u137F]/.test(rawCity)) {
      errors.city_name = 'City must contain letters.'
      hasError = true
    }

    if (!rawSubCity) {
      errors.sub_city_name = 'Sub-city or zone is required.'
      hasError = true
    } else if (/^\d+$/.test(rawSubCity)) {
      errors.sub_city_name = 'Sub-city cannot be only numbers. Please enter a valid name (e.g. Bole, Tabor).'
      hasError = true
    } else if (rawSubCity.length < 2) {
      errors.sub_city_name = 'Sub-city must be at least 2 characters.'
      hasError = true
    } else if (!/[a-zA-Z\u1200-\u137F]/.test(rawSubCity)) {
      errors.sub_city_name = 'Sub-city must contain letters.'
      hasError = true
    }

    if (hasError) {
      toastStore.error('Please enter valid location details before continuing.')
      return
    }
  }

  if (currentStep.value === 3) {
    if (!form.price || Number(form.price) <= 0) {
      errors.price = 'Please enter a valid price.'
      hasError = true
    }

    if (hasError) {
      toastStore.error('Please specify a valid property price.')
      return
    }
  }

  currentStep.value++
}

async function uploadFiles(files) {
  if (!files || files.length === 0) return
  isUploading.value = true
  try {
    for (const file of Array.from(files)) {
      if (!file.type.startsWith('image/')) {
        toastStore.error(`"${file.name}" is not an image file.`)
        continue
      }
      if (file.size > 10 * 1024 * 1024) {
        toastStore.error(`"${file.name}" exceeds 10MB limit.`)
        continue
      }
      const res = await ownerService.uploadImage(file)
      const url = res?.data?.url || res?.url
      if (url) {
        form.images.push(url)
      }
    }
    toastStore.success('Photo uploaded from device!')
  } catch (err) {
    console.error('File upload failed:', err)
    toastStore.error(err.message || 'Failed to upload photo from device.')
  } finally {
    isUploading.value = false
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

function handleFileUpload(event) {
  const files = event.target.files
  uploadFiles(files)
}

function handleFileDrop(event) {
  isDragging.value = false
  const files = event.dataTransfer.files
  uploadFiles(files)
}

function removeImage(index) {
  form.images.splice(index, 1)
}

async function submitProperty() {
  clearErrors()

  if (isEditing.value && !isDirty.value) {
    toastStore.info('No changes were made to the property.')
    closeModal()
    return
  }

  if (!form.title || !form.title.trim()) {
    toastStore.error('Property title is required.')
    currentStep.value = 1
    return
  }

  if (!form.price || Number(form.price) <= 0) {
    toastStore.error('A valid property price is required.')
    currentStep.value = 3
    return
  }

  if (!form.images || form.images.length === 0) {
    toastStore.error('Please upload at least one property photo.')
    currentStep.value = 4
    return
  }

  isSubmitting.value = true
  try {
    if (isEditing.value && form.id) {
      await ownerService.updateProperty(form.id, form)
      toastStore.success('Property updated successfully!')
    } else {
      await ownerService.createProperty(form)
      toastStore.success('Property created successfully!')
    }
    emit('saved')
    closeModal()
  } catch (err) {
    console.error('Save failed:', err)
    toastStore.error(err.message || 'Failed to save property listing.')
  } finally {
    isSubmitting.value = false
  }
}
</script>
