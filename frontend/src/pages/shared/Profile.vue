<template>
  <div class="space-y-5">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Avatar Section -->
        <div class="flex flex-col items-center md:items-start gap-4">
          <div class="relative">
            <img
              :src="profile.avatar"
              :alt="profile.name"
              class="w-24 h-24 rounded-full object-cover border-4 border-emerald-100"
            />
            <label class="absolute bottom-0 right-0 bg-emerald-600 text-white p-2 rounded-full cursor-pointer hover:bg-emerald-700 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <input type="file" class="hidden" @change="handleAvatarUpload" accept="image/*" />
            </label>
          </div>
          <div class="text-center md:text-left">
            <h1 class="text-2xl font-bold text-gray-900">{{ profile.name }}</h1>
            <p class="text-sm text-gray-500">{{ roleLabel }} • Joined {{ profile.joinedDate }}</p>
            <span class="inline-block mt-2 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">{{ profile.role }}</span>
          </div>
        </div>

        <!-- Quick Info -->
        <div class="flex-1 grid grid-cols-2 gap-4">
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs text-gray-500 font-semibold mb-1">Email</p>
            <p class="text-sm font-medium text-gray-900">{{ profile.email }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs text-gray-500 font-semibold mb-1">Phone</p>
            <p class="text-sm font-medium text-gray-900">{{ profile.phone || 'Not set' }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs text-gray-500 font-semibold mb-1">Account Status</p>
            <p class="text-sm font-medium text-emerald-600">Verified</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs text-gray-500 font-semibold mb-1">Last Login</p>
            <p class="text-sm font-medium text-gray-900">{{ profile.lastLogin }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Profile
      </h2>

      <form @submit.prevent="saveProfile" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- First Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name *</label>
            <input
              v-model="formData.firstName"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter first name"
            />
          </div>

          <!-- Last Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name *</label>
            <input
              v-model="formData.lastName"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter last name"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
            <input
              v-model="formData.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter email"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
            <input
              v-model="formData.phone"
              type="tel"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter phone number"
            />
          </div>

          <!-- City (role-specific) -->
          <div v-if="profile.role !== 'admin'">
            <label class="block text-sm font-semibold text-gray-700 mb-1">City</label>
            <input
              v-model="formData.city"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Enter city"
            />
          </div>

          <!-- Bio -->
          <div v-if="profile.role !== 'admin'">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bio</label>
            <input
              v-model="formData.bio"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
              placeholder="Tell us about yourself"
            />
          </div>
        </div>

        <!-- Bio full-width -->
        <div v-if="profile.role === 'admin'">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Bio</label>
          <textarea
            v-model="formData.bio"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="Tell us about yourself"
          ></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4">
          <button
            type="submit"
            class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Save Changes
          </button>
          <button
            type="button"
            @click="resetForm"
            class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Change Password
      </h2>

      <form @submit.prevent="changePassword" class="space-y-4 max-w-md">
        <!-- Current Password -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Current Password *</label>
          <input
            v-model="passwordData.current"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="Enter current password"
          />
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">New Password *</label>
          <input
            v-model="passwordData.new"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="Enter new password"
          />
          <p class="text-xs text-gray-500 mt-1">At least 8 characters with uppercase, lowercase, and numbers</p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
          <input
            v-model="passwordData.confirm"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            placeholder="Confirm new password"
          />
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4">
          <button
            type="submit"
            class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors"
          >
            Update Password
          </button>
          <button
            type="button"
            @click="resetPasswordForm"
            class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors"
          >
            Clear
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const userStr = localStorage.getItem('betlink_user')
const user = userStr ? JSON.parse(userStr) : { role: 'buyer' }

const roleLabel = computed(() => {
  const labels = { admin: 'Administrator', owner: 'Property Owner', buyer: 'Buyer', agent: 'Real Estate Agent' }
  return labels[user.role] || 'User'
})

// Profile data
const profile = ref({
  name: 'Abebe Kebede',
  email: 'abebe@example.com',
  phone: '+251-911-234-567',
  avatar: 'https://ui-avatars.com/api/?name=Abebe+Kebede&background=1e293b&color=fff&size=128',
  role: user.role,
  joinedDate: 'Jan 2024',
  lastLogin: '2 hours ago',
})

const formData = ref({
  firstName: 'Abebe',
  lastName: 'Kebede',
  email: 'abebe@example.com',
  phone: '+251-911-234-567',
  city: 'Addis Ababa',
  bio: 'Lorem ipsum dolor sit amet.',
})

const passwordData = ref({
  current: '',
  new: '',
  confirm: '',
})

// Methods
const handleAvatarUpload = (e) => {
  const file = e.target.files?.[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (evt) => {
      profile.value.avatar = evt.target?.result
    }
    reader.readAsDataURL(file)
  }
}

const saveProfile = () => {
  profile.value.name = `${formData.value.firstName} ${formData.value.lastName}`
  profile.value.email = formData.value.email
  profile.value.phone = formData.value.phone
  // Toast notification
  alert('Profile updated successfully!')
}

const resetForm = () => {
  formData.value = {
    firstName: profile.value.name.split(' ')[0],
    lastName: profile.value.name.split(' ')[1],
    email: profile.value.email,
    phone: profile.value.phone,
    city: 'Addis Ababa',
    bio: 'Lorem ipsum dolor sit amet.',
  }
}

const changePassword = () => {
  if (passwordData.value.new !== passwordData.value.confirm) {
    alert('Passwords do not match!')
    return
  }
  if (passwordData.value.new.length < 8) {
    alert('Password must be at least 8 characters!')
    return
  }
  alert('Password changed successfully!')
  resetPasswordForm()
}

const resetPasswordForm = () => {
  passwordData.value = { current: '', new: '', confirm: '' }
}
</script>
