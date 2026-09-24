<template>
  <AuthLayout :show-logo="false" :show-footer="false">
    <div class="space-y-6">
      <!-- Success state -->
      <div v-if="emailSent" class="text-center space-y-6 py-2">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-200/60 dark:border-emerald-800/60 shadow-xs">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        </div>

        <div class="space-y-1.5">
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Check Your Email</h1>
          <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed max-w-sm mx-auto">
            We've sent password reset instructions to
            <span class="font-semibold text-slate-900 dark:text-white">{{ form.email }}</span>
          </p>
        </div>

        <div class="space-y-3 pt-2">
          <AuthButton
            variant="secondary"
            @click="openEmailClient"
          >
            Open Email App
          </AuthButton>

          <div class="text-xs text-slate-500 dark:text-slate-400 pt-1">
            <p>Didn't receive the email?</p>
            <button
              v-if="canResend"
              @click="resendEmail"
              :disabled="resending"
              class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 cursor-pointer disabled:opacity-50 transition-colors"
            >
              {{ resending ? 'Sending...' : 'Click here to resend' }}
            </button>
            <p v-else class="text-slate-400 mt-0.5">
              Resend available in {{ resendCountdown }}s
            </p>
          </div>

          <div class="pt-2">
            <RouterLink
              to="/login"
              class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span>Back to Sign In</span>
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- Request form -->
      <div v-else class="space-y-6">
        <!-- Header -->
        <div class="space-y-1.5 text-center sm:text-left">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            {{ t('auth.forgot_password', 'Forgot Password?') }}
          </h1>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage" class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-xs text-rose-700 dark:text-rose-300 font-medium">{{ errorMessage }}</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" autocomplete="off" class="space-y-5">
          <AuthInput
            id="email"
            v-model="form.email"
            type="email"
            :label="t('auth.email', 'Email Address')"
            :placeholder="t('auth.email_placeholder', 'name@example.com')"
            required
          />

          <AuthButton type="submit" :loading="loading">
            {{ loading ? t('common.loading', 'Resetting...') : 'Reset' }}
          </AuthButton>
        </form>

        <div class="text-center pt-1 border-t border-slate-100 dark:border-slate-800">
          <RouterLink
            to="/login"
            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>{{ t('auth.sign_in_btn', 'Back to Sign In') }}</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import AuthLayout from '@/components/auth/AuthLayout.vue'
import AuthInput from '@/components/auth/AuthInput.vue'
import AuthButton from '@/components/auth/AuthButton.vue'
import { useLanguage } from '@/composables/useLanguage'

const { t } = useLanguage()

import { authService } from '@/services/authService'

const loading = ref(false)
const resending = ref(false)
const emailSent = ref(false)
const errorMessage = ref('')
const canResend = ref(false)
const resendCountdown = ref(30)

let countdownInterval = null

const form = reactive({
  email: ''
})

const errors = reactive({
  email: ''
})

onMounted(() => {
  form.email = ''
  errors.email = ''
  errorMessage.value = ''
  emailSent.value = false
})

function validateForm() {
  errors.email = ''

  if (!form.email) {
    errors.email = 'Email is required'
    return false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    return false
  }

  return true
}

function startResendCountdown() {
  canResend.value = false
  resendCountdown.value = 30

  if (countdownInterval) clearInterval(countdownInterval)
  countdownInterval = setInterval(() => {
    resendCountdown.value--
    if (resendCountdown.value <= 0) {
      clearInterval(countdownInterval)
      canResend.value = true
    }
  }, 1000)
}

async function handleSubmit() {
  errorMessage.value = ''
  
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const response = await authService.forgotPassword({ email: form.email.trim() })
    if (response.success) {
      emailSent.value = true
      startResendCountdown()
    } else {
      errorMessage.value = response.message || 'Unable to send reset link. Please try again.'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Unable to send reset link. Please try again.'
    console.error('Password reset error:', error)
  } finally {
    loading.value = false
  }
}

async function resendEmail() {
  if (!canResend.value) return
  resending.value = true
  errorMessage.value = ''

  try {
    const response = await authService.forgotPassword({ email: form.email.trim() })
    if (response.success) {
      startResendCountdown()
    } else {
      errorMessage.value = response.message || 'Unable to resend email. Please try again.'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Unable to resend email. Please try again.'
    console.error('Resend error:', error)
  } finally {
    resending.value = false
  }
}

function openEmailClient() {
  window.location.href = 'mailto:'
}

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>
