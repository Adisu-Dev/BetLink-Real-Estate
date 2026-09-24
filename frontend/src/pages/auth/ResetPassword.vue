<template>
  <AuthLayout>
    <div class="space-y-6">
      <!-- Success state -->
      <div v-if="resetSuccess" class="text-center space-y-6 py-2">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-200/60 dark:border-emerald-800/60 shadow-xs">
          <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>

        <div class="space-y-1.5">
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Password Reset Successfully</h1>
          <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">Your password has been updated. You can now sign in with your new password.</p>
        </div>

        <AuthButton @click="goToLogin">
          Sign In
        </AuthButton>
      </div>

      <!-- Invalid link state -->
      <div v-else-if="invalidLink" class="text-center space-y-6 py-2">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl border border-rose-200/60 dark:border-rose-800/60 shadow-xs">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>

        <div class="space-y-1.5">
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reset Link Expired</h1>
          <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">This password reset link is invalid or has expired.</p>
        </div>

        <div class="space-y-3">
          <AuthButton @click="requestNewLink">
            Request New Link
          </AuthButton>
          
          <RouterLink
            to="/login"
            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
          >
            ← Back to Login
          </RouterLink>
        </div>
      </div>

      <!-- Reset form -->
      <div v-else class="space-y-6">
        <!-- Header -->
        <div class="space-y-1.5 text-center sm:text-left">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Reset Password</h1>
          <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed">Choose a strong, secure password for your account.</p>
        </div>

        <!-- Error message -->
        <div v-if="errorMessage" class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center gap-2.5">
          <p class="text-xs text-rose-700 dark:text-rose-300 font-medium">{{ errorMessage }}</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleReset" autocomplete="off" class="space-y-5">
          <AuthInput
            id="email"
            v-model="form.email"
            type="email"
            label="Email Address"
            placeholder="name@example.com"
            required
            :error="errors.email"
          />

          <PasswordInput
            id="password"
            v-model="form.password"
            label="New Password"
            placeholder="••••••••"
            required
            :error="errors.password"
          />

          <!-- Password Requirements -->
          <PasswordRequirements v-if="form.password" :password="form.password" />

          <PasswordInput
            id="confirmPassword"
            v-model="form.confirmPassword"
            label="Confirm New Password"
            placeholder="••••••••"
            required
            :error="errors.confirmPassword"
          />

          <AuthButton type="submit" :loading="loading">
            {{ loading ? 'Resetting Password...' : 'Reset Password' }}
          </AuthButton>
        </form>

        <div class="text-center pt-1 border-t border-slate-100 dark:border-slate-800">
          <RouterLink
            to="/login"
            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
          >
            ← Back to Login
          </RouterLink>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import AuthLayout from '@/components/auth/AuthLayout.vue'
import AuthInput from '@/components/auth/AuthInput.vue'
import PasswordInput from '@/components/auth/PasswordInput.vue'
import PasswordRequirements from '@/components/auth/PasswordRequirements.vue'
import AuthButton from '@/components/auth/AuthButton.vue'
import { authService } from '@/services/authService'

const router = useRouter()
const route = useRoute()

const loading = ref(false)
const resetSuccess = ref(false)
const invalidLink = ref(false)
const errorMessage = ref('')

const form = reactive({
  email: '',
  password: '',
  confirmPassword: '',
  token: ''
})

const errors = reactive({
  email: '',
  password: '',
  confirmPassword: ''
})

onMounted(() => {
  form.token = route.query.token || ''
  form.email = route.query.email || ''
  
  if (!form.token) {
    invalidLink.value = true
  }
})

function validateForm() {
  let isValid = true
  errors.email = ''
  errors.password = ''
  errors.confirmPassword = ''

  if (!form.email) {
    errors.email = 'Email address is required'
    isValid = false
  }

  const passwordValid = 
    form.password.length >= 8 &&
    /[A-Z]/.test(form.password) &&
    /[a-z]/.test(form.password) &&
    /\d/.test(form.password) &&
    /[!@#$%^&*(),.?":{}|<>]/.test(form.password)

  if (!passwordValid) {
    errors.password = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character'
    isValid = false
  }

  if (form.password !== form.confirmPassword) {
    errors.confirmPassword = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

async function handleReset() {
  errorMessage.value = ''
  
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const response = await authService.resetPassword({
      token: form.token,
      email: form.email.trim(),
      password: form.password,
      password_confirmation: form.confirmPassword
    })

    if (response.success) {
      resetSuccess.value = true
    } else {
      errorMessage.value = response.message || 'Unable to reset password. Please try again.'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Unable to reset password. Please try again.'
    console.error('Password reset error:', error)
  } finally {
    loading.value = false
  }
}

function goToLogin() {
  router.push('/login')
}

function requestNewLink() {
  router.push('/forgot-password')
}
</script>
