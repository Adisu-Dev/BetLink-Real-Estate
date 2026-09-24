<template>
  <AuthLayoutSplit
    image="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1600&auto=format&fit=crop&q=80"
    :headline="t('auth.create_account', 'Create an Account')"
    :subtitle="t('auth.create_account_subtitle', 'Join BetLink to search, buy, or list properties in Ethiopia.')"
    :quote="t('auth.register_quote', 'BetLink made finding our dream apartment in Bole effortless with direct owner contacts.')"
    :quoteAuthor="t('auth.register_quote_author', 'Abebe Kebede, Home Buyer')"
  >
    <!-- Form Header -->
    <div class="mb-5 text-center sm:text-left">
      <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
        {{ currentStep === 1 ? t('auth.create_account', 'Create an Account') : t('auth.verify_email_title', 'Verify Your Email Address') }}
      </h2>
      <p v-if="currentStep === 2" class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
        {{ t('auth.otp_sent_msg', 'A 6-digit verification code was sent to your email.') }}
      </p>
    </div>



    <!-- Global Error Banner -->
    <transition name="fade">
      <div 
        v-if="errorMessage" 
        class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800/80 rounded-2xl text-red-700 dark:text-red-300 text-xs sm:text-sm flex items-start gap-3 shadow-xs"
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

    <!-- STEP 1: Registration Details Form -->
    <form v-if="currentStep === 1" @submit.prevent="handleRegister" novalidate autocomplete="off" class="space-y-4 sm:space-y-5">
      
      <!-- 1. Role Selection -->
      <div>
        <label for="reg-role" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          {{ t('auth.role', 'Role') }} <span class="text-red-500">*</span>
        </label>
        
        <div class="relative">
          <select
            id="reg-role"
            name="role"
            v-model="form.role"
            @change="handleRoleChange"
            :class="[
              'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 font-medium appearance-none cursor-pointer pr-10 transition-all duration-200 focus:outline-none',
              errors.role 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.role && !errors.role && form.role
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
          >
            <option value="buyer">{{ t('auth.role_buyer', 'Buyer / Tenant') }}</option>
            <option value="owner">{{ t('auth.role_owner', 'Owner') }}</option>
            <option value="agent">{{ t('auth.role_agent', 'Agent') }}</option>
          </select>
          <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>

        <transition name="fade">
          <p v-if="errors.role" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.role }}</span>
          </p>
        </transition>
      </div>

      <!-- 2. Full Name -->
      <div>
        <label for="reg-fullname" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          {{ t('auth.full_name', 'Full Name') }} <span class="text-red-500">*</span>
        </label>
        
        <div class="relative">
          <input
            id="reg-fullname"
            ref="nameInputRef"
            name="name"
            v-model="form.fullName"
            type="text"
            :placeholder="t('auth.full_name_placeholder', 'e.g. Abebe Kebede')"
            required
            maxlength="60"
            autocomplete="name"
            :class="[
              'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
              errors.fullName 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.fullName && !errors.fullName && form.fullName.trim().length >= 3
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
            @keydown="handleNameKeydown"
            @input="handleNameInput"
            @blur="validateFullName(true)"
          />
          <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
            <svg 
              v-if="isFieldTouched.fullName && !errors.fullName && form.fullName.trim().length >= 3" 
              class="w-4 h-4 text-emerald-500" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2.5" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <svg 
              v-else-if="errors.fullName" 
              class="w-4 h-4 text-red-500" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>

        <!-- Field Specific Error Message -->
        <transition name="fade">
          <p v-if="errors.fullName" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.fullName }}</span>
          </p>
        </transition>
      </div>

      <!-- 3. Email Address -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="reg-email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
            {{ t('auth.email', 'Email Address') }} <span class="text-red-500">*</span>
          </label>
          <button
            v-if="form.email"
            type="button"
            @click="clearEmail"
            class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center gap-0.5 cursor-pointer"
          >
            Clear
          </button>
        </div>
        
        <div class="relative">
          <input
            id="reg-email"
            ref="emailInputRef"
            name="email"
            v-model="form.email"
            type="email"
            :placeholder="t('auth.email_placeholder', 'name@example.com')"
            required
            autocomplete="email"
            :class="[
              'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
              errors.email 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.email && !errors.email && isValidEmailFormat(form.email)
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
            @input="handleEmailInput"
            @blur="validateEmail(true)"
          />
          <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
            <svg 
              v-if="isFieldTouched.email && !errors.email && isValidEmailFormat(form.email)" 
              class="w-4 h-4 text-emerald-500" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2.5" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <svg 
              v-else-if="errors.email" 
              class="w-4 h-4 text-red-500" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
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

      <!-- 4. Phone Number -->
      <div>
        <label for="reg-phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          {{ t('auth.phone_number', 'Phone Number') }} <span class="text-red-500">*</span>
        </label>
        
        <div class="flex gap-2">
          <!-- Country Code Selector with Flag -->
          <div class="w-28 sm:w-32 flex-shrink-0">
            <select
              v-model="form.countryCode"
              @change="handleCountryCodeChange"
              class="w-full px-2.5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-xs sm:text-sm text-slate-900 dark:text-slate-100 font-bold focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900 cursor-pointer text-center"
            >
              <option value="+251">🇪🇹 +251</option>
              <option value="+1">🇺🇸 +1</option>
              <option value="+44">🇬🇧 +44</option>
              <option value="+971">🇦🇪 +971</option>
              <option value="+49">🇩🇪 +49</option>
              <option value="+254">🇰🇪 +254</option>
            </select>
          </div>
          
          <!-- Phone Number Input -->
          <div class="flex-1 relative">
            <input
              id="reg-phone"
              ref="phoneInputRef"
              name="phone"
              v-model="form.phone"
              type="tel"
              :placeholder="t('auth.phone_placeholder', '0911 234 567 or 911 234 567')"
              required
              maxlength="20"
              autocomplete="tel"
              :class="[
                'w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
                errors.phone 
                  ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                  : isFieldTouched.phone && !errors.phone && isValidPhoneFormat(form.phone, form.countryCode)
                    ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                    : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
              ]"
              @keydown="handlePhoneKeydown"
              @input="handlePhoneInput"
              @blur="validatePhone(true)"
            />
            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
              <svg 
                v-if="isFieldTouched.phone && !errors.phone && isValidPhoneFormat(form.phone, form.countryCode)" 
                class="w-4 h-4 text-emerald-500" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2.5" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <svg 
                v-else-if="errors.phone" 
                class="w-4 h-4 text-red-500" 
                fill="currentColor" 
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Field Specific Error Message -->
        <transition name="fade">
          <p v-if="errors.phone" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.phone }}</span>
          </p>
        </transition>
      </div>

      <!-- 5. Password Fields -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="reg-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
            {{ t('auth.password', 'Password') }} <span class="text-red-500">*</span>
          </label>
        </div>
          
        <div class="relative">
          <input
            id="reg-password"
            ref="passwordInputRef"
            name="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            required
            autocomplete="new-password"
            :class="[
              'w-full px-4 py-3 pr-11 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
              errors.password 
                ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                : isFieldTouched.password && !errors.password && form.password.length >= 6
                  ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                  : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
            ]"
            @input="handlePasswordInput"
            @blur="validatePassword(true)"
          />
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

        <!-- Confirm Password Field -->
        <div>
          <label for="reg-confirm-password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            {{ t('auth.confirm_password', 'Confirm Password') }} <span class="text-red-500">*</span>
          </label>
          
          <div class="relative">
            <input
              id="reg-confirm-password"
              ref="confirmPasswordInputRef"
              name="password_confirmation"
              v-model="form.confirmPassword"
              :type="showConfirmPassword ? 'text' : 'password'"
              placeholder="••••••••"
              required
              autocomplete="new-password"
              :class="[
                'w-full px-4 py-3 pr-11 rounded-2xl border text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition-all duration-200 focus:outline-none',
                errors.confirmPassword 
                  ? 'border-red-500 bg-red-50 dark:bg-red-950/30 focus:ring-2 focus:ring-red-500 focus:border-red-500' 
                  : isFieldTouched.confirmPassword && !errors.confirmPassword && form.confirmPassword === form.password && form.confirmPassword.length > 0
                    ? 'border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
                    : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:border-slate-900'
              ]"
              @input="handleConfirmPasswordInput"
              @blur="validateConfirmPassword(true)"
            />
            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer"
              tabindex="-1"
              :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
            >
              <svg v-if="showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            <p v-if="errors.confirmPassword" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <span>{{ errors.confirmPassword }}</span>
            </p>
          </transition>
        </div>

      <!-- 7. Terms & Conditions Checkbox -->
      <div class="pt-1">
        <label class="flex items-start gap-3 cursor-pointer group">
          <input
            v-model="form.agreeToTerms"
            @change="handleTermsChange"
            type="checkbox"
            class="w-4 h-4 mt-0.5 rounded-md border-slate-300 dark:border-slate-700 text-slate-900 focus:ring-2 focus:ring-slate-900 transition-all cursor-pointer"
          />
          <span class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">
            {{ t('auth.terms_agreement', "I agree to BetLink's Terms of Service and Privacy Policy") }}
            <span class="text-red-500">*</span>
          </span>
        </label>
        
        <transition name="fade">
          <p v-if="errors.agreeToTerms" class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ errors.agreeToTerms }}</span>
          </p>
        </transition>
      </div>

      <!-- Submit Button (Step 1) -->
      <div class="pt-2">
        <button
          type="submit"
          :disabled="!isFormValid || loading"
          :class="[
            'w-full py-4 px-6 rounded-2xl font-black text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2.5 shadow-md',
            isFormValid && !loading
              ? 'bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 shadow-slate-900/10 cursor-pointer'
              : 'bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-500 cursor-not-allowed shadow-none'
          ]"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ loading ? t('auth.sending_otp', 'Sending verification code...') : t('auth.continue_verification', 'Verify Email & Continue') }}</span>
        </button>
      </div>
    </form>

    <!-- STEP 2: 6-Digit OTP Verification Screen -->
    <div v-if="currentStep === 2" class="space-y-5">
      <!-- Dev Mode OTP banner for instant testing -->
      <transition name="fade">
        <div 
          v-if="devOtp" 
          class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/70 rounded-2xl text-xs flex items-center justify-between gap-3 shadow-xs"
        >
          <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white font-bold text-[10px]">OTP</span>
            <div>
              <span class="text-slate-600 dark:text-slate-400 font-medium">Verification Code:</span>
              <strong class="ml-1.5 font-mono font-black text-sm tracking-widest text-slate-900 dark:text-white">{{ devOtp }}</strong>
            </div>
          </div>
          <button 
            type="button" 
            @click="fillDevOtp" 
            class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition-all cursor-pointer shadow-xs active:scale-95"
          >
            Auto-fill Code
          </button>
        </div>
      </transition>

      <!-- Success notification if newly sent -->
      <transition name="fade">
        <div 
          v-if="otpSuccessMessage" 
          class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm flex items-start gap-3 shadow-xs"
        >
          <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="flex-1 font-medium leading-relaxed">{{ otpSuccessMessage }}</div>
        </div>
      </transition>

      <!-- Recipient destinations info -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs gap-2">
        <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
          <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span class="font-medium truncate max-w-[200px]">{{ form.email }}</span>
        </div>
        <div v-if="form.phone" class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
          <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <span class="font-mono text-xs">{{ form.countryCode }} {{ form.phone }}</span>
        </div>
      </div>

      <!-- OTP Input Form -->
      <form @submit.prevent="handleVerifyOtp" class="space-y-4 sm:space-y-5">
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
              {{ t('auth.enter_otp', 'Enter 6-digit code') }} <span class="text-red-500">*</span>
            </label>
          </div>

          <div class="relative">
            <input
              ref="otpInputRef"
              v-model="otpCode"
              type="text"
              inputmode="numeric"
              maxlength="6"
              pattern="[0-9]{6}"
              placeholder="••••••"
              autocomplete="one-time-code"
              class="w-full text-center tracking-[0.5em] font-mono text-2xl sm:text-3xl font-black px-4 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-400 focus:outline-none transition-all duration-200"
              @input="otpError = ''"
            />
          </div>

          <transition name="fade">
            <p v-if="otpError" class="mt-2 text-xs text-red-600 dark:text-red-400 font-semibold flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <span>{{ otpError }}</span>
            </p>
          </transition>
        </div>

        <!-- Timer & Resend Controls (Email / SMS) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 px-1 text-xs gap-2">
          <div class="flex items-center gap-1.5 font-semibold text-slate-500 dark:text-slate-400">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>{{ t('auth.otp_expires_in', 'Code expires in') }}: <strong class="text-slate-900 dark:text-white font-mono font-black">{{ formattedOtpTimer }}</strong></span>
          </div>

          <div class="flex items-center gap-3">
            <button
              type="button"
              :disabled="resendCooldown > 0 || loading"
              @click="handleResendOtp('email')"
              class="font-bold text-xs transition-colors cursor-pointer text-slate-900 dark:text-white hover:underline disabled:text-slate-400 disabled:no-underline disabled:cursor-not-allowed"
            >
              <span v-if="resendCooldown > 0">{{ t('auth.resend_in', 'Resend in {s}s').replace('{s}', resendCooldown) }}</span>
              <span v-else>Resend via Email</span>
            </button>

            <span v-if="resendCooldown === 0 && form.phone" class="text-slate-300 dark:text-slate-600">|</span>

            <button
              v-if="resendCooldown === 0 && form.phone"
              type="button"
              :disabled="loading"
              @click="handleResendOtp('sms')"
              class="font-bold text-xs transition-colors cursor-pointer text-emerald-600 dark:text-emerald-400 hover:underline disabled:text-slate-400"
            >
              Send via SMS
            </button>
          </div>
        </div>

        <!-- Submit & Back Buttons -->
        <div class="pt-2 space-y-2.5">
          <button
            type="submit"
            :disabled="otpCode.length < 6 || loading"
            class="w-full py-4 px-6 rounded-2xl font-black text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2.5 shadow-md bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed cursor-pointer"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? t('auth.verifying', 'Verifying...') : t('auth.verify_and_activate', 'Verify & Activate Account') }}</span>
          </button>

          <button
            type="button"
            @click="backToForm"
            class="w-full py-2 text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer text-center"
          >
            ← {{ t('auth.edit_email', 'Change email / edit info') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Sign In Link -->
    <div class="text-center mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
      <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
        {{ t('auth.already_have_account', 'Already have an account?') }}
        <RouterLink 
          to="/login" 
          class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors ml-1"
        >
          {{ t('auth.sign_in_btn', 'Sign In') }}
        </RouterLink>
      </p>
    </div>

  </AuthLayoutSplit>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, onBeforeUnmount } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useLanguage } from '@/composables/useLanguage'
