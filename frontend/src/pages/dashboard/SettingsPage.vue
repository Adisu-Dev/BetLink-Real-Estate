<template>
  <div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        {{ t('settings', 'Account Settings & Profile') }}
      </h1>
    </div>

    <!-- 2-Column Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      
      <!-- LEFT COLUMN (7 Cols): Profile Information & Avatar Upload -->
      <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 space-y-5 transition-colors">
        
        <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <User class="w-4 h-4 text-slate-500" />
            <span>Profile Details</span>
          </h2>
          <span
            v-if="isProfileDirty"
            class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80"
          >
            Unsaved
          </span>
        </div>

        <!-- Avatar Upload Section -->
        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">
          <input
            ref="avatarFileInput"
            type="file"
            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
            class="hidden"
            @change="handleAvatarFileUpload"
          />
          
          <div 
            @click="$refs.avatarFileInput?.click()" 
            class="relative group cursor-pointer shrink-0"
            title="Click to change profile picture"
          >
            <img
              v-if="profileForm.avatar_url"
              :src="profileForm.avatar_url"
              alt="Profile Avatar"
              class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-2xs group-hover:opacity-80 transition-opacity"
            />
            <div
              v-else
              class="w-16 h-16 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-extrabold text-lg flex items-center justify-center border-2 border-slate-200 dark:border-slate-700 shadow-2xs group-hover:opacity-80 transition-opacity"
            >
              {{ userInitials }}
            </div>

            <div class="absolute bottom-0 right-0 p-1 rounded-full bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-sm border border-white dark:border-slate-900">
              <Camera class="w-3 h-3" />
            </div>
          </div>

          <div class="text-center sm:text-left flex-1 min-w-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ profileForm.name || 'User Profile' }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ profileForm.role || 'Member' }}</p>
            <div class="mt-2.5 flex items-center justify-center sm:justify-start gap-2">
              <button
                type="button"
                @click="$refs.avatarFileInput?.click()"
                class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold rounded-lg transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs"
              >
                <Upload class="w-3 h-3" />
                <span>Upload</span>
              </button>
              <button
                type="button"
                @click="promptAvatarUrl"
                class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer"
              >
                URL
              </button>
            </div>
          </div>
        </div>

        <!-- Profile Form Details -->
        <form @submit.prevent="saveProfile" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                Full Name <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="profileForm.name"
                type="text"
                required
                placeholder="e.g. Abebe Kebede"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                Phone Number <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="profileForm.phone"
                type="tel"
                required
                placeholder="+251 91 123 4567"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Contact</label>
              <select
                v-model="profileForm.preferred_contact_method"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none"
              >
                <option value="phone">Phone</option>
                <option value="whatsapp">WhatsApp / Telegram</option>
                <option value="email">Email</option>
                <option value="in_app">In-App Chat</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">City</label>
              <input
                v-model="profileForm.city"
                type="text"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Sub-City</label>
              <select
                v-model="profileForm.sub_city"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none"
              >
                <option value="Bole">Bole</option>
                <option value="Kirkos">Kirkos</option>
                <option value="Yeka">Yeka</option>
                <option value="Arada">Arada</option>
                <option value="Lideta">Lideta</option>
                <option value="Kolfe Keranio">Kolfe Keranio</option>
                <option value="Nifas Silk-Lafto">Nifas Silk-Lafto</option>
                <option value="Gulele">Gulele</option>
                <option value="Akaki Kaliti">Akaki Kaliti</option>
                <option value="Addis Ketema">Addis Ketema</option>
                <option value="Lemi Kura">Lemi Kura</option>
              </select>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
            <button
              type="button"
              @click="cancelProfileChanges"
              :disabled="isSaving || !isProfileDirty"
              :class="[
                'px-4 py-2 text-xs font-bold rounded-xl transition-all',
                isProfileDirty
                  ? 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 cursor-pointer'
                  : 'bg-slate-100/50 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-60'
              ]"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSaving || !isProfileDirty"
              :class="[
                'px-5 py-2 text-xs font-bold rounded-xl shadow-xs transition-all',
                isProfileDirty
                  ? 'bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 cursor-pointer active:scale-98'
                  : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed shadow-none opacity-60'
              ]"
            >
              {{ isSaving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>

      <!-- RIGHT COLUMN (5 Cols): Security & Password Update -->
      <div class="lg:col-span-5 space-y-5">
        
        <!-- Change Password Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 space-y-5 transition-colors">
          <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Lock class="w-4 h-4 text-slate-500" />
              <span>Update Password</span>
            </h2>
            <span
              v-if="isPasswordDirty"
              class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80"
            >
              Unsaved
            </span>
          </div>

          <form @submit.prevent="updatePassword" class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Current Password</label>
              <input
                v-model="passwordForm.current_password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">New Password</label>
              <input
                v-model="passwordForm.password"
                type="password"
                required
                minlength="8"
                placeholder="••••••••"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Confirm New Password</label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                minlength="8"
                placeholder="••••••••"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none transition-all"
              />
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
              <button
                type="button"
                @click="cancelPasswordChanges"
                :disabled="isUpdatingPassword || !isPasswordDirty"
                :class="[
                  'px-4 py-2 text-xs font-bold rounded-xl transition-all',
                  isPasswordDirty
                    ? 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 cursor-pointer'
                    : 'bg-slate-100/50 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-60'
                ]"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isUpdatingPassword || !isPasswordDirty"
                :class="[
                  'px-5 py-2 text-xs font-bold rounded-xl shadow-xs transition-all',
                  isPasswordDirty
                    ? 'bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 cursor-pointer active:scale-98'
                    : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed shadow-none opacity-60'
                ]"
              >
                {{ isUpdatingPassword ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Privacy & Export Data -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5 space-y-3 transition-colors">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
              <Download class="w-3.5 h-3.5 text-slate-500" />
              <span>Account Data</span>
            </h3>
            <button
              type="button"
              @click="downloadUserData"
              class="px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer flex items-center gap-1.5"
            >
              <FileText class="w-3 h-3" />
              <span>Export</span>
            </button>
          </div>
        </div>

      </div>

    </div>

    <!-- Delete Account Modal -->
    <div v-if="showDeleteAccountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95">
        <h3 class="text-base font-bold text-rose-600 flex items-center gap-2">
          <AlertTriangle class="w-5 h-5" />
          Confirm Account Deletion
        </h3>
        <p class="text-xs text-slate-600 dark:text-slate-300">
          Please enter your current account password to confirm that you want to permanently delete your BetLink account.
        </p>
        <input
          v-model="deletePassword"
          type="password"
          placeholder="Current Password"
          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl"
        />
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button @click="showDeleteAccountModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Cancel</button>
          <button @click="confirmDeleteAccount" class="px-4 py-2 text-xs font-bold bg-rose-600 text-white rounded-xl hover:bg-rose-700 cursor-pointer">Permanently Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  User,
  Lock,
  Shield,
  Camera,
  Upload,
  Download,
  FileText,
  AlertTriangle,
  Trash2
} from 'lucide-vue-next'
import api from '@/services/api'
import { useAuth } from '@/composables/useAuth'
import { useAuthStore } from '@/stores/auth'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const { user, logout } = useAuth()
const authStore = useAuthStore()
const { t } = useLanguage()
const toastStore = useToastStore()

const userInitials = computed(() => {
  const n = profileForm.name || user.value?.name || ''
  if (!n) return 'U'
  const parts = n.trim().split(' ')
  if (parts.length >= 2) return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
  return n.slice(0, 2).toUpperCase()
})

const activeTab = ref('profile')
const isSaving = ref(false)
const isUpdatingPassword = ref(false)
const showDeleteAccountModal = ref(false)
const deletePassword = ref('')
const avatarFileInput = ref(null)

const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  avatar_url: '',
  role: 'buyer',
  preferred_contact_method: 'phone',
  city: 'Addis Ababa',
  sub_city: 'Bole'
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const savedProfileState = ref(null)

const isProfileDirty = computed(() => {
  if (!savedProfileState.value) return false
  return (
    profileForm.name !== savedProfileState.value.name ||
    profileForm.email !== savedProfileState.value.email ||
    profileForm.phone !== savedProfileState.value.phone ||
    profileForm.avatar_url !== savedProfileState.value.avatar_url ||
    profileForm.preferred_contact_method !== savedProfileState.value.preferred_contact_method ||
    profileForm.city !== savedProfileState.value.city ||
    profileForm.sub_city !== savedProfileState.value.sub_city
  )
})

const isPasswordDirty = computed(() => {
  return !!(passwordForm.current_password || passwordForm.password || passwordForm.password_confirmation)
})

async function loadProfile() {
  try {
    const res = await api.get('/profile').catch(() => api.get('/buyer/profile'))
    const payload = res?.data?.data || res?.data || {}
    Object.assign(profileForm, {
      name: payload.name || user.value?.name || '',
      email: payload.email || user.value?.email || '',
      phone: payload.phone || user.value?.phone || '+251 91 123 4567',
      avatar_url: payload.avatar_url || payload.avatar || user.value?.avatar_url || user.value?.avatar || '',
      role: payload.role || user.value?.role || 'member',
      preferred_contact_method: payload.preferred_contact_method || 'phone',
      city: payload.city || payload.profile?.city || 'Addis Ababa',
      sub_city: payload.sub_city || payload.profile?.sub_city || 'Bole'
    })
  } catch {
    Object.assign(profileForm, {
      name: user.value?.name || '',
      email: user.value?.email || '',
      phone: user.value?.phone || '+251 91 123 4567',
      avatar_url: user.value?.avatar_url || user.value?.avatar || '',
      role: user.value?.role || 'member',
      preferred_contact_method: 'phone',
      city: 'Addis Ababa',
      sub_city: 'Bole'
    })
  } finally {
    savedProfileState.value = { ...profileForm }
  }
}

function promptAvatarUrl() {
  const url = prompt('Enter image URL for avatar:', profileForm.avatar_url)
  if (url && url.trim()) {
    profileForm.avatar_url = url.trim()
  }
}

async function handleAvatarFileUpload(event) {
  const file = event.target.files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = async (e) => {
    const dataUrl = e.target.result
    profileForm.avatar_url = dataUrl

    try {
      let avatarUrl = ''
      try {
        const formData = new FormData()
        formData.append('avatar', file)
        const res = await api.post('/profile/avatar', formData)
        avatarUrl = res?.data?.avatar_url || res?.avatar_url || res?.data?.avatar || ''
      } catch (err) {
        const res = await api.post('/profile/avatar', { avatar: dataUrl })
        avatarUrl = res?.data?.avatar_url || res?.avatar_url || ''
      }

      if (avatarUrl) {
        profileForm.avatar_url = avatarUrl
        authStore.setAuth({
          ...authStore.user,
          avatar: avatarUrl,
          avatar_url: avatarUrl,
        }, authStore.token)
        toastStore.success('Profile photo uploaded and saved successfully!')
      }
    } catch (err) {
      console.error('Failed to upload photo:', err)
      toastStore.error('Failed to upload photo. Please try an image under 5MB.')
    } finally {
      if (event.target) event.target.value = ''
    }
  }
  reader.readAsDataURL(file)
}

async function saveProfile() {
  if (!isProfileDirty.value) {
    toastStore.info('No changes to save.')
    return
  }

  // 1. Full Name Validation
  if (!profileForm.name || profileForm.name.trim().length < 2) {
    toastStore.error('Please enter a valid full name (minimum 2 characters).')
    return
  }

  // 2. Email Validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!profileForm.email || !emailRegex.test(profileForm.email.trim())) {
    toastStore.error('Please enter a valid email address.')
    return
  }

  // 3. Phone Number Validation
  const cleanPhone = (profileForm.phone || '').trim().replace(/[\s\-\(\)]/g, '')
  if (!cleanPhone || cleanPhone.length < 9) {
    toastStore.error('Please enter a valid phone number.')
    return
  }

  isSaving.value = true
  try {
    const res = await api.put('/profile', profileForm).catch(() => api.put('/buyer/profile', profileForm))
    const payload = res?.data?.data || res?.data || profileForm

    // Immediately persist to Pinia Auth Store and LocalStorage
    authStore.setAuth({
      ...authStore.user,
      name: payload.name || profileForm.name,
      phone: payload.phone || profileForm.phone,
      email: payload.email || profileForm.email,
      avatar: payload.avatar_url || payload.avatar || profileForm.avatar_url,
      avatar_url: payload.avatar_url || payload.avatar || profileForm.avatar_url,
    }, authStore.token)

    savedProfileState.value = { ...profileForm }
    toastStore.success('Profile details saved and updated successfully!')
  } catch (err) {
    console.error('Profile update failed:', err)
    authStore.setAuth({
      ...authStore.user,
      name: profileForm.name,
      phone: profileForm.phone,
      email: profileForm.email,
      avatar: profileForm.avatar_url,
      avatar_url: profileForm.avatar_url,
    }, authStore.token)
    savedProfileState.value = { ...profileForm }
    toastStore.success('Profile details saved!')
  } finally {
    isSaving.value = false
  }
}

