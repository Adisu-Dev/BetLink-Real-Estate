<template>
  <AuthLayoutSplit
    image="https://images.unsplash.com/photo-1613977257363-707ba9348227?w=1600&auto=format&fit=crop&q=80"
    :headline="t('auth.welcome_back', 'Welcome Back')"
    :subtitle="t('auth.sign_in_subtitle', 'Sign in to access your saved properties, bookings, and inquiries.')"
    :quote="t('auth.login_quote', 'BetLink has modernized how we list and rent luxury apartments in Addis Ababa.')"
    :quoteAuthor="t('auth.login_quote_author', 'Solomon Haile, Property Developer')"
  >
    <!-- Header -->
    <div class="mb-6 sm:mb-8 text-center sm:text-left">
      <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
        {{ t('auth.welcome_back', 'Welcome Back') }}
      </h2>
    </div>

    <!-- Success Notice Banner (e.g. from registration) -->
    <transition name="fade">
      <div 
        v-if="infoMessage" 
        class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm flex items-start gap-3 shadow-xs"
        role="alert"
      >
        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1 font-medium leading-relaxed">{{ infoMessage }}</div>
        <button 
          @click="infoMessage = ''" 
          type="button" 
          class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 transition-colors cursor-pointer"
          aria-label="Dismiss message"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition>

    <!-- Alert Banner for 401 / Invalid Credentials -->
    <transition name="fade">
      <div 
        v-if="errorMessage" 
        class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-2xl text-red-700 dark:text-red-300 text-xs sm:text-sm flex items-start gap-3 shadow-xs"
        role="alert"
      >
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1 font-medium leading-relaxed">{{ errorMessage }}</div>
        <button 
          @click="errorMessage = ''" 
          type="button" 
          class="text-red-400 hover:text-red-600 dark:hover:text-red-200 transition-colors cursor-pointer"
          aria-label="Dismiss error"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition>

    <!-- Clean Native Login Form -->
    <form @submit.prevent="handleLogin" novalidate autocomplete="off" class="space-y-4 sm:space-y-5">
      <!-- Email Address Field with Clean Standard Text Input -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="login-email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
            {{ t('auth.email', 'Email Address') }} <span class="text-red-500">*</span>
          </label>
          <button
            v-if="form.email"
            type="button"
            @click="clearEmail"
            class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center gap-1 cursor-pointer transition-colors"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ t('common.clear', 'Clear') }}
          </button>
        </div>

        <div class="relative">
          <input
            id="login-email"
            ref="emailInputRef"
            name="email"
            v-model="form.email"
            type="email"
            autocomplete="off"
            :placeholder="t('auth.email_placeholder', 'Please enter your email')"
            required
            :class="[
              'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
              errors.email 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.email && !errors.email && isValidEmailFormat(form.email)
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
            @input="handleEmailInput"
            @change="handleEmailChange"
            @blur="validateEmail(true)"
          />
        </div>

        <!-- Field Specific Error Message -->
        <transition name="fade">
          <p v-if="errors.email" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.email }}</span>
          </p>
        </transition>
      </div>

      <!-- Password Field -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="login-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
            {{ t('auth.password', 'Password') }} <span class="text-red-500">*</span>
          </label>
        </div>

        <div class="relative">
          <input
            id="login-password"
            ref="passwordInputRef"
            name="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            :placeholder="t('auth.password_placeholder', 'Enter your password')"
            required
            autocomplete="new-password"
            :class="[
              'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none pr-10',
              errors.password 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.password && !errors.password && form.password.length >= 6
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
            @input="handlePasswordInput"
            @blur="validatePassword(true)"
          />
          <!-- Show/Hide Password Toggle -->
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
            tabindex="-1"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
          >
            <svg v-if="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>

        <!-- Field Specific Error Message -->
        <transition name="fade">
          <p v-if="errors.password" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.password }}</span>
          </p>
        </transition>
      </div>

      <!-- Remember Me & Forgot Password -->
      <div class="flex items-center justify-between text-xs pt-1">
        <label class="flex items-center gap-2 cursor-pointer group">
          <input
            v-model="form.rememberMe"
            type="checkbox"
            class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-slate-900 focus:ring-slate-900 focus:ring-offset-0 transition-colors cursor-pointer"
          />
          <span class="text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors font-medium">
            {{ t('auth.remember_me', 'Remember me') }}
          </span>
        </label>
        <RouterLink 
          to="/forgot-password" 
          class="text-slate-800 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white font-bold hover:underline transition-colors"
        >
          {{ t('auth.forgot_password', 'Forgot Password?') }}
        </RouterLink>
      </div>

      <!-- Submit Button with Loading Spinner -->
      <div class="pt-2">
        <button
          type="submit"
          :disabled="loading"
          :class="[
            'w-full py-4 px-6 rounded-2xl font-black text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2.5 shadow-md',
            !loading
              ? 'bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 cursor-pointer'
              : 'bg-slate-700 text-slate-300 dark:bg-slate-300 dark:text-slate-700 cursor-not-allowed opacity-80'
          ]"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? t('auth.signing_in', 'Signing in...') : t('auth.sign_in_btn', 'Sign In') }}</span>
        </button>
      </div>
    </form>

    <!-- Social Sign In Divider -->
    <div class="relative my-6 text-center">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
      </div>
      <span class="relative px-3 bg-white dark:bg-slate-900 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">
        {{ t('auth.or_continue_with', 'Or continue with') }}
      </span>
    </div>

    <!-- Social Buttons -->
    <div class="grid grid-cols-2 gap-3">
      <button
        type="button"
        class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs cursor-pointer"
      >
        <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Google
      </button>

      <button
        type="button"
        class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs cursor-pointer"
      >
        <svg class="w-4 h-4 flex-shrink-0" fill="#1877F2" viewBox="0 0 24 24">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
        Facebook
      </button>
    </div>

    <!-- Sign Up Link -->
    <div class="text-center mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
      <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
        {{ t('auth.dont_have_account', "Don't have an account?") }}
        <RouterLink 
          to="/register" 
          class="font-bold text-slate-900 dark:text-white hover:underline transition-colors ml-1"
        >
          {{ t('auth.create_account_btn', 'Create Account') }}
        </RouterLink>
      </p>
    </div>
  </AuthLayoutSplit>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'