import AuthLayoutSplit from '@/components/auth/AuthLayoutSplit.vue'

const router = useRouter()
const { register, sendOtp, verifyOtp, getDashboardRoute } = useAuth()
const { t } = useLanguage()

const currentStep = ref(1) // 1 = Registration Form, 2 = 6-digit OTP Verification
const otpCode = ref('')
const otpError = ref('')
const otpSuccessMessage = ref('')
const devOtp = ref('')
const otpTimeLeft = ref(120) // 2 minutes
const resendCooldown = ref(0)
let timerInterval = null
let resendInterval = null
const otpInputRef = ref(null)

function fillDevOtp() {
  if (devOtp.value) {
    otpCode.value = devOtp.value
    otpError.value = ''
  }
}

const formattedOtpTimer = computed(() => {
  const m = Math.floor(otpTimeLeft.value / 60)
  const s = otpTimeLeft.value % 60
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

function startOtpTimer() {
  if (timerInterval) clearInterval(timerInterval)
  otpTimeLeft.value = 120 // 2 minutes
  timerInterval = setInterval(() => {
    if (otpTimeLeft.value > 0) {
      otpTimeLeft.value--
    } else {
      clearInterval(timerInterval)
      otpError.value = t('auth.otp_expired', 'Verification code has expired. Please request a new code.')
    }
  }, 1000)
}

function startResendCooldown() {
  if (resendInterval) clearInterval(resendInterval)
  resendCooldown.value = 60
  resendInterval = setInterval(() => {
    if (resendCooldown.value > 0) {
      resendCooldown.value--
    } else {
      clearInterval(resendInterval)
    }
  }, 1000)
}

let webOtpAbortController = null

function listenForWebOtp() {
  if (typeof window !== 'undefined' && 'OTPCredential' in window) {
    try {
      if (webOtpAbortController) {
        webOtpAbortController.abort()
      }
      webOtpAbortController = new AbortController()
      navigator.credentials.get({
        otp: { transport: ['sms'] },
        signal: webOtpAbortController.signal
      }).then(credential => {
        if (credential && credential.code) {
          otpCode.value = credential.code
        }
      }).catch(() => {})
    } catch (e) {}
  }
}

// Automatically verify and redirect to dashboard once 6 digits are entered
watch(otpCode, (newVal) => {
  if (newVal) {
    const clean = newVal.replace(/\D/g, '')
    if (clean.length === 6 && !loading.value) {
      handleVerifyOtp()
    }
  }
})

function backToForm() {
  if (webOtpAbortController) {
    try { webOtpAbortController.abort() } catch (e) {}
  }
  currentStep.value = 1
  otpError.value = ''
  otpSuccessMessage.value = ''
}

onBeforeUnmount(() => {
  if (timerInterval) clearInterval(timerInterval)
  if (resendInterval) clearInterval(resendInterval)
  if (webOtpAbortController) {
    try { webOtpAbortController.abort() } catch (e) {}
  }
})

const loading = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const isEmailManuallyEdited = ref(false)
const isPasswordManuallyEdited = ref(false)

const nameInputRef = ref(null)
const emailInputRef = ref(null)
const phoneInputRef = ref(null)
const passwordInputRef = ref(null)
const confirmPasswordInputRef = ref(null)

const form = reactive({
  role: 'buyer',
  fullName: '',
  email: '',
  countryCode: '+251',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeToTerms: false
})

const errors = reactive({
  role: '',
  fullName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeToTerms: ''
})

const isFieldTouched = reactive({
  role: false,
  fullName: false,
  email: false,
  phone: false,
  password: false,
  confirmPassword: false,
  agreeToTerms: false
})

function selectRole(role) {
  form.role = role
  errors.role = ''
  errorMessage.value = ''
  isFieldTouched.role = true
}

function handleRoleChange() {
  errors.role = ''
  errorMessage.value = ''
  isFieldTouched.role = true
}

function handleCountryCodeChange() {
  errors.phone = ''
  errorMessage.value = ''
  validatePhone(false)
}

function unlockManualEmail() {
  isEmailManuallyEdited.value = true
  if (emailInputRef.value) {
    emailInputRef.value.focus()
  }
}

function clearEmail() {
  form.email = ''
  isEmailManuallyEdited.value = true
  errors.email = ''
  isFieldTouched.email = false
  if (emailInputRef.value) {
    emailInputRef.value.focus()
  }
}

function autoGeneratePasswordFromNameOrEmail() {
  if (isPasswordManuallyEdited.value) return

  const nameParts = form.fullName.trim().split(/\s+/).filter(Boolean)
  let base = 'BetLink'
  if (nameParts.length > 0) {
    const clean = nameParts[0].replace(/[^a-zA-Z]/g, '')
    if (clean.length >= 2) {
      base = clean.charAt(0).toUpperCase() + clean.slice(1, 6).toLowerCase()
    }
  } else if (form.email && form.email.includes('@')) {
    const clean = form.email.split('@')[0].replace(/[^a-zA-Z]/g, '')
    if (clean.length >= 2) {
      base = clean.charAt(0).toUpperCase() + clean.slice(1, 6).toLowerCase()
    }
  }

  const specialChars = ['@', '#', '$', '!']
  const randomChar = specialChars[Math.floor(Math.random() * specialChars.length)]
  const randomNum = Math.floor(1000 + Math.random() * 9000)
  const genPass = `${base}${randomChar}${randomNum}!`

  form.password = genPass
  form.confirmPassword = genPass
  isFieldTouched.password = true
  isFieldTouched.confirmPassword = true
  validatePassword(false)
  validateConfirmPassword(false)
}

function regeneratePassword() {
  isPasswordManuallyEdited.value = false
  autoGeneratePasswordFromNameOrEmail()
}

function clearPassword() {
  form.password = ''
  form.confirmPassword = ''
  isPasswordManuallyEdited.value = true
  errors.password = ''
  errors.confirmPassword = ''
  isFieldTouched.password = false
  isFieldTouched.confirmPassword = false
  if (passwordInputRef.value) {
    passwordInputRef.value.focus()
  }
}

// -------------------------------------------------------------
// 1. FULL NAME VALIDATION & REAL-TIME NUMBER BLOCKING
// -------------------------------------------------------------
function handleNameKeydown(e) {
  // Allow control & navigation keys
  if (['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
    return
  }
  if (e.ctrlKey || e.metaKey || e.altKey) {
    return
  }

  // Block numeric keys (0-9)
  if (/^[0-9]$/.test(e.key)) {
    e.preventDefault()
    errors.fullName = 'Numbers are not allowed in full name. Only letters and spaces are permitted.'
    return
  }

  // Allow letters (including Ethiopian Ge'ez Unicode characters \u1200-\u137F), spaces, hyphens, and apostrophes
  const validCharRegex = /^[a-zA-Z\u1200-\u137F\s\-']$/
  if (!validCharRegex.test(e.key)) {
    e.preventDefault()
    errors.fullName = 'Special symbols are not allowed in full name. Only letters, spaces, hyphens, and apostrophes are permitted.'
    return
  }
}

function handleNameInput(e) {
  isFieldTouched.fullName = true
  errors.fullName = ''
  errorMessage.value = ''
  const rawValue = e.target.value

  // Strip any numbers or invalid characters that might have been pasted
  const sanitized = rawValue.replace(/[0-9]/g, '').replace(/[^a-zA-Z\u1200-\u137F\s\-']/g, '')
  if (sanitized !== rawValue) {
    form.fullName = sanitized
    errors.fullName = 'Numbers and special symbols were removed. Full name can only contain letters and spaces.'
  } else {
    validateFullName(false)
  }

  // Trigger smart password auto-generation based on entered full name
  if (form.fullName.trim().length >= 2 && !isPasswordManuallyEdited.value) {
    autoGeneratePasswordFromNameOrEmail()
  }
}

function validateFullName(showError = true) {
  const name = form.fullName.trim()
  if (!name) {
    if (showError) errors.fullName = t('auth.name_required', 'Full Name is required.')
    return false
  }
  if (name.length < 3) {
    if (showError) errors.fullName = t('auth.name_min_length', 'Full Name must be at least 3 characters long.')
    return false
  }
  if (/[0-9]/.test(name)) {
    if (showError) errors.fullName = t('auth.name_no_numbers', 'Numbers are not allowed in full name.')
    return false
  }
  if (!/^[a-zA-Z\u1200-\u137F\s\-']+$/.test(name)) {
    if (showError) errors.fullName = t('auth.name_invalid_chars', 'Full name can only contain letters, spaces, hyphens, and apostrophes.')
    return false
  }
  errors.fullName = ''
  return true
}

// -------------------------------------------------------------
// 2. EMAIL VALIDATION
// -------------------------------------------------------------
function isValidEmailFormat(email) {
  const re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/
  return re.test(email.trim())
}

function handleEmailInput() {
  isEmailManuallyEdited.value = true
  isFieldTouched.email = true
  errors.email = ''
  errorMessage.value = ''
  validateEmail(false)

  if (!form.password && !isPasswordManuallyEdited.value) {
    autoGeneratePasswordFromNameOrEmail()
  }
}

function validateEmail(showError = true) {
  const email = form.email.trim()
  if (!email) {
    if (showError) errors.email = t('auth.email_required', 'Email address is required.')
    return false
  }
  if (!isValidEmailFormat(email)) {
    if (showError) errors.email = t('auth.email_invalid', 'Please enter a valid email address (e.g. name@example.com).')
    return false
  }
  errors.email = ''
  return true
}

// -------------------------------------------------------------
// 3. PHONE NUMBER VALIDATION (ETHIOPIAN FORMAT)
// -------------------------------------------------------------
function handlePhoneKeydown(e) {
  // Allow control & navigation keys
  if (['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
    return
  }
  if (e.ctrlKey || e.metaKey || e.altKey) {
    return
  }

  // Block letters and symbols except +, space, hyphens
  if (!/^[0-9\s\+\-]$/.test(e.key)) {
    e.preventDefault()
    errors.phone = 'Phone number can only contain digits.'
    return
  }
}

function handlePhoneInput(e) {
  isFieldTouched.phone = true
  errors.phone = ''
  errorMessage.value = ''
  const rawValue = e.target.value

  // Strip non-digits except + and spaces
  const sanitized = rawValue.replace(/[^0-9\s\+\-]/g, '')
  if (sanitized !== rawValue) {
    form.phone = sanitized
  } else {
    validatePhone(false)
  }
}

function isValidPhoneFormat(phone, countryCode = form.countryCode) {
  if (!phone) return false
  const clean = phone.replace(/[\s\-\(\)]/g, '')
  if (!clean) return false

  if (countryCode === '+251') {
    // Ethiopian format:
    // +251 9/7... (e.g. +251911234567, +251711234567)
    // 09/07 with 9 digits after 0 (e.g. 0911234567, 0711234567)
    // or 9 digits starting with 9 or 7 (e.g. 911234567, 711234567)
    return /^(?:\+?251|0)?([97]\d{8})$/.test(clean)
  } else {
    return /^\+?\d{7,15}$/.test(clean)
  }
}

function validatePhone(showError = true) {
  const rawDigits = (form.phone || '').trim()
  if (!rawDigits) {
    if (showError) errors.phone = t('auth.phone_required', 'Phone number is required.')
    return false
  }

  if (form.countryCode === '+251') {
    if (!isValidPhoneFormat(rawDigits, '+251')) {
      if (showError) {
        errors.phone = t('auth.phone_invalid', 'Enter a valid Ethiopian phone number (+251 9/7... or 09/07 with 9 digits).')
      }
      return false
    }
  } else {
    if (!isValidPhoneFormat(rawDigits, form.countryCode)) {
      if (showError) errors.phone = t('auth.phone_invalid_generic', 'Phone number must be between 7 and 15 digits.')
      return false
    }
  }

  errors.phone = ''
  return true
}

function handlePasswordInput() {
  isPasswordManuallyEdited.value = true
  isFieldTouched.password = true
  errors.password = ''
  errorMessage.value = ''
  validatePassword(false)
  if (form.confirmPassword) {
    validateConfirmPassword(false)
  }
}

function validatePassword(showError = true) {
  const p = form.password || ''
  if (!p) {
    if (showError) errors.password = 'Password is required.'
    return false
  }
  if (p.length < 6) {
    if (showError) errors.password = 'Password must be at least 6 characters.'
    return false
  }
  errors.password = ''
  return true
}

// -------------------------------------------------------------
// 5. CONFIRM PASSWORD VALIDATION
// -------------------------------------------------------------
function handleConfirmPasswordInput() {
  isPasswordManuallyEdited.value = true
  isFieldTouched.confirmPassword = true
  errors.confirmPassword = ''
  errorMessage.value = ''
  validateConfirmPassword(false)
}

function validateConfirmPassword(showError = true) {
  if (!form.confirmPassword) {
    if (showError) errors.confirmPassword = t('auth.confirm_password_required', 'Password confirmation is required.')
    return false
  }
  if (form.confirmPassword !== form.password) {
    if (showError) errors.confirmPassword = t('auth.passwords_do_not_match', 'Passwords do not match. Please ensure both passwords match exactly.')
    return false
  }
  errors.confirmPassword = ''
  return true
}

// -------------------------------------------------------------
// 6. TERMS AGREEMENT CHECKBOX
// -------------------------------------------------------------
function handleTermsChange() {
  isFieldTouched.agreeToTerms = true
  errorMessage.value = ''
  if (form.agreeToTerms) {
    errors.agreeToTerms = ''
  }
}

// -------------------------------------------------------------
// FORM VALIDITY STATE
// -------------------------------------------------------------
const isFormValid = computed(() => {
  const isRoleOk = Boolean(form.role)
  const isNameOk = form.fullName.trim().length >= 3 && !/[0-9]/.test(form.fullName) && /^[a-zA-Z\u1200-\u137F\s\-']+$/.test(form.fullName.trim())
  const isEmailOk = isValidEmailFormat(form.email)
  const isPhoneOk = isValidPhoneFormat(form.phone, form.countryCode)
  const isTermsOk = Boolean(form.agreeToTerms)
  const isPassOk = form.password && form.password.length >= 6
  const isConfirmOk = form.confirmPassword === form.password && form.confirmPassword.length > 0

  return isRoleOk && isNameOk && isEmailOk && isPhoneOk && isPassOk && isConfirmOk && isTermsOk
})

// -------------------------------------------------------------
// 7. SUBMIT & BACKEND ERROR MAPPING
// -------------------------------------------------------------
async function handleRegister() {
  errorMessage.value = ''

  if (!form.role) {
    errors.role = t('auth.role_required', 'Please select an account role.')
  }

  const isNameValid = validateFullName(true)
  const isEmailValid = validateEmail(true)
  const isPhoneValid = validatePhone(true)
  const isPassValid = validatePassword(true)
  const isConfirmValid = validateConfirmPassword(true)

  if (!form.agreeToTerms) {
    errors.agreeToTerms = t('auth.terms_required', 'You must agree to the Terms of Service & Privacy Policy before creating an account.')
  }

  if (!form.role || !isNameValid || !isEmailValid || !isPhoneValid || !isPassValid || !isConfirmValid || !form.agreeToTerms) {
    if (!form.role) window.scrollTo({ top: 0, behavior: 'smooth' })
    else if (!isNameValid) nameInputRef.value?.focus()
    else if (!isEmailValid) emailInputRef.value?.focus()
    else if (!isPhoneValid) phoneInputRef.value?.focus()
    else if (!isPassValid) passwordInputRef.value?.focus()
    else if (!isConfirmValid) confirmPasswordInputRef.value?.focus()
    return
  }

  loading.value = true

  try {
    const rawDigits = form.phone.replace(/[\s\-\(\)]/g, '')
    let cleanPhone = rawDigits
    if (form.countryCode === '+251') {
      const match = rawDigits.match(/([97]\d{8})$/)
      if (match) {
        cleanPhone = match[1]
      }
    } else if (cleanPhone.startsWith('0')) {
      cleanPhone = cleanPhone.slice(1)
    }
    const fullPhoneNumber = `${form.countryCode}${cleanPhone}`

    const payload = {
      name: form.fullName.trim(),
      email: form.email.trim(),
      phone: fullPhoneNumber,
      role: form.role,
      password: form.password,
    }

    const result = await sendOtp(payload)

    if (result && (result.success || result.data)) {
      currentStep.value = 2
      otpError.value = ''
      const resPayload = result.data || result
      const foundOtp = resPayload?.dev_otp || resPayload?.otp || result?.dev_otp || result?.otp
      if (foundOtp) {
        devOtp.value = foundOtp
      }
      otpSuccessMessage.value = result?.message || resPayload?.message || t('auth.otp_sent_msg', 'A 6-digit verification code was sent to your email. Check your inbox and spam folder.')
      startOtpTimer()
      startResendCooldown()
      listenForWebOtp()
      setTimeout(() => {
        otpInputRef.value?.focus()
      }, 150)
    } else {
      handleBackendErrors(result)
    }
  } catch (err) {
    handleBackendErrors(err)
  } finally {
    loading.value = false
  }
}

async function handleVerifyOtp() {
  otpError.value = ''
  errorMessage.value = ''

  const cleanOtp = (otpCode.value || '').replace(/\D/g, '')
  if (cleanOtp.length !== 6) {
    otpError.value = t('auth.enter_valid_otp', 'Please enter a valid 6-digit verification code.')
    return
  }

  loading.value = true
  try {
    const res = await verifyOtp({
      email: form.email.trim(),
      otp: cleanOtp,
    })

    if (res.success) {
      const targetRoute = getDashboardRoute(res.user?.role || form.role)
      router.push(targetRoute)
    } else {
      otpError.value = res.error || t('auth.otp_invalid', 'Invalid or expired verification code.')
    }
  } catch (err) {
    otpError.value = err.message || 'Verification failed. Please try again.'
  } finally {
    loading.value = false
  }
}

async function handleResendOtp(channel = 'email') {
  if (resendCooldown.value > 0 || loading.value) return
  loading.value = true
  otpError.value = ''
  try {
    const rawDigits = form.phone.replace(/[\s\-\(\)]/g, '')
    let cleanPhone = rawDigits
    if (form.countryCode === '+251') {
      const match = rawDigits.match(/([97]\d{8})$/)
      if (match) cleanPhone = match[1]
    } else if (cleanPhone.startsWith('0')) {
      cleanPhone = cleanPhone.slice(1)
    }

    const payload = {
      name: form.fullName.trim(),
      email: form.email.trim(),
      phone: `${form.countryCode}${cleanPhone}`,
      role: form.role,
      password: form.password,
      channel: channel,
    }

    const res = await sendOtp(payload)
    const resPayload = res?.data || res
    const foundOtp = resPayload?.dev_otp || resPayload?.otp || res?.dev_otp || res?.otp
    if (foundOtp) {
      devOtp.value = foundOtp
    }
    otpSuccessMessage.value = res?.message || resPayload?.message || (channel === 'sms'
      ? 'A new 6-digit verification code was sent to your phone via SMS.'
      : t('auth.otp_sent_msg', 'A new 6-digit verification code has been dispatched to your email.'))
    startOtpTimer()
    startResendCooldown()
    listenForWebOtp()
  } catch (err) {
    otpError.value = err.message || 'Failed to resend code.'
  } finally {
    loading.value = false
  }
}

function handleBackendErrors(errPayload) {
  const backendErrors =
    errPayload?.errors ||
    errPayload?.response?.data?.errors ||
    errPayload?.raw?.response?.data?.errors ||
    null

  const message =
    errPayload?.message ||
    errPayload?.error ||
    errPayload?.response?.data?.message ||
    errPayload?.raw?.response?.data?.message ||
    'Registration failed. Please check the highlighted fields.'

  if (backendErrors && typeof backendErrors === 'object') {
    // 1. Email Errors
    if (backendErrors.email && backendErrors.email.length) {
      const emailMsg = backendErrors.email[0]
      if (
        emailMsg.toLowerCase().includes('already') ||
        emailMsg.toLowerCase().includes('taken') ||
        emailMsg.toLowerCase().includes('unique') ||
        emailMsg.toLowerCase().includes('in use')
      ) {
        errors.email = 'Email address is already in use. Try logging in or use a different email.'
      } else {
        errors.email = emailMsg
      }
      emailInputRef.value?.focus()
    }

    // 2. Full Name / Name Errors
    if (backendErrors.name && backendErrors.name.length) {
      errors.fullName = backendErrors.name[0]
    }
    if (backendErrors.fullName && backendErrors.fullName.length) {
      errors.fullName = backendErrors.fullName[0]
    }

    // 3. Phone Number Errors
    if (backendErrors.phone && backendErrors.phone.length) {
      const phoneMsg = backendErrors.phone[0]
      if (
        phoneMsg.toLowerCase().includes('already') ||
        phoneMsg.toLowerCase().includes('taken') ||
        phoneMsg.toLowerCase().includes('unique') ||
        phoneMsg.toLowerCase().includes('in use')
      ) {
        errors.phone = 'Phone number is already registered with another account. Please use a different phone number.'
      } else {
        errors.phone = phoneMsg
      }
    }

    // 4. Password Errors
    if (backendErrors.password && backendErrors.password.length) {
      errors.password = backendErrors.password[0]
    }

    // 5. Confirm Password Errors
    if (backendErrors.password_confirmation && backendErrors.password_confirmation.length) {
      errors.confirmPassword = backendErrors.password_confirmation[0]
    }

    // 6. Role Errors
    if (backendErrors.role && backendErrors.role.length) {
      errors.role = backendErrors.role[0]
    }

    errorMessage.value = message
  } else {
    errorMessage.value = message
  }
}

onMounted(() => {
  window.scrollTo({ top: 0, left: 0, behavior: 'instant' })
  errors.role = ''
  errors.fullName = ''
  errors.email = ''
  errors.phone = ''
  errors.password = ''
  errors.confirmPassword = ''
  errors.agreeToTerms = ''
  errorMessage.value = ''
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>