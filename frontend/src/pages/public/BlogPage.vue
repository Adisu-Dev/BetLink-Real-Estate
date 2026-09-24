<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 lg:py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
      <!-- Back Navigation Bar -->
      <div class="flex items-center gap-3">
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
      <div class="text-center max-w-2xl mx-auto">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Insights & Resources</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1 mb-2">BetLink Real Estate Blog</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Stay informed with the latest property market insights, investment tips, and neighborhood guides across Ethiopia.</p>
      </div>

      <!-- Search and Categories -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 space-y-4 shadow-xs">
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="flex-1 relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search articles, guides, and trends..."
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 transition-colors"
              @keyup.enter="handleSearch"
            />
          </div>
          <button
            type="button"
            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition-colors shadow-xs cursor-pointer"
            @click="handleSearch"
          >
            Search
          </button>
        </div>

        <!-- Categories -->
        <div v-if="categories.length > 0" class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer',
              selectedCategory === ''
                ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900'
                : 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
            ]"
            @click="setCategory('')"
          >
            All Articles
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id || cat.slug || cat"
            type="button"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer',
              selectedCategory === (cat.slug || cat.name || cat)
                ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900'
                : 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
            ]"
            @click="setCategory(cat.slug || cat.name || cat)"
          >
            {{ cat.name || cat }}
          </button>
        </div>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl h-72 animate-pulse p-4" />
      </div>

      <!-- Articles Grid -->
      <div v-else-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <article
          v-for="article in articles"
          :key="article.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 rounded-2xl overflow-hidden shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between"
        >
          <div class="aspect-[16/9] overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
            <img
              :src="article.image_url || article.image || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'"
              :alt="article.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <span v-if="article.category" class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-[10px] font-bold shadow-xs">
              {{ article.category?.name || article.category }}
            </span>
          </div>

          <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
            <div>
              <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-1">{{ formatDate(article.published_at || article.created_at) }}</p>
              <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white line-clamp-2 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                {{ article.title }}
              </h2>
              <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 mt-2 leading-relaxed">
                {{ article.excerpt || article.summary || article.body }}
              </p>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-900 dark:text-white font-bold">
              <span>Read article</span>
              <span>&rarr;</span>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty state -->
      <div v-else class="text-center py-16 px-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-xs">
        <p class="text-slate-600 dark:text-slate-400 text-sm">No blog posts found matching your search.</p>
        <button
          type="button"
          @click="resetFilters"
          class="mt-3 inline-flex items-center text-xs font-bold text-slate-900 dark:text-white hover:underline cursor-pointer"
        >
          Clear filters & view all &rarr;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { blogService } from '../../services/blogService'
import { formatDate } from '../../utils/formatters'

const route = useRoute()
const router = useRouter()

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const articles = ref([])
const categories = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const isLoading = ref(true)

const fallbackBlogPosts = [
  {
    id: 1,
    title: 'Top 5 Neighborhoods for Real Estate Investment in Addis Ababa',
    category: 'Market Trends',
    published_at: '2026-08-10',
    excerpt: 'An in-depth look at emerging investment corridors in Bole, CMC, and Kazanchis with high rental yields.',
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&auto=format&fit=crop&q=80',
  },
  {
    id: 2,
    title: 'Essential Checklist for First-Time Homebuyers in Ethiopia',
    category: 'Buying Guides',
    published_at: '2026-08-05',
    excerpt: 'Key legal steps, title deed verification, and financing options you need before buying your first apartment.',
    image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80',
  },
  {
    id: 3,
    title: 'Short-Stay Rentals vs Long-Term Leases: What Maximizes Your Income?',
    category: 'Market Trends',
    published_at: '2026-07-28',
    excerpt: 'Comparing operating expenses, guest occupancy rates, and annual returns for furnished units in Addis Ababa.',
    image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&auto=format&fit=crop&q=80',
  },
  {
    id: 4,
    title: 'Navigating Land and Property Registration Laws in 2026',
    category: 'Legal Tips',
    published_at: '2026-07-15',
    excerpt: 'Understanding the newest city administration requirements for transfers and digital title deed registrations.',
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&auto=format&fit=crop&q=80',
  }
]

const initFromRoute = () => {
  if (route.query.search || route.query.q) {
    searchQuery.value = route.query.search || route.query.q || ''
  }
  if (route.query.category) {
    selectedCategory.value = route.query.category
  }
}

const handleSearch = () => {
  router.push({
    path: '/blog',
    query: {
      search: searchQuery.value || undefined,
      category: selectedCategory.value || undefined,
    }
  })
  fetchBlogs()
}

const setCategory = (cat) => {
  selectedCategory.value = cat
  handleSearch()
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  router.push({ path: '/blog' })
  fetchBlogs()
}

const fetchCategories = async () => {
  try {
    const res = await blogService.getCategories()
    if (res && res.success && Array.isArray(res.data)) {
      categories.value = res.data
    } else if (Array.isArray(res)) {
      categories.value = res
    }
  } catch {
    categories.value = ['Market Trends', 'Buying Guides', 'Legal Tips', 'Interior Design', 'Addis Ababa Living']
  }
}

const fetchBlogs = async () => {
  isLoading.value = true
  try {
    const params = {}
    if (searchQuery.value.trim()) params.q = searchQuery.value.trim()
    if (selectedCategory.value) params.category = selectedCategory.value

    const res = await blogService.getBlogs(params)
    let list = []
    if (res && res.success && res.data) {
      list = Array.isArray(res.data) ? res.data : res.data.data || []
    } else if (Array.isArray(res)) {
      list = res
    }

    if (list.length === 0) {
      let filtered = [...fallbackBlogPosts]
      if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim()
        filtered = filtered.filter(a => a.title.toLowerCase().includes(q) || a.excerpt.toLowerCase().includes(q))
      }
      if (selectedCategory.value) {
        filtered = filtered.filter(a => a.category.toLowerCase() === selectedCategory.value.toLowerCase())
      }
      articles.value = filtered
    } else {
      articles.value = list
    }
  } catch {
    let filtered = [...fallbackBlogPosts]
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      filtered = filtered.filter(a => a.title.toLowerCase().includes(q) || a.excerpt.toLowerCase().includes(q))
    }
    if (selectedCategory.value) {
      filtered = filtered.filter(a => a.category.toLowerCase() === selectedCategory.value.toLowerCase())
    }
    articles.value = filtered
  } finally {
    isLoading.value = false
  }
}

watch(() => route.query, () => {
  initFromRoute()
  fetchBlogs()
})

onMounted(async () => {
  initFromRoute()
  await fetchCategories()
  await fetchBlogs()
})
</script>