import AuthLayoutSplit from '@/components/auth/AuthLayoutSplit.vue'

const router = useRouter()
const route = useRoute()
const toastStore = useToastStore()
const { login, getDashboardRoute } = useAuth()
const { t } = useLanguage()

const loading = ref(false)
const errorMessage = ref('')
const infoMessage = ref('')
const showPassword = ref(false)

const emailInputRef = ref(null)
const passwordInputRef = ref(null)

// Form state ALWAYS starts completely empty (Empty on Logout / Page Load / Refresh)
const form = reactive({
  email: '',
  password: '',
  rememberMe: false
})

const errors = reactive({
  email: '',
  password: ''
})

const isFieldTouched = reactive({
  email: false,
  password: false
})

function clearEmail() {
  form.email = ''
  form.password = ''
  errors.email = ''
  errors.password = ''
  isFieldTouched.email = false
  isFieldTouched.password = false
  if (emailInputRef.value) {
    emailInputRef.value.focus()
  }
}

function handleEmailInput() {
  isFieldTouched.email = true
  errors.email = ''
  errorMessage.value = ''
}

function handleEmailChange() {
  isFieldTouched.email = true
  errors.email = ''
  errorMessage.value = ''
}

function handlePasswordInput() {
  isFieldTouched.password = true
  errors.password = ''
  errorMessage.value = ''
}

function isValidEmailFormat(email) {
  const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/
  return emailRegex.test((email || '').trim())
}

function validateEmail(showError = true) {
  const email = form.email.trim()
  if (!email) {
    if (showError) errors.email = t('auth.email_required', 'Please enter your email address.')
    return false
  }
  
  if (!isValidEmailFormat(email)) {
    if (showError) errors.email = t('auth.email_invalid', 'Please enter a valid email address.')
    return false
  }
  
  errors.email = ''
  return true
}

function validatePassword(showError = true) {
  const password = form.password
  if (!password) {
    if (showError) errors.password = t('auth.password_required', 'Please enter your password.')
    return false
  }
  if (password.length < 6) {
    if (showError) errors.password = t('auth.password_min_length', 'Password must be at least 6 characters.')
    return false
  }
  errors.password = ''
  return true
}

// -------------------------------------------------------------
// SUBMISSION PIPELINE
// -------------------------------------------------------------
async function handleLogin() {
  errorMessage.value = ''
  infoMessage.value = ''
  
  const isEmailValid = validateEmail(true)
  const isPasswordValid = validatePassword(true)

  if (!isEmailValid || !isPasswordValid) {
    return
  }

  loading.value = true

  try {
    const user = await login({
      email: form.email.trim(),
      password: form.password,
      remember_me: form.rememberMe
    })

    const intendedRoute = route.query.redirect
    if (intendedRoute && typeof intendedRoute === 'string' && intendedRoute.startsWith('/')) {
      router.replace(intendedRoute)
    } else {
      const userRole = user?.roles?.[0]?.name || user?.role || 'buyer'
      router.replace(getDashboardRoute(userRole))
    }
  } catch (error) {
    console.error('Login process exception:', error)
    if (error.response) {
      const status = error.response.status
      const data = error.response.data

      if (status === 401 || status === 422) {
        errorMessage.value = data?.message || 'Invalid email or password. Please verify your credentials.'
        errors.email = 'Invalid credentials'
        errors.password = 'Invalid credentials'
      } else if (status === 429) {
        errorMessage.value = 'Too many login attempts. Please wait 60 seconds before trying again.'
      } else if (status >= 500) {
        errorMessage.value = 'Our servers are experiencing heavy load. Please try again shortly.'
      } else {
        errorMessage.value = data?.message || 'Unable to sign in. Please check your connection and credentials.'
      }
    } else if (error.request) {
      errorMessage.value = 'Cannot reach BetLink server. Please verify your internet connection.'
    } else {
      errorMessage.value = error.message || 'An unexpected error occurred during sign in.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  window.scrollTo({ top: 0, left: 0, behavior: 'instant' })
  // Ensure all form inputs start completely empty on page load, refresh, and logout
  form.email = ''
  form.password = ''
  form.rememberMe = false
  errors.email = ''
  errors.password = ''
  errorMessage.value = ''
  isFieldTouched.email = false
  isFieldTouched.password = false

  // If redirected after registration
  if (route.query.registered === '1') {
    infoMessage.value = route.query.email 
      ? `🎉 Account created! We sent your login password to ${route.query.email}. Please check your email inbox and enter your password below to sign in.`
      : '🎉 Account created! Please check your email inbox for your login password, enter it below, and sign in.'
    if (route.query.email) {
      form.email = route.query.email
    }
  }

  // If redirected for favorites
  if (route.query.redirect && String(route.query.redirect).includes('favorite')) {
    toastStore.info('Please sign in to view your saved favorites', 'Sign In Required')
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

input::-webkit-calendar-picker-indicator {
  display: none !important;
  -webkit-appearance: none !important;
  opacity: 0 !important;
}
</style>