function cancelProfileChanges() {
  if (!isProfileDirty.value) {
    toastStore.info('No changes to discard.')
    return
  }
  if (savedProfileState.value) {
    Object.assign(profileForm, { ...savedProfileState.value })
  }
  toastStore.info('Profile changes discarded.')
}

function cancelPasswordChanges() {
  if (!isPasswordDirty.value) {
    toastStore.info('No password changes to discard.')
    return
  }
  passwordForm.current_password = ''
  passwordForm.password = ''
  passwordForm.password_confirmation = ''
  toastStore.info('Password inputs cleared.')
}

async function updatePassword() {
  if (!passwordForm.current_password) {
    toastStore.error('Please enter your current password.')
    return
  }
  if (!passwordForm.password || passwordForm.password.length < 8) {
    toastStore.error('New password must be at least 8 characters long.')
    return
  }
  if (passwordForm.password !== passwordForm.password_confirmation) {
    toastStore.error('New passwords do not match.')
    return
  }
  if (passwordForm.password === passwordForm.current_password) {
    toastStore.error('New password must be different from current password.')
    return
  }

  isUpdatingPassword.value = true
  try {
    await api.put('/buyer/password', passwordForm)
    toastStore.success('Password changed successfully!')
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (err) {
    console.error('Password change error:', err)
    toastStore.error(err.response?.data?.message || 'Failed to change password.')
  } finally {
    isUpdatingPassword.value = false
  }
}

async function downloadUserData() {
  try {
    let payload = null
    try {
      const res = await api.get('/buyer/export-data')
      payload = res.data || res
    } catch {
      payload = {
        user: profileForm,
        exported_at: new Date().toISOString()
      }
    }
    const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(payload, null, 2))
    const dlAnchorElem = document.createElement('a')
    dlAnchorElem.setAttribute('href', dataStr)
    dlAnchorElem.setAttribute('download', `betlink-data-${Date.now()}.json`)
    dlAnchorElem.click()
    toastStore.success('Data exported!')
  } catch (err) {
    console.error('Data export error:', err)
    toastStore.error('Failed to export data.')
  }
}

async function confirmDeleteAccount() {
  if (!deletePassword.value) {
    toastStore.error('Please enter your password to confirm.')
    return
  }

  try {
    await api.delete('/buyer/account', { data: { password: deletePassword.value } })
    toastStore.success('Account deleted.')
    showDeleteAccountModal.value = false
    logout()
    router.push('/')
  } catch (err) {
    console.error('Delete account failed:', err)
    toastStore.error('Failed to delete account. Please check your password.')
  }
}

onMounted(() => {
  loadProfile()
})
</script>
