import { ref, computed } from 'vue'
import { useHttp } from './useHttp'

let searchTimeout = null

export function useSearch() {
  const { get } = useHttp()
  const query = ref('')
  const results = ref([])
  const isSearching = ref(false)

  // Debounced search function
  const search = async (searchQuery) => {
    if (searchTimeout) clearTimeout(searchTimeout)

    query.value = searchQuery

    if (!searchQuery || searchQuery.length < 2) {
      results.value = []
      return
    }

    searchTimeout = setTimeout(async () => {
      await performSearch(searchQuery)
    }, 300) // 300ms debounce
  }

  // Perform the actual search
  const performSearch = async (searchQuery) => {
    isSearching.value = true
    try {
      const result = await get(`/search?q=${encodeURIComponent(searchQuery)}`)
      if (result.success) {
        results.value = result.data || []
      } else {
        results.value = []
      }
    } catch (error) {
      console.error('Search failed:', error)
      results.value = []
    } finally {
      isSearching.value = false
    }
  }

  // Group results by type
  const groupedResults = computed(() => {
    const groups = {
      properties: [],
      users: [],
      appointments: [],
      messages: []
    }

    results.value.forEach(result => {
      if (result.type === 'property') {
        groups.properties.push(result)
      } else if (result.type === 'user') {
        groups.users.push(result)
      } else if (result.type === 'appointment') {
        groups.appointments.push(result)
      } else if (result.type === 'message') {
        groups.messages.push(result)
      }
    })

    return groups
  })

  // Get display label for result type
  const getResultLabel = (result) => {
    switch (result.type) {
      case 'property':
        return result.title
      case 'user':
        return `${result.first_name} ${result.last_name}`
      case 'appointment':
        return `${result.property_title} - ${result.appointment_type}`
      case 'message':
        return result.subject || result.content?.substring(0, 50)
      default:
        return result.title || result.name
    }
  }

  // Get result icon
  const getResultIcon = (type) => {
    const icons = {
      property: '🏠',
      user: '👤',
      appointment: '📅',
      message: '📧'
    }
    return icons[type] || '📋'
  }

  const clearSearch = () => {
    query.value = ''
    results.value = []
  }

  return {
    query,
    results,
    isSearching,
    groupedResults,
    search,
    getResultLabel,
    getResultIcon,
    clearSearch
  }
}
