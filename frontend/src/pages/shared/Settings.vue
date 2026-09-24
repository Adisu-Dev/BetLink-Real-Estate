<template>
  <div class="space-y-5">
    <!-- Notifications -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-200">
      <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        Notifications
      </h2>

      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Email on new messages</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Get notified when you receive messages</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.emailOnMessage" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Email on appointment confirmations</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Receive reminders for scheduled appointments</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.emailOnAppointment" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Email on property reviews</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Notify me when I receive a new review</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.emailOnReview" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Weekly digest</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Get a summary of platform activity</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.weeklyDigest" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Privacy Settings -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-200">
      <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Privacy & Visibility
      </h2>

      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Show profile on search</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Let others find you through property searches</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.showProfile" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Hide phone number</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Keep your phone number private</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.hidePhone" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Allow direct messages</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Receive messages from other users</p>
          </div>
          <label class="relative flex items-center cursor-pointer">
            <input v-model="settings.allowMessages" type="checkbox" class="hidden peer" />
            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-checked:bg-emerald-600 rounded-full transition-colors"></div>
            <span class="absolute left-1 top-0.5 w-5 h-5 bg-white dark:bg-slate-200 rounded-full transition-all peer-checked:translate-x-5"></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Account Preferences -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-200">
      <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
        </svg>
        Account Preferences
      </h2>

      <div class="space-y-3">
        <div>
          <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Language</label>
          <select v-model="settings.language" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200">
            <option value="en">English</option>
            <option value="am">Amharic</option>
            <option value="or">Oromo</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Timezone</label>
          <select v-model="settings.timezone" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200">
            <option value="UTC+3">EAT (UTC+3) - Ethiopia</option>
            <option value="UTC">UTC</option>
            <option value="UTC+1">UTC+1</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Theme</label>
          <div class="flex gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="settings.theme" type="radio" value="light" class="w-4 h-4" />
              <span class="text-sm text-slate-700 dark:text-slate-300">Light</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="settings.theme" type="radio" value="dark" class="w-4 h-4" />
              <span class="text-sm text-slate-700 dark:text-slate-300">Dark</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="settings.theme" type="radio" value="auto" class="w-4 h-4" />
              <span class="text-sm text-slate-700 dark:text-slate-300">Auto</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-red-200 dark:border-red-900 p-6 transition-colors duration-200">
      <h2 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0 4v2M12 3v1m0 1V5m9.939 9.939l-.707.707m-1.414-1.414l.707.707m-1.414-1.414l-.707-.707m1.414 1.414l.707-.707M3.061 14.061l.707.707m1.414-1.414l-.707.707m1.414 1.414l.707.707m-1.414-1.414l-.707-.707"/>
        </svg>
        Danger Zone
      </h2>

      <div class="space-y-3">
        <div class="p-4 bg-red-50 dark:bg-red-950 rounded-lg flex items-center justify-between transition-colors duration-200">
          <div>
            <p class="font-semibold text-slate-900 dark:text-slate-100">Delete Account</p>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Permanently delete your account and all data. This action cannot be undone.</p>
          </div>
          <button
            @click="showDeleteConfirm = true"
            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm flex-shrink-0"
          >
            Delete 
          </button>
        </div>
      </div>
    </div>

    <!-- Save Settings Button -->
    <div class="flex gap-3">
      <button
        @click="saveSettings"
        class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Save Settings
      </button>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-slate-900 rounded-lg p-6 max-w-sm mx-4 space-y-4 transition-colors duration-200">
        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Delete Account?</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400"/>
          This will permanently delete your account and all associated data.
        <input
          v-model="deleteConfirmText"
          type="text"
          placeholder='Type "DELETE" to confirm'
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200"
        />
        <div class="flex gap-3">
          <button
            @click="confirmDelete"
            :disabled="deleteConfirmText !== 'DELETE'"
            class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Delete 
          </button>
          <button
            @click="showDeleteConfirm = false; deleteConfirmText = ''"
            class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
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

const settings = ref({
  emailOnMessage: true,
  emailOnAppointment: true,
  emailOnReview: true,
  weeklyDigest: false,
  showProfile: true,
  hidePhone: false,
  allowMessages: true,
  language: 'en',
  timezone: 'UTC+3',
  theme: 'light',
})

const showDeleteConfirm = ref(false)
const deleteConfirmText = ref('')

const saveSettings = () => {
  alert('Settings saved successfully!')
}

const confirmDelete = () => {
  if (deleteConfirmText.value === 'DELETE') {
    alert('Account deleted successfully. Redirecting...')
    showDeleteConfirm.value = false
    deleteConfirmText.value = ''
  }
}
</script>
