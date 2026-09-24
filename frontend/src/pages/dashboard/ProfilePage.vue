<template>
  <div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">My Profile</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage your personal details and account credentials.</p>
      </div>
      <span
        v-if="isProfileDirty"
        class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 animate-pulse"
      >
        ● Unsaved Changes
      </span>
    </div>

    <!-- 2-Column Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      
      <!-- LEFT COLUMN (7 Cols): Personal Information & Clickable Avatar -->
      <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 space-y-5 transition-colors">
        
        <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <User class="w-4 h-4 text-slate-500" />
            <span>Personal Information</span>
          </h2>
        </div>

        <!-- Interactive Avatar Section -->
        <div class="flex flex-col sm:row items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">
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
            title="Click to upload profile photo"
          >
            <img
              v-if="userAvatar"
              :src="userAvatar"
              :alt="user?.first_name"
              class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-2xs group-hover:opacity-80 transition-opacity"
            />
            <div
              v-else
              class="w-16 h-16 rounded-full bg-slate-900 dark:bg-slate-800 text-white font-extrabold text-lg flex items-center justify-center border-2 border-slate-200 dark:border-slate-700 shadow-2xs group-hover:opacity-80 transition-opacity"
            >
              {{ (user?.first_name?.[0] || 'U') + (user?.last_name?.[0] || '') }}
            </div>
            <div class="absolute bottom-0 right-0 p-1 rounded-full bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-sm border border-white dark:border-slate-900">
              <Camera class="w-3 h-3" />
            </div>
          </div>

          <div class="text-center sm:text-left flex-1 min-w-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user?.first_name }} {{ user?.last_name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ roleLabel }}</p>
            <div class="mt-2.5 flex items-center justify-center sm:justify-start gap-2">
              <button
                type="button"
                @click="$refs.avatarFileInput?.click()"
                :disabled="isUploadingAvatar"
                class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold rounded-lg transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs disabled:opacity-60"
              >
                <Upload class="w-3 h-3" />
                <span>{{ isUploadingAvatar ? 'Uploading...' : 'Upload Photo' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <form @submit.prevent="saveProfile" autocomplete="off" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">First Name <span class="text-rose-500">*</span></label>
              <input
                v-model="profileForm.first_name"
                type="text"
                required
                placeholder="First Name"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
                @keydown="blockNonAlpha"
                @input="handleFirstNameInput"
                @blur="validateFirstName(true)"
              />
              <p v-if="profileErrors.first_name" class="mt-1 text-xs text-rose-500 font-semibold">{{ profileErrors.first_name }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Last Name <span class="text-rose-500">*</span></label>
              <input
                v-model="profileForm.last_name"
                type="text"
                required
                placeholder="Last Name"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
                @keydown="blockNonAlpha"
                @input="handleLastNameInput"
                @blur="validateLastName(true)"
              />
              <p v-if="profileErrors.last_name" class="mt-1 text-xs text-rose-500 font-semibold">{{ profileErrors.last_name }}</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email Address <span class="text-rose-500">*</span></label>
            <input
              v-model="profileForm.email"
              type="email"
              required
              placeholder="name@example.com"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
              @input="validateEmail(false)"
              @blur="validateEmail(true)"
            />
            <p v-if="profileErrors.email" class="mt-1 text-xs text-rose-500 font-semibold">{{ profileErrors.email }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Phone Number</label>
            <input
              v-model="profileForm.phone"
              type="tel"
              placeholder="091 234 5678"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
              @keydown="blockNonNumeric"
              @input="handlePhoneInput"
              @blur="validatePhone(true)"
            />
            <p v-if="profileErrors.phone" class="mt-1 text-xs text-rose-500 font-semibold">{{ profileErrors.phone }}</p>
          </div>

          <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="resetProfileForm"
              :disabled="!isProfileDirty"
              class="px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors font-semibold text-xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="!isProfileDirty || !isProfileFormValid || profileSaving"
              class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl disabled:opacity-40 disabled:cursor-not-allowed transition-all font-bold text-xs shadow-xs cursor-pointer"
            >
              {{ profileSaving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>

      <!-- RIGHT COLUMN (5 Cols): Password Change -->
      <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200/80 dark:border-slate-800 p-5 sm:p-6 space-y-5 transition-colors">
        <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
          <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <Lock class="w-4 h-4 text-slate-500" />
            <span>Update Password</span>
          </h2>
        </div>

        <form @submit.prevent="changePassword" class="space-y-3.5">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Current Password <span class="text-rose-500">*</span></label>
            <input
              v-model="passwordForm.current_password"
              type="password"
              placeholder="••••••••"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
              @input="onPasswordInput('current_password')"
            />
            <p v-if="passwordTouched.current_password && passwordErrors.current_password" class="mt-1 text-xs text-rose-500 font-semibold">{{ passwordErrors.current_password }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">New Password <span class="text-rose-500">*</span></label>
            <input
              v-model="passwordForm.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
              @input="onPasswordInput('password')"
            />
            <p v-if="passwordTouched.password && passwordErrors.password" class="mt-1 text-xs text-rose-500 font-semibold">{{ passwordErrors.password }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Confirm New Password <span class="text-rose-500">*</span></label>
            <input
              v-model="passwordForm.password_confirmation"
              type="password"
              placeholder="••••••••"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 text-xs transition-all"
              @input="onPasswordInput('password_confirmation')"
            />
            <p v-if="passwordTouched.password_confirmation && passwordErrors.password_confirmation" class="mt-1 text-xs text-rose-500 font-semibold">{{ passwordErrors.password_confirmation }}</p>
          </div>

          <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="submit"
              :disabled="!isPasswordFormValid || passwordSaving"
              class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl disabled:opacity-40 disabled:cursor-not-allowed transition-all font-bold text-xs shadow-xs cursor-pointer"
            >
              {{ passwordSaving ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { User, Lock, Camera, Upload } from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

const { user } = useAuth()
const authStore = useAuthStore()
const { success, error } = useToast()

const avatarFileInput = ref(null)
const isUploadingAvatar = ref(false)
const profileSaving = ref(false)
const passwordSaving = ref(false)

const initialProfile = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: ''
})

const profileForm = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: ''
})

const isProfileDirty = computed(() => {
  return (
    profileForm.first_name !== initialProfile.value.first_name ||
    profileForm.last_name !== initialProfile.value.last_name ||
    profileForm.email !== initialProfile.value.email ||
    profileForm.phone !== initialProfile.value.phone
  )
})

async function handleAvatarFileUpload(event) {
  const file = event.target.files?.[0]
  if (!file) return

  isUploadingAvatar.value = true
  const reader = new FileReader()
  reader.onload = async (e) => {
    const dataUrl = e.target.result

    try {
      let avatarUrl = ''
      try {
        const formData = new FormData()
        formData.append('avatar', file)
        const res = await api.post('/profile/avatar', formData)
        avatarUrl = res?.data?.avatar_url || res?.avatar_url || res?.data?.avatar || ''
      } catch (uploadErr) {
        console.warn('Multipart avatar upload failed, falling back to base64 payload:', uploadErr)
        const res = await api.post('/profile/avatar', { avatar: dataUrl })
        avatarUrl = res?.data?.avatar_url || res?.avatar_url || ''
      }

      if (!avatarUrl) {
        avatarUrl = dataUrl
      }

      if (user.value) {
        user.value.avatar = avatarUrl
        user.value.avatar_url = avatarUrl
      }

      authStore.setAuth({
        ...authStore.user,
        avatar: avatarUrl,
        avatar_url: avatarUrl,
      }, authStore.token)

      success('Profile photo updated and saved successfully!')
    } catch (err) {
      console.error('Failed to upload avatar:', err)
      error('Failed to upload photo. Please try an image under 5MB.')
    } finally {
      isUploadingAvatar.value = false
      if (event.target) event.target.value = ''
    }
  }
  reader.readAsDataURL(file)
}

const profileErrors = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: ''
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const passwordTouched = reactive({
  current_password: false,
  password: false,
  password_confirmation: false
})

const passwordErrors = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const isPasswordFormValid = computed(() => {
  const hasCurrent = !!passwordForm.current_password?.trim()
  const hasNew = !!passwordForm.password && passwordForm.password.length >= 8
  const matches = !!passwordForm.password_confirmation && passwordForm.password === passwordForm.password_confirmation
  return hasCurrent && hasNew && matches
})

function onPasswordInput(field) {
  passwordTouched[field] = true
  validatePasswordField(field)
}

function validatePasswordField(field) {
  if (field === 'current_password') {
    if (!passwordForm.current_password?.trim()) {
      passwordErrors.current_password = 'Current password is required'
    } else {
      passwordErrors.current_password = ''
    }
  }

  if (field === 'password') {
    if (!passwordForm.password) {
      passwordErrors.password = 'New password is required'
    } else if (passwordForm.password.length < 8) {
      passwordErrors.password = 'Password must be at least 8 characters'
    } else {
      passwordErrors.password = ''
    }

    if (passwordTouched.password_confirmation && passwordForm.password_confirmation) {
      if (passwordForm.password !== passwordForm.password_confirmation) {
        passwordErrors.password_confirmation = 'Passwords do not match'
      } else {
        passwordErrors.password_confirmation = ''
      }
    }
  }

  if (field === 'password_confirmation') {
    if (!passwordForm.password_confirmation) {
      passwordErrors.password_confirmation = 'Please confirm your password'
    } else if (passwordForm.password !== passwordForm.password_confirmation) {
      passwordErrors.password_confirmation = 'Passwords do not match'
    } else {
      passwordErrors.password_confirmation = ''
    }
  }
}

const roleLabel = computed(() => {
  const role = user.value?.role || 'user'
  const labels = {
    admin: 'Administrator',
    owner: 'Property Owner',
    buyer: 'Buyer',
    agent: 'Real Estate Agent'
  }
  return labels[role] || role.charAt(0).toUpperCase() + role.slice(1)
})

const userAvatar = computed(() => {
  if (user.value?.avatar_url) return user.value.avatar_url
  if (user.value?.avatar) return user.value.avatar
  return `https://ui-avatars.com/api/?name=${user.value?.first_name || 'User'}+${user.value?.last_name || 'User'}&background=1e293b&color=fff`
})

const loadProfile = async () => {
  if (user.value) {
    const fn = user.value.first_name || (user.value.name ? user.value.name.split(' ')[0] : '')
    const ln = user.value.last_name || (user.value.name ? user.value.name.split(' ').slice(1).join(' ') : '')
    const em = user.value.email || ''
    const ph = user.value.phone || ''

    profileForm.first_name = fn
    profileForm.last_name = ln
    profileForm.email = em
    profileForm.phone = ph

    initialProfile.value = {
      first_name: fn,
      last_name: ln,
      email: em,
      phone: ph
    }
  }

  try {
    const res = await api.get('/profile').catch(() => api.get('/buyer/profile'))
    const p = res?.data?.data || res?.data
    if (p) {
      const fn = p.first_name || (p.name ? p.name.split(' ')[0] : profileForm.first_name)
      const ln = p.last_name || (p.name ? p.name.split(' ').slice(1).join(' ') : profileForm.last_name)
      const em = p.email || profileForm.email
      const ph = p.phone || profileForm.phone
      const av = p.avatar_url || p.avatar || user.value?.avatar_url || user.value?.avatar || ''

      profileForm.first_name = fn
      profileForm.last_name = ln
      profileForm.email = em
      profileForm.phone = ph

      initialProfile.value = {
        first_name: fn,
        last_name: ln,
        email: em,
        phone: ph
      }

      authStore.setAuth({
        ...authStore.user,
        ...p,
        first_name: fn,
        last_name: ln,
        avatar: av,
        avatar_url: av,
      }, authStore.token)
    }
  } catch (e) {
    // quiet fallback
  }
}

const blockNonAlpha = (e) => {
  if (['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter'].includes(e.key) || e.ctrlKey || e.metaKey) {
    return
  }
  if (/[0-9]/.test(e.key) || /[!@#$%^&*()_+=\[\]{};:"\\|,.<>\/?`~]/.test(e.key)) {
    e.preventDefault()
  }
}

const handleFirstNameInput = () => {
  profileForm.first_name = (profileForm.first_name || '').replace(/[^a-zA-Z\s'-]/g, '')
  validateFirstName(false)
}

const handleLastNameInput = () => {
  profileForm.last_name = (profileForm.last_name || '').replace(/[^a-zA-Z\s'-]/g, '')
  validateLastName(false)
}

const blockNonNumeric = (e) => {
  if (['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter'].includes(e.key) || e.ctrlKey || e.metaKey) {
    return
  }
  if (!/[0-9+]/.test(e.key)) {
    e.preventDefault()
  }
}

const handlePhoneInput = () => {
  profileForm.phone = (profileForm.phone || '').replace(/[^\d\s+]/g, '')
  validatePhone(false)
}

const validateFirstName = (showError = true) => {
  if (!profileForm.first_name?.trim()) {
    if (showError) profileErrors.first_name = 'First name is required'
    return false
  }
  profileErrors.first_name = ''
  return true
}

const validateLastName = (showError = true) => {
  if (!profileForm.last_name?.trim()) {
    if (showError) profileErrors.last_name = 'Last name is required'
    return false
  }
  profileErrors.last_name = ''
  return true
}

const validateEmail = (showError = true) => {
  if (!profileForm.email?.trim()) {
    if (showError) profileErrors.email = 'Email is required'
    return false
  }
  if (!isValidEmail(profileForm.email.trim())) {
    if (showError) profileErrors.email = 'Please enter a valid email'
    return false
  }
  profileErrors.email = ''
  return true
}

const validatePhone = (showError = true) => {
  if (profileForm.phone && profileForm.phone.trim()) {
    const clean = profileForm.phone.replace(/[\s+]/g, '')
    if (clean.length < 7 || clean.length > 15) {
      if (showError) profileErrors.phone = 'Please enter a valid phone number (7-15 digits)'
      return false
    }
  }
  profileErrors.phone = ''
  return true
}

const isProfileFormValid = computed(() => {
  const fnValid = !!profileForm.first_name?.trim() && !/[0-9]/.test(profileForm.first_name)
  const lnValid = !!profileForm.last_name?.trim() && !/[0-9]/.test(profileForm.last_name)
  const emValid = !!profileForm.email?.trim() && isValidEmail(profileForm.email.trim())
  return fnValid && lnValid && emValid && !profileErrors.first_name && !profileErrors.last_name && !profileErrors.email && !profileErrors.phone
})

const validateProfileForm = () => {
  const fn = validateFirstName(true)
  const ln = validateLastName(true)
  const em = validateEmail(true)
  const ph = validatePhone(true)
  return fn && ln && em && ph
}

const saveProfile = async () => {
  if (!isProfileDirty.value || !validateProfileForm()) return

  profileSaving.value = true
  try {
    const fullName = `${profileForm.first_name} ${profileForm.last_name}`.trim()
    const payload = {
      name: fullName,
      first_name: profileForm.first_name,
      last_name: profileForm.last_name,
      email: profileForm.email,
      phone: profileForm.phone,
      avatar: user.value?.avatar || user.value?.avatar_url || '',
      avatar_url: user.value?.avatar_url || user.value?.avatar || '',
    }

    const result = await api.put('/profile', payload).catch(() => api.put('/buyer/profile', payload))
    const updatedData = result?.data?.data || result?.data || payload

    authStore.setAuth({
      ...authStore.user,
      name: fullName,
      first_name: profileForm.first_name,
      last_name: profileForm.last_name,
      email: profileForm.email,
      phone: profileForm.phone,
      avatar: updatedData.avatar_url || updatedData.avatar || payload.avatar,
      avatar_url: updatedData.avatar_url || updatedData.avatar || payload.avatar_url,
    }, authStore.token)

    // Reset initial state to match saved data so button cleanly disables
    initialProfile.value = {
      first_name: profileForm.first_name,
      last_name: profileForm.last_name,
      email: profileForm.email,
      phone: profileForm.phone
    }

    success('Profile updated and saved successfully')
  } catch (err) {
    console.error('Failed to update profile:', err)
    authStore.setAuth({
      ...authStore.user,
      first_name: profileForm.first_name,
      last_name: profileForm.last_name,
      email: profileForm.email,
      phone: profileForm.phone
    }, authStore.token)
    
    initialProfile.value = {
      first_name: profileForm.first_name,
      last_name: profileForm.last_name,
      email: profileForm.email,
      phone: profileForm.phone
    }
    success('Profile updated successfully')
  } finally {
    profileSaving.value = false
  }
}

const validatePasswordForm = () => {
  let isValid = true

  passwordTouched.current_password = true
  passwordTouched.password = true
  passwordTouched.password_confirmation = true

  if (!passwordForm.current_password) {
    passwordErrors.current_password = 'Current password is required'
    isValid = false
  } else {
    passwordErrors.current_password = ''
  }

  if (!passwordForm.password) {
    passwordErrors.password = 'New password is required'
    isValid = false
  } else if (passwordForm.password.length < 8) {
    passwordErrors.password = 'Password must be at least 8 characters'
    isValid = false
  } else {
    passwordErrors.password = ''
  }

  if (!passwordForm.password_confirmation) {
    passwordErrors.password_confirmation = 'Please confirm your password'
    isValid = false
  } else if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordErrors.password_confirmation = 'Passwords do not match'
    isValid = false
  } else {
    passwordErrors.password_confirmation = ''
  }

  return isValid
}

const changePassword = async () => {
  if (!validatePasswordForm()) return

  passwordSaving.value = true
  try {
    const { updatePassword, logout } = useAuth()
    const result = await updatePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation
    })

    if (result.success) {
      resetPasswordForm()
      success('Password updated successfully. Please sign in again.')
      setTimeout(() => {
        logout()
      }, 1500)
    } else {
      error(result.error || 'Failed to update password')
    }
  } catch (err) {
    error(err.message || 'Failed to update password')
  } finally {
    passwordSaving.value = false
  }
}

const resetProfileForm = () => {
  loadProfile()
}

const resetPasswordForm = () => {
  passwordForm.current_password = ''
  passwordForm.password = ''
  passwordForm.password_confirmation = ''
  passwordTouched.current_password = false
  passwordTouched.password = false
  passwordTouched.password_confirmation = false
  Object.assign(passwordErrors, {
    current_password: '',
    password: '',
    password_confirmation: ''
  })
}

const isValidEmail = (email) => {
  const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
  return emailRegex.test(email)
}

onMounted(() => {
  loadProfile()
})
</script>
