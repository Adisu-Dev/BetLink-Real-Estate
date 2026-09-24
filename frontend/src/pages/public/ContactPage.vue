<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 pt-2 pb-12 sm:pt-4 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
      <!-- Back Navigation Bar -->
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="handleGoBack"
          class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors shrink-0 cursor-pointer shadow-2xs"
          title="Go Back"
          aria-label="Go Back"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Back</span>
      </div>

      <!-- Header -->
      <div class="text-center max-w-2xl mx-auto space-y-1.5">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Get in Touch</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Contact the BetLink Team</h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base">Have questions about listings, account verification, or partnerships? We're here to help.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact  Cards -->
        <div class="space-y-4">
          <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-2 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold">
              📍
            </div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Head Office</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">Bole Sub-City, Africa Avenue, Addis Ababa, Ethiopia</p>
          </div>

          <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-2 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold">
              📞
            </div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Phone Support</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">+251 911 234 567 / +251 116 123 456</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500">Mon - Sat: 8:30 AM - 6:00 PM</p>
          </div>

          <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-2 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold">
              ✉️
            </div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Email </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">support@betlink.et / info@betlink.et</p>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2 p-6 sm:p-8 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
          <form @submit.prevent="submitContact" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                  Full Name <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  @input="onNameInput"
                  @keypress="onNameKeyPress"
                  type="text"
                  placeholder="e.g. Dawit Bekele"
                  class="w-full bg-slate-50 dark:bg-slate-800 border rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
                  :class="formErrors.name ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
                />
                <p v-if="formErrors.name" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.name }}</p>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                  Email Address <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="dawit@example.com"
                  class="w-full bg-slate-50 dark:bg-slate-800 border rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
                  :class="formErrors.email ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
                />
                <p v-if="formErrors.email" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.email }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Subject <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.subject"
                type="text"
                placeholder="e.g. Inquiring about property listing verification"
                class="w-full bg-slate-50 dark:bg-slate-800 border rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
                :class="formErrors.subject ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
              />
              <p v-if="formErrors.subject" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.subject }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Message <span class="text-rose-500">*</span>
              </label>
              <textarea
                v-model="form.message"
                rows="5"
                placeholder="How can our team assist you today?"
                class="w-full bg-slate-50 dark:bg-slate-800 border rounded-xl p-3.5 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 transition-colors"
                :class="formErrors.message ? 'border-rose-500' : 'border-slate-300 dark:border-slate-700'"
              ></textarea>
              <p v-if="formErrors.message" class="text-[11px] text-rose-500 mt-1 font-semibold">{{ formErrors.message }}</p>
            </div>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full sm:w-auto px-8 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-bold text-xs sm:text-sm transition-colors disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer shadow-xs"
            >
              <span v-if="submitting" class="animate-spin">⌛</span>
              <span>{{ submitting ? 'Sending Message...' : 'Send Message' }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toast'
import { contactService } from '../../services/contactService'

const router = useRouter()
const toastStore = useToastStore()
const submitting = ref(false)

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const formErrors = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const onNameKeyPress = (e) => {
  if (/[0-9]/.test(e.key)) {
    e.preventDefault()
  }
}

const onNameInput = (e) => {
  form.name = (e.target.value || '').replace(/[0-9]/g, '')
  if (formErrors.name) {
    if (!form.name.trim()) {
      formErrors.name = 'Full Name is required.'
    } else if (form.name.trim().length < 2) {
      formErrors.name = 'Name must be at least 2 characters.'
    } else {
      formErrors.name = ''
    }
  }
}

const submitContact = async () => {
  formErrors.name = ''
  formErrors.email = ''
  formErrors.subject = ''
  formErrors.message = ''

  let hasError = false
  if (!form.name || !form.name.trim()) {
    formErrors.name = 'Full Name is required.'
    hasError = true
  } else if (/\d/.test(form.name)) {
    formErrors.name = 'Name cannot contain numbers.'
    hasError = true
  } else if (form.name.trim().length < 2) {
    formErrors.name = 'Name must be at least 2 characters.'
    hasError = true
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!form.email || !form.email.trim()) {
    formErrors.email = 'Email address is required.'
    hasError = true
  } else if (!emailRegex.test(form.email.trim())) {
    formErrors.email = 'Please enter a valid email address.'
    hasError = true
  }

  if (!form.subject || !form.subject.trim()) {
    formErrors.subject = 'Subject is required.'
    hasError = true
  } else if (form.subject.trim().length < 3) {
    formErrors.subject = 'Subject must be at least 3 characters.'
    hasError = true
  }

  if (!form.message || !form.message.trim()) {
    formErrors.message = 'Message is required.'
    hasError = true
  } else if (form.message.trim().length < 10) {
    formErrors.message = 'Message must be at least 10 characters.'
    hasError = true
  }

  if (hasError) return

  submitting.value = true
  try {
    const res = await contactService.submitContact({
      name: form.name.trim(),
      email: form.email.trim(),
      subject: form.subject.trim(),
      message: form.message.trim(),
    })
    toastStore.success(res?.message || 'Your message has been sent successfully! Our team will respond shortly.')
    form.name = ''
    form.email = ''
    form.subject = ''
    form.message = ''
  } catch (err) {
    console.error('Failed to submit contact:', err)
    toastStore.error(err.message || 'Could not send your message. Please try again.')
  } finally {
    submitting.value = false
  }
}
</script>
