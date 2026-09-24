<template>
  <section class="py-12 md:py-16 bg-white dark:bg-slate-900 transition-colors" aria-label="Blog preview">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
        <div class="max-w-2xl">
          <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">
            {{ t('nav.blog') }}
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            {{ t('home.recent_articles_title') }}
          </h2>
          <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2">
            {{ t('home.recent_articles_subtitle') }}
          </p>
        </div>

        <RouterLink
          to="/blog"
          class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors group flex-shrink-0"
        >
          <span>{{ t('common.view_all') }}</span>
          <svg class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </RouterLink>
      </div>

      <!-- Loading Skeleton State -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <div
          v-for="i in 3"
          :key="i"
          class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl h-80 animate-pulse flex flex-col justify-between p-4"
        >
          <div class="w-full h-44 bg-slate-200 dark:bg-slate-700 rounded-xl" />
          <div class="space-y-2 mt-4">
            <div class="w-3/4 h-4 bg-slate-200 dark:bg-slate-700 rounded" />
            <div class="w-1/2 h-3 bg-slate-200 dark:bg-slate-700 rounded" />
          </div>
        </div>
      </div>

      <!-- Error State with Retry -->
      <div v-else-if="errorMessage" class="text-center py-10 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm">
        <p class="text-xs text-slate-500 max-w-md mx-auto mb-3">{{ errorMessage }}</p>
        <button
          type="button"
          class="px-4 py-2 text-xs font-semibold rounded-lg bg-emerald-600 text-white"
          @click="fetchBlogPosts"
        >
          {{ t('common.retry') }}
        </button>
      </div>

      <!-- Blog Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <article
          v-for="post in displayPosts"
          :key="post.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group cursor-pointer"
          @click="navigateToPost(post)"
        >
          <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800">
            <img
              :src="post.image_url || post.image || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80'"
              :alt="post.title"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
              loading="lazy"
              decoding="async"
            />
            <span class="absolute top-3 left-3 bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-800 text-[10px] font-bold px-2.5 py-1 rounded-lg backdrop-blur-md shadow-xs">
              {{ post.category || post.blog_category?.name || 'Guide' }}
            </span>
          </div>

          <div class="p-5 sm:p-6 space-y-3 flex-1 flex flex-col justify-between bg-white dark:bg-slate-900">
            <div class="space-y-2">
              <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                <span>{{ formatDate(post.published_at || post.created_at) }}</span>
                <span>&bull;</span>
                <span>{{ post.read_time || '5' }} min read</span>
              </div>

              <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-2 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors duration-150">
                {{ post.title }}
              </h3>

              <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm leading-relaxed line-clamp-2">
                {{ post.excerpt || post.summary || post.body }}
              </p>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 font-semibold">
              <span>{{ t('home.read_more') }}</span>
              <svg class="w-4 h-4 transition-transform duration-150 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </article>
      </div>

      <!-- View all button -->
      <div class="text-center mt-10">
        <RouterLink
          to="/blog"
          class="inline-flex items-center justify-center px-7 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
          </svg>
          {{ t('home.recent_articles_title') }}
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { blogService } from '../../services/blogService'
import { formatDate } from '../../utils/formatters'
import { useLanguage } from '../../composables/useLanguage'

const router = useRouter()
const { t } = useLanguage()

const blogPosts = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

const fallbackBlogPosts = [
  {
    id: 1,
    slug: 'top-neighborhoods-to-buy-in-addis-ababa',
    title: 'Top 5 Neighborhoods for Real Estate Investment in Addis Ababa',
    category: 'Market Trends',
    published_at: '2026-08-10',
    read_time: 4,
    excerpt: 'An in-depth look at emerging investment corridors in Bole, CMC, and Kazanchis with high rental yields.',
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&auto=format&fit=crop&q=80',
  },
  {
    id: 2,
    slug: 'essential-checklist-for-first-time-homebuyers-in-ethiopia',
    title: 'Essential Checklist for First-Time Homebuyers in Ethiopia',
    category: 'Buyer Guide',
    published_at: '2026-08-05',
    read_time: 6,
    excerpt: 'Key legal steps, title deed verification, and financing options you need before buying your first apartment.',
    image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80',
  },
  {
    id: 3,
    slug: 'short-term-rentals-vs-long-term-leases-for-ethiopian-property-owners',
    title: 'Short-Stay Rentals vs Long-Term Leases: What Maximizes Your Income?',
    category: 'Owner Tips',
    published_at: '2026-07-28',
    read_time: 5,
    excerpt: 'Comparing operating expenses, guest occupancy rates, and annual returns for furnished units in Addis Ababa.',
    image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&auto=format&fit=crop&q=80',
  },
]

const displayPosts = computed(() => {
  if (blogPosts.value.length > 0) return blogPosts.value.slice(0, 3)
  return fallbackBlogPosts
})

const fetchBlogPosts = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await blogService.getBlogs({ per_page: 3 })
    if (res && res.success && res.data) {
      if (Array.isArray(res.data)) {
        blogPosts.value = res.data
      } else if (Array.isArray(res.data.data)) {
        blogPosts.value = res.data.data
      } else {
        blogPosts.value = []
      }
    } else if (Array.isArray(res)) {
      blogPosts.value = res
    } else if (res && Array.isArray(res.data)) {
      blogPosts.value = res.data
    } else {
      blogPosts.value = []
    }
  } catch (err) {
    blogPosts.value = []
  } finally {
    isLoading.value = false
  }
}

const navigateToPost = (post) => {
  if (post?.title) {
    router.push({
      path: '/blog',
      query: {
        search: post.title,
        category: post.category || (post.blog_category?.name) || undefined,
      }
    })
  } else {
    router.push({ path: '/blog' })
  }
}

onMounted(() => {
  fetchBlogPosts()
})
</script>
