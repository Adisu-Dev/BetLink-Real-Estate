<template>
  <div class="relative">
    <!-- Main Photo Gallery Container -->
    <div
      class="rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-800"
      :class="[
        imageList.length === 1
          ? 'h-[320px] sm:h-[420px] md:h-[480px]'
          : imageList.length === 2
            ? 'grid grid-cols-1 md:grid-cols-2 gap-2.5 h-[320px] sm:h-[400px] md:h-[450px]'
            : 'grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-2.5 h-[320px] sm:h-[400px] md:h-[460px]'
      ]"
    >
      <!-- Case 1: Single Photo -> Full-Width Hero (NO BLACK RECTANGLE ON SIDE) -->
      <div
        v-if="imageList.length === 1"
        class="w-full h-full relative cursor-pointer overflow-hidden group"
        @click="openLightbox(0)"
      >
        <img
          :src="getImageUrl(0)"
          :alt="title"
          fetchpriority="high"
          loading="eager"
          decoding="async"
          class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500"
          @error="handleImgError"
        />
        <div class="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors" />
      </div>

      <!-- Case 2: 2 Photos -> 50 / 50 Columns -->
      <template v-else-if="imageList.length === 2">
        <div
          v-for="idx in [0, 1]"
          :key="idx"
          class="relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(idx)"
        >
          <img
            :src="getImageUrl(idx)"
            :alt="`${title} - Photo ${idx + 1}`"
            :fetchpriority="idx === 0 ? 'high' : 'auto'"
            :loading="idx === 0 ? 'eager' : 'lazy'"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
          <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors" />
        </div>
      </template>

      <!-- Case 3: 3 Photos -> 1 Large (left 2 cols), 2 Stacked (right 2 cols) -->
      <template v-else-if="imageList.length === 3">
        <div
          class="md:col-span-2 md:row-span-2 relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(0)"
        >
          <img
            :src="getImageUrl(0)"
            :alt="title"
            fetchpriority="high"
            loading="eager"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
        </div>
        <div
          v-for="idx in [1, 2]"
          :key="idx"
          class="hidden md:block md:col-span-2 md:row-span-1 relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(idx)"
        >
          <img
            :src="getImageUrl(idx)"
            :alt="`${title} - Photo ${idx + 1}`"
            loading="lazy"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
        </div>
      </template>

      <!-- Case 4: 4 Photos -> 1 Large (left 2 cols), 3 on right -->
      <template v-else-if="imageList.length === 4">
        <div
          class="md:col-span-2 md:row-span-2 relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(0)"
        >
          <img
            :src="getImageUrl(0)"
            :alt="title"
            fetchpriority="high"
            loading="eager"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
        </div>
        <div
          v-for="idx in [1, 2, 3]"
          :key="idx"
          class="hidden md:block relative cursor-pointer overflow-hidden group h-full"
          :class="idx === 1 ? 'md:col-span-2 md:row-span-1' : 'md:col-span-1 md:row-span-1'"
          @click="openLightbox(idx)"
        >
          <img
            :src="getImageUrl(idx)"
            :alt="`${title} - Photo ${idx + 1}`"
            loading="lazy"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
        </div>
      </template>

      <!-- Case 5: 5 or More Photos -> Classic 5-Photo Grid with +N Badge -->
      <template v-else>
        <!-- Main Featured Image (Large 2x2 on desktop) -->
        <div
          class="md:col-span-2 md:row-span-2 relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(0)"
        >
          <img
            :src="getImageUrl(0)"
            :alt="title"
            fetchpriority="high"
            loading="eager"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
          <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors" />
        </div>

        <div
          v-for="idx in [1, 2, 3]"
          :key="idx"
          class="hidden md:block relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(idx)"
        >
          <img
            :src="getImageUrl(idx)"
            :alt="`${title} - Photo ${idx + 1}`"
            loading="lazy"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
          <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors" />
        </div>

        <!-- 5th Image with +N more overlay if more exist -->
        <div
          class="hidden md:block relative cursor-pointer overflow-hidden group h-full"
          @click="openLightbox(4)"
        >
          <img
            :src="getImageUrl(4)"
            :alt="`${title} - Photo 5`"
            loading="lazy"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            @error="handleImgError"
          />
          <div
            v-if="imageList.length > 5"
            class="absolute inset-0 bg-slate-900/75 backdrop-blur-[2px] flex items-center justify-center text-white group-hover:bg-slate-900/60 transition-colors"
          >
            <div class="text-center">
              <span class="text-lg font-bold block">+{{ imageList.length - 4 }}</span>
              <span class="text-xs text-slate-300">More Photos</span>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- "View All Photos" floating action button -->
    <button
      v-if="imageList.length > 1"
      type="button"
      class="absolute bottom-4 right-4 bg-slate-900/90 hover:bg-slate-900 text-slate-100 text-xs font-semibold px-4 py-2 rounded-xl backdrop-blur-md border border-slate-700/80 shadow-lg flex items-center space-x-2 transition-transform active:scale-95 cursor-pointer z-10"
      @click="openLightbox(0)"
    >
      <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <span>View All {{ imageList.length }} Photos</span>
    </button>

    <!-- Fullscreen Lightbox Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isLightboxOpen"
          class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-md flex flex-col justify-between p-4 sm:p-6"
          @keydown.esc="closeLightbox"
          @keydown.left="prevImage"
          @keydown.right="nextImage"
          tabindex="0"
          ref="lightboxRef"
        >
          <!-- Top bar -->
          <div class="flex items-center justify-between text-slate-300 pb-2">
            <div class="flex items-center space-x-3">
              <span class="text-sm font-semibold text-slate-100">{{ activeIndex + 1 }} / {{ imageList.length }}</span>
              <span class="text-xs text-slate-400 hidden sm:inline">&bull; {{ title }}</span>
            </div>

            <button
              type="button"
              class="p-2 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-200 focus:outline-none transition-colors"
              aria-label="Close photo gallery"
              @click="closeLightbox"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Main Image Display -->
          <div class="relative flex-1 flex items-center justify-center max-h-[75vh] my-auto">
            <!-- Prev Button -->
            <button
              v-if="imageList.length > 1"
              type="button"
              class="absolute left-2 sm:left-6 z-10 p-3 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white backdrop-blur-sm border border-slate-700 shadow-xl transition-transform hover:scale-110"
              aria-label="Previous photo"
              @click="prevImage"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            <img
              :src="getImageUrl(activeIndex)"
              :alt="`${title} - Photo ${activeIndex + 1}`"
              class="max-h-[70vh] max-w-full object-contain rounded-xl shadow-2xl transition-all duration-300"
              @error="handleImgError"
            />

            <!-- Next Button -->
            <button
              v-if="imageList.length > 1"
              type="button"
              class="absolute right-2 sm:right-6 z-10 p-3 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white backdrop-blur-sm border border-slate-700 shadow-xl transition-transform hover:scale-110"
              aria-label="Next photo"
              @click="nextImage"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Thumbnails carousel -->
          <div v-if="imageList.length > 1" class="pt-4 flex items-center justify-center space-x-2 overflow-x-auto py-2">
            <button
              v-for="(img, idx) in imageList"
              :key="idx"
              type="button"
              :class="[
                'w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 border-2 transition-all',
                idx === activeIndex
                  ? 'border-emerald-500 scale-105 opacity-100 shadow-md'
                  : 'border-transparent opacity-50 hover:opacity-80'
              ]"
              @click="activeIndex = idx"
            >
              <img :src="getImageUrl(idx)" class="w-full h-full object-cover" @error="handleImgError" />
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

