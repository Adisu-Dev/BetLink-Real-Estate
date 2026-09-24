<template>
  <div class="space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('system_settings_title', 'System Settings') }}
        </h1>
      </div>

      <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <button
          type="button"
          @click="loadSettings"
          :disabled="loading || isSaving"
          class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs disabled:opacity-50"
        >
          🔄
        </button>
      </div>
    </div>

    <!-- Error State  -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadSettings"
        class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer"
      >
        {{ t('retry_loading', 'Retry Loading') }}
      </button>
    </div>

    <!-- Loading  -->
    <div v-else-if="loading" class="space-y-6">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 animate-pulse space-y-4">
        <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
        <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
        <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
      </div>
    </div>

    <!-- Settings Form -->
    <form v-else @submit.prevent="saveSettings" class="space-y-6">
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 transition-colors space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
          <span class="text-xl">🌐</span>
          <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            Platform Identity & Public Contacts
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Platform Name <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.site_name"
              type="text"
              placeholder="e.g. BetLink"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.site_name ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.site_name" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.site_name }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Platform Tagline
            </label>
            <input
              v-model="form.site_tagline"
              type="text"
              placeholder="e.g. Ethiopia's #1 Property Marketplace"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
            />
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Support & Inquiries Email <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.site_email"
              type="email"
              placeholder="e.g. support@betlink.et"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.site_email ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.site_email" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.site_email }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Support Phone Number <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.site_phone"
              type="text"
              placeholder="e.g. +251 911 000 000"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.site_phone ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.site_phone" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.site_phone }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Default Currency <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.currency"
              type="text"
              placeholder="e.g. ETB"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all font-mono uppercase"
              :class="formErrors.currency ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.currency" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.currency }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
             Symbol <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.currency_symbol"
              type="text"
              placeholder="e.g. Br"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all font-semibold"
              :class="formErrors.currency_symbol ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.currency_symbol" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.currency_symbol }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 transition-colors space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
          <span class="text-xl">🏠</span>
          <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            Property Moderation & Upload Rules
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Max Images per Property <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="form.max_images_per_property"
              type="number"
              min="1"
              max="50"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.max_images_per_property ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.max_images_per_property" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.max_images_per_property }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Listing Duration (Days) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="form.property_expiry_days"
              type="number"
              min="7"
              max="365"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.property_expiry_days ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.property_expiry_days" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.property_expiry_days }}</p>
          </div>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3.5">
          <input
            id="moderationToggle"
            v-model="form.property_approval_required"
            type="checkbox"
            class="w-5 h-5 rounded text-slate-900 dark:text-white focus:ring-slate-400 cursor-pointer"
          />
          <label for="moderationToggle" class="cursor-pointer select-none">
            <span class="text-sm font-bold text-slate-900 dark:text-white block">
              Require Administrator Approval Before Publishing
            </span>
          </label>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 transition-colors space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
          <span class="text-xl">💳</span>
          <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            Platform Fees & Tour Viewing Durations
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Platform Service Fee (%) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="form.service_fee_percentage"
              type="number"
              step="0.5"
              min="0"
              max="50"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.service_fee_percentage ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.service_fee_percentage" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.service_fee_percentage }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Default Tour Duration (Minutes) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model.number="form.appointment_duration_default"
              type="number"
              min="15"
              max="240"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.appointment_duration_default ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.appointment_duration_default" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.appointment_duration_default }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 transition-colors space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
          <span class="text-xl">🔍</span>
          <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            Search Engine Optimization (SEO) Defaults
          </h2>
        </div>

        <div class="space-y-5">
          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Default Page Title <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.meta_title"
              type="text"
              placeholder="e.g. BetLink — Find Your Dream Property in Ethiopia"
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.meta_title ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            />
            <p v-if="formErrors.meta_title" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.meta_title }}</p>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
              Default Meta Description <span class="text-rose-500">*</span>
            </label>
            <textarea
              v-model="form.meta_description"
              rows="2"
              placeholder="Brief summary snippet indexed by search engines..."
              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-sm transition-all"
              :class="formErrors.meta_description ? 'border-rose-500 dark:border-rose-500' : 'border-slate-300 dark:border-slate-700'"
            ></textarea>
            <p v-if="formErrors.meta_description" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.meta_description }}</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons Bar: Single line, gap-2, bg-slate-300, no hover, disabled when not dirty -->
      <div class="flex items-center justify-end gap-2 pt-6 border-t border-slate-200/80 dark:border-slate-800 mt-6">
        <button
          type="button"
          @click="resetToDefaults"
          :disabled="!isDirty || isSaving"
          class="px-3.5 py-2 bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        >
          Reset
        </button>
        <button
          type="button"
          @click="cancelChanges"
          :disabled="!isDirty || isSaving"
          class="px-3.5 py-2 bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs shadow-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="!isDirty || isSaving"
          class="px-3.5 py-2 bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs shadow-xs disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1.5 cursor-pointer"
        >
          <span v-if="isSaving" class="animate-spin">⌛</span>
          <span>{{ isSaving ? 'Saving...' : 'Save' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { adminService } from '../../services/adminService'
import { useLanguage } from '../../composables/useLanguage'
import { useToast } from '../../composables/useToast'

const { t } = useLanguage()
const { success, error } = useToast()

const loading = ref(true)
const isSaving = ref(false)
const apiError = ref(null)

const originalSettings = ref('')
const isDirty = computed(() => !loading.value && originalSettings.value !== '' && JSON.stringify(form) !== originalSettings.value)

const form = reactive({
  site_name: 'BetLink',
  site_tagline: "Ethiopia's #1 Property Marketplace",
  site_email: 'info@betlink.et',
  site_phone: '+251911000000',
  currency: 'ETB',
  currency_symbol: 'Br',
  max_images_per_property: 20,
  property_approval_required: true,
  featured_properties_count: 12,
  property_expiry_days: 90,
  service_fee_percentage: 5,
  appointment_duration_default: 30,
  meta_title: 'BetLink — Find Your Dream Property in Ethiopia',
  meta_description: 'Buy, sell, and rent properties across Ethiopia on BetLink — the #1 property marketplace.',
})

const formErrors = reactive({
  site_name: '',
  site_email: '',
  site_phone: '',
  currency: '',
  currency_symbol: '',
  max_images_per_property: '',
  property_expiry_days: '',
  service_fee_percentage: '',
  appointment_duration_default: '',
  meta_title: '',
  meta_description: '',
})

function clearErrors() {
  Object.keys(formErrors).forEach(k => {
    formErrors[k] = ''
  })
}

function validateForm() {
  clearErrors()
  let isValid = true

  // 1. Site Name
  if (!form.site_name || !form.site_name.trim()) {
    formErrors.site_name = 'Platform Name is required.'
    isValid = false
  } else if (form.site_name.trim().length < 2) {
    formErrors.site_name = 'Platform Name must be at least 2 characters.'
    isValid = false
  }

  // 2. Email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.site_email || !form.site_email.trim()) {
    formErrors.site_email = 'Support Email is required.'
    isValid = false
  } else if (!emailRegex.test(form.site_email.trim())) {
    formErrors.site_email = 'Please provide a valid email format (e.g. info@betlink.et).'
    isValid = false
  }

  // 3. Phone
  if (!form.site_phone || !form.site_phone.trim()) {
    formErrors.site_phone = 'Support Phone Number is required.'
    isValid = false
  } else if (form.site_phone.trim().length < 5) {
    formErrors.site_phone = 'Please enter a valid phone number.'
    isValid = false
  }

  // 4. Currency
  if (!form.currency || !form.currency.trim()) {
    formErrors.currency = 'Currency Code is required.'
    isValid = false
  }

  if (!form.currency_symbol || !form.currency_symbol.trim()) {
    formErrors.currency_symbol = 'Currency Symbol is required.'
    isValid = false
  }

  // 5. Max Images
  const maxImages = Number(form.max_images_per_property)
  if (isNaN(maxImages) || maxImages < 1 || maxImages > 50) {
    formErrors.max_images_per_property = 'Max images must be between 1 and 50.'
    isValid = false
  }

  // 6. Expiry Days
  const expiryDays = Number(form.property_expiry_days)
  if (isNaN(expiryDays) || expiryDays < 7 || expiryDays > 365) {
    formErrors.property_expiry_days = 'Listing active duration must be between 7 and 365 days.'
    isValid = false
  }

  // 7. Service Fee %
  const fee = Number(form.service_fee_percentage)
  if (isNaN(fee) || fee < 0 || fee > 50) {
    formErrors.service_fee_percentage = 'Service fee percentage must be between 0% and 50%.'
    isValid = false
  }

  // 8. Tour Duration
  const duration = Number(form.appointment_duration_default)
  if (isNaN(duration) || duration < 15 || duration > 240) {
    formErrors.appointment_duration_default = 'Appointment duration must be between 15 and 240 minutes.'
    isValid = false
  }

  // 9. Meta Title & Description
  if (!form.meta_title || !form.meta_title.trim()) {
    formErrors.meta_title = 'Default SEO Title is required.'
    isValid = false
  }

  if (!form.meta_description || !form.meta_description.trim()) {
    formErrors.meta_description = 'Default SEO Description is required.'
    isValid = false
  }

  return isValid
}

const loadSettings = async () => {
  loading.value = true
  apiError.value = null
  clearErrors()

  try {
    const res = await adminService.getSettings()
    if (res && res.data) {
      const groups = res.data
      if (typeof groups === 'object') {
        Object.values(groups).forEach(items => {
          if (Array.isArray(items)) {
            items.forEach(item => {
              if (item.key in form) {
                if (item.type === 'boolean') {
                  form[item.key] = Boolean(Number(item.value))
                } else if (item.type === 'integer') {
                  form[item.key] = Number(item.value)
                } else {
                  form[item.key] = item.value
                }
              }
            })
          }
        })
      }
    }
  } catch (err) {
    console.error('Failed to load settings:', err)
    apiError.value = err.message || 'Failed to load system configuration.'
  } finally {
    loading.value = false
    originalSettings.value = JSON.stringify(form)
  }
}

function cancelChanges() {
  if (originalSettings.value) {
    try {
      const parsed = JSON.parse(originalSettings.value)
      Object.assign(form, parsed)
      clearErrors()
      success('Reverted', 'Form restored to previously saved settings.')
    } catch (e) {}
  }
}

const saveSettings = async () => {
  if (!validateForm()) {
    error('Validation Failed', 'Please fix the highlighted errors before saving.')
    return
  }

  isSaving.value = true
  try {
    const payload = Object.keys(form).map(key => {
      let val = form[key]
      if (typeof val === 'boolean') val = val ? '1' : '0'
      return {
        key,
        value: String(val),
      }
    })

    await adminService.updateSettings(payload)
    originalSettings.value = JSON.stringify(form)
    success('Settings Saved', 'System configuration has been successfully updated.')
  } catch (err) {
    console.error('Failed to update settings:', err)
    error('Update Failed', err.message || 'Could not save system settings.')
  } finally {
    isSaving.value = false
  }
}

function resetToDefaults() {
  form.site_name = 'BetLink'
  form.site_tagline = "Ethiopia's #1 Property Marketplace"
  form.site_email = 'info@betlink.et'
  form.site_phone = '+251911000000'
  form.currency = 'ETB'
  form.currency_symbol = 'Br'
  form.max_images_per_property = 20
  form.property_approval_required = true
  form.featured_properties_count = 12
  form.property_expiry_days = 90
  form.service_fee_percentage = 5
  form.appointment_duration_default = 30
  form.meta_title = 'BetLink — Find Your Dream Property in Ethiopia'
  form.meta_description = 'Buy, sell, and rent properties across Ethiopia on BetLink — the #1 property marketplace.'
  clearErrors()
  success('Reset to Defaults', 'Form reset to factory default values. Click Save to apply.')
}

onMounted(() => {
  loadSettings()
})
</script>
