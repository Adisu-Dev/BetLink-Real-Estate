<template>
  <div class="relative inline-block text-left" ref="dropdownRef">
    <!-- Trigger Button -->
    <button
      type="button"
      @click="isOpen = !isOpen"
      :aria-expanded="isOpen"
      aria-haspopup="listbox"
      class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none whitespace-nowrap cursor-pointer select-none"
    >
      <!-- Rounded Flag Icon -->
      <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden shrink-0 ring-1 ring-slate-900/10 dark:ring-white/20 shadow-2xs">
        <!-- SVG Flag based on country code -->
        <svg v-if="currentLanguageObject.flagCode === 'us'" class="w-full h-full object-cover" viewBox="0 0 512 512" aria-hidden="true">
          <mask id="btn-us-mask">
            <circle cx="256" cy="256" r="256" fill="#fff"/>
          </mask>
          <g mask="url(#btn-us-mask)">
            <rect width="512" height="512" fill="#b22234"/>
            <path stroke="#fff" stroke-width="39.38" d="M0 59.08h512M0 137.84h512M0 216.62h512M0 295.38h512M0 374.15h512M0 452.92h512"/>
            <rect width="210" height="276" fill="#3c3b6e"/>
            <g fill="#fff">
              <circle cx="35" cy="35" r="9"/><circle cx="85" cy="35" r="9"/><circle cx="135" cy="35" r="9"/><circle cx="185" cy="35" r="9"/>
              <circle cx="60" cy="70" r="9"/><circle cx="110" cy="70" r="9"/><circle cx="160" cy="70" r="9"/>
              <circle cx="35" cy="105" r="9"/><circle cx="85" cy="105" r="9"/><circle cx="135" cy="105" r="9"/><circle cx="185" cy="105" r="9"/>
              <circle cx="60" cy="140" r="9"/><circle cx="110" cy="140" r="9"/><circle cx="160" cy="140" r="9"/>
              <circle cx="35" cy="175" r="9"/><circle cx="85" cy="175" r="9"/><circle cx="135" cy="175" r="9"/><circle cx="185" cy="175" r="9"/>
              <circle cx="60" cy="210" r="9"/><circle cx="110" cy="210" r="9"/><circle cx="160" cy="210" r="9"/>
              <circle cx="35" cy="245" r="9"/><circle cx="85" cy="245" r="9"/><circle cx="135" cy="245" r="9"/><circle cx="185" cy="245" r="9"/>
            </g>
          </g>
        </svg>

        <svg v-else-if="currentLanguageObject.flagCode === 'et'" class="w-full h-full object-cover" viewBox="0 0 512 512" aria-hidden="true">
          <mask id="btn-et-mask">
            <circle cx="256" cy="256" r="256" fill="#fff"/>
          </mask>
          <g mask="url(#btn-et-mask)">
            <rect width="512" height="170.67" fill="#078930"/>
            <rect y="170.67" width="512" height="170.67" fill="#fcdd09"/>
            <rect y="341.33" width="512" height="170.67" fill="#da121a"/>
            <circle cx="256" cy="256" r="92" fill="#0f47af"/>
            <polygon points="256,188 266,234 314,234 275,262 290,308 256,280 222,308 237,262 198,234 246,234" fill="#fcdd09"/>
            <circle cx="256" cy="256" r="14" fill="#0f47af"/>
          </g>
        </svg>

        <span v-else class="text-xs">{{ currentLanguageObject.flag }}</span>
      </span>

      <!-- Full Native Language Name -->
      <span class="font-medium text-slate-800 dark:text-slate-100 truncate max-w-[100px] sm:max-w-[130px]">
        {{ currentLanguageObject.nativeName }}
      </span>

      <!-- Chevron Down Icon -->
      <ChevronDown
        class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
        :class="{ 'rotate-180 text-slate-800 dark:text-slate-200': isOpen }"
        aria-hidden="true"
      />
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition ease-out duration-150 transform"
      :enter-from-class="direction === 'up' ? 'opacity-0 translate-y-1 scale-95' : 'opacity-0 -translate-y-1 scale-95'"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-100 transform"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      :leave-to-class="direction === 'up' ? 'opacity-0 translate-y-1 scale-95' : 'opacity-0 -translate-y-1 scale-95'"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 w-52 sm:w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 py-1.5 z-50 overflow-hidden focus:outline-none"
        :class="direction === 'up' ? 'bottom-full mb-2' : 'top-full mt-2'"
        role="listbox"
      >
        <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 mb-1">
          Select Language
        </div>

        <button
          v-for="lang in languages"
          :key="lang.code"
          type="button"
          role="option"
          :aria-selected="currentLang === lang.code"
          @click="selectLanguage(lang.code)"
          :class="[
            'w-full flex items-center justify-between px-3.5 py-2.5 text-xs sm:text-sm font-medium transition-colors text-left cursor-pointer',
            currentLang === lang.code
              ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold'
              : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'
          ]"
        >
          <div class="flex items-center gap-2.5 min-w-0">
            <!-- Rounded Flag Icon in Option -->
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden shrink-0 ring-1 ring-slate-900/10 dark:ring-white/20 shadow-2xs">
              <svg v-if="lang.flagCode === 'us'" class="w-full h-full object-cover" viewBox="0 0 512 512" aria-hidden="true">
                <mask :id="`opt-us-mask-${lang.code}`">
                  <circle cx="256" cy="256" r="256" fill="#fff"/>
                </mask>
                <g :mask="`url(#opt-us-mask-${lang.code})`">
                  <rect width="512" height="512" fill="#b22234"/>
                  <path stroke="#fff" stroke-width="39.38" d="M0 59.08h512M0 137.84h512M0 216.62h512M0 295.38h512M0 374.15h512M0 452.92h512"/>
                  <rect width="210" height="276" fill="#3c3b6e"/>
                  <g fill="#fff">
                    <circle cx="35" cy="35" r="9"/><circle cx="85" cy="35" r="9"/><circle cx="135" cy="35" r="9"/><circle cx="185" cy="35" r="9"/>
                    <circle cx="60" cy="70" r="9"/><circle cx="110" cy="70" r="9"/><circle cx="160" cy="70" r="9"/>
                    <circle cx="35" cy="105" r="9"/><circle cx="85" cy="105" r="9"/><circle cx="135" cy="105" r="9"/><circle cx="185" cy="105" r="9"/>
                    <circle cx="60" cy="140" r="9"/><circle cx="110" cy="140" r="9"/><circle cx="160" cy="140" r="9"/>
                    <circle cx="35" cy="175" r="9"/><circle cx="85" cy="175" r="9"/><circle cx="135" cy="175" r="9"/><circle cx="185" cy="175" r="9"/>
                    <circle cx="60" cy="210" r="9"/><circle cx="110" cy="210" r="9"/><circle cx="160" cy="210" r="9"/>
                    <circle cx="35" cy="245" r="9"/><circle cx="85" cy="245" r="9"/><circle cx="135" cy="245" r="9"/><circle cx="185" cy="245" r="9"/>
                  </g>
                </g>
              </svg>

              <svg v-else-if="lang.flagCode === 'et'" class="w-full h-full object-cover" viewBox="0 0 512 512" aria-hidden="true">
                <mask :id="`opt-et-mask-${lang.code}`">
                  <circle cx="256" cy="256" r="256" fill="#fff"/>
                </mask>
                <g :mask="`url(#opt-et-mask-${lang.code})`">
                  <rect width="512" height="170.67" fill="#078930"/>
                  <rect y="170.67" width="512" height="170.67" fill="#fcdd09"/>
                  <rect y="341.33" width="512" height="170.67" fill="#da121a"/>
                  <circle cx="256" cy="256" r="92" fill="#0f47af"/>
                  <polygon points="256,188 266,234 314,234 275,262 290,308 256,280 222,308 237,262 198,234 246,234" fill="#fcdd09"/>
                  <circle cx="256" cy="256" r="14" fill="#0f47af"/>
                </g>
              </svg>

              <span v-else class="text-xs">{{ lang.flag }}</span>
            </span>

            <!-- Language Native Name -->
            <span class="truncate">{{ lang.nativeName }}</span>
          </div>

          <!-- Active Checkmark -->
          <Check
            v-if="currentLang === lang.code"
            class="w-4 h-4 text-slate-800 dark:text-slate-200 shrink-0 ml-2"
            aria-hidden="true"
          />
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { ChevronDown, Check } from 'lucide-vue-next'
import { useLanguage } from '../../composables/useLanguage'

const props = defineProps({
  direction: {
    type: String,
    default: 'down' // 'up' | 'down'
  }
})

const { currentLang, currentLanguageObject, languages, setLanguage } = useLanguage()

const isOpen = ref(false)
const dropdownRef = ref(null)

function selectLanguage(code) {
  setLanguage(code)
  isOpen.value = false
}

function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