const props = defineProps({
  images: {
    type: Array,
    default: () => [],
  },
  title: {
    type: String,
    default: 'Property',
  },
})

const defaultImage = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1200&q=80'

const imageList = computed(() => {
  if (!props.images || props.images.length === 0) {
    return [defaultImage]
  }
  return props.images
})

const isLightboxOpen = ref(false)
const activeIndex = ref(0)
const lightboxRef = ref(null)

const getImageUrl = (index) => {
  const item = imageList.value[index]
  if (!item) return defaultImage
  let url = typeof item === 'string' ? item : (item.image_url || item.url || defaultImage)
  if (url && typeof url === 'string' && url.includes('images.unsplash.com')) {
    url = url.replace(/w=\d+/, 'w=800').replace(/q=\d+/, 'q=75')
    if (!url.includes('auto=format')) url += '&auto=format'
  }
  return url
}

const handleImgError = (e) => {
  e.target.src = defaultImage
}

const openLightbox = (index = 0) => {
  activeIndex.value = index
  isLightboxOpen.value = true
  nextTick(() => {
    lightboxRef.value?.focus()
  })
}

const closeLightbox = () => {
  isLightboxOpen.value = false
}

const nextImage = () => {
  activeIndex.value = (activeIndex.value + 1) % imageList.value.length
}

const prevImage = () => {
  activeIndex.value = (activeIndex.value - 1 + imageList.value.length) % imageList.value.length
}
</script>
