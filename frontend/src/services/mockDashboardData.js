// Mock data for dashboard pages during development
// This will be replaced with real API calls when backend is ready

export const mockDashboardData = {
  // Dashboard Stats
  stats: {
    buyer: [
      { label: 'Properties Viewed', value: '24', icon: 'eye', trend: '+12%' },
      { label: 'Favorites', value: '8', icon: 'heart', trend: '+3' },
      { label: 'Appointments', value: '3', icon: 'calendar', trend: '-1' },
      { label: 'Messages', value: '12', icon: 'message', trend: '+5' }
    ],
    owner: [
      { label: 'Active Properties', value: '12', icon: 'home', trend: '+2' },
      { label: 'Total Inquiries', value: '47', icon: 'envelope', trend: '+8' },
      { label: 'Appointments', value: '6', icon: 'calendar', trend: '+1' },
      { label: 'Revenue', value: '$24,500', icon: 'currency', trend: '+15%' }
    ],
    agent: [
      { label: 'Active Listings', value: '18', icon: 'home', trend: '+5' },
      { label: 'Leads', value: '32', icon: 'users', trend: '+12' },
      { label: 'Appointments', value: '9', icon: 'calendar', trend: '+3' },
      { label: 'Sales', value: '5', icon: 'trending-up', trend: '+2' }
    ],
    admin: [
      { label: 'Total Users', value: '1,240', icon: 'users', trend: '+45' },
      { label: 'Active Properties', value: '856', icon: 'home', trend: '+23' },
      { label: 'Pending Verifications', value: '34', icon: 'shield', trend: '+8' },
      { label: 'Reports', value: '12', icon: 'alert', trend: '+3' }
    ]
  },

  // Properties
  properties: [
    {
      id: 1,
      title: 'Modern Apartment Downtown',
      price: 4500000,
      currency: 'ETB',
      listing_type: 'sale',
      listingType: 'sale',
      type: 'Apartment',
      category: 'Apartment',
      location: 'Bole Atlas, Addis Ababa',
      bedrooms: 3,
      beds: 3,
      bathrooms: 2,
      baths: 2,
      area: 140,
      areaUnit: 'sqm',
      sqft: 140,
      status: 'active',
      is_featured: true,
      is_verified: true,
      is_favorite: true,
      isFavorite: true,
      image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
      owner: {
        id: 1,
        name: 'Abebe Kebede',
        role: 'Verified Owner',
        is_verified: true,
        phone: '+251 91 123 4567',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80'
      },
      views: 234,
      favorites: 12
    },
    {
      id: 2,
      title: 'Luxury Villa with Pool',
      price: 18500000,
      currency: 'ETB',
      listing_type: 'sale',
      listingType: 'sale',
      type: 'Villa',
      category: 'Villa',
      location: 'CMC, Addis Ababa',
      bedrooms: 5,
      beds: 5,
      bathrooms: 4,
      baths: 4,
      area: 450,
      areaUnit: 'sqm',
      sqft: 450,
      status: 'active',
      is_featured: true,
      is_verified: true,
      is_favorite: true,
      isFavorite: true,
      image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600&q=80',
      owner: {
        id: 2,
        name: 'Sara Mohammed',
        role: 'Verified Landlord',
        is_verified: true,
        phone: '+251 92 345 6789',
        avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=100&q=80'
      },
      views: 567,
      favorites: 45
    },
    {
      id: 3,
      title: 'Cozy Studio Apartment',
      price: 35000,
      currency: 'ETB',
      listing_type: 'rent',
      listingType: 'rent',
      type: 'Apartment',
      category: 'Apartment',
      location: 'Kazanchis, Addis Ababa',
      bedrooms: 1,
      beds: 1,
      bathrooms: 1,
      baths: 1,
      area: 55,
      areaUnit: 'sqm',
      sqft: 55,
      status: 'active',
      is_featured: false,
      is_verified: true,
      is_favorite: true,
      isFavorite: true,
      image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80',
      owner: {
        id: 3,
        name: 'Almaz Tadesse',
        role: 'Verified Owner',
        is_verified: true,
        phone: '+251 93 456 7890',
        avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&q=80'
      },
      views: 89,
      favorites: 5
    },
    {
      id: 4,
      title: 'Commercial Office Space',
      price: 85000,
      currency: 'ETB',
      listing_type: 'rent',
      listingType: 'rent',
      type: 'Commercial',
      category: 'Commercial',
      location: 'Mexico Square, Addis Ababa',
      bedrooms: 0,
      beds: 0,
      bathrooms: 2,
      baths: 2,
      area: 220,
      areaUnit: 'sqm',
      sqft: 220,
      status: 'active',
      is_featured: false,
      is_verified: true,
      is_favorite: false,
      isFavorite: false,
      image: 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=600&q=80',
      owner: {
        id: 4,
        name: 'Haile Properties',
        role: 'Commercial Broker',
        is_verified: true,
        phone: '+251 94 567 8901',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80'
      },
      views: 156,
      favorites: 8
    },
    {
      id: 5,
      title: 'Executive Penthouse Suite',
      price: 120000,
      currency: 'ETB',
      listing_type: 'rent',
      listingType: 'rent',
      type: 'Apartment',
      category: 'Apartment',
      location: 'Sarbet, Addis Ababa',
      bedrooms: 4,
      beds: 4,
      bathrooms: 3,
      baths: 3,
      area: 310,
      areaUnit: 'sqm',
      sqft: 310,
      status: 'active',
      is_featured: true,
      is_verified: true,
      is_favorite: true,
      isFavorite: true,
      image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&q=80',
      owner: {
        id: 5,
        name: 'Dawit Mengistu',
        role: 'Verified Landlord',
        is_verified: true,
        phone: '+251 95 678 9012',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80'
      },
      views: 412,
      favorites: 31
    },
    {
      id: 6,
      title: 'Furnished Short-Stay Studio',
      price: 3500,
      currency: 'ETB',
      listing_type: 'short_rent',
      listingType: 'short_rent',
      type: 'Condo',
      category: 'Condo',
      location: 'Old Airport, Addis Ababa',
      bedrooms: 1,
      beds: 1,
      bathrooms: 1,
      baths: 1,
      area: 60,
      areaUnit: 'sqm',
      sqft: 60,
      status: 'active',
      is_featured: false,
      is_verified: true,
      is_favorite: false,
      isFavorite: false,
      image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80',
      owner: {
        id: 6,
        name: 'Selamawit Bekele',
        role: 'Superhost',
        is_verified: true,
        phone: '+251 96 789 0123',
        avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&q=80'
      },
      views: 290,
      favorites: 19
    },
    {
      id: 7,
      title: 'Bole Medhanealem Modern Condo',
      price: 6800000,
      currency: 'ETB',
      listing_type: 'sale',
      listingType: 'sale',
      type: 'Condominium',
      category: 'Condominium',
      location: 'Bole Medhanealem, Addis Ababa',
      bedrooms: 2,
      beds: 2,
      bathrooms: 2,
      baths: 2,
      area: 110,
      areaUnit: 'sqm',
      sqft: 110,
      status: 'active',
      is_featured: true,
      is_verified: true,
      is_favorite: false,
      isFavorite: false,
      image: 'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=600&q=80',
      owner: {
        id: 7,
        name: 'Kassahun Worku',
        role: 'Verified Lister',
        is_verified: true,
        phone: '+251 97 890 1234',
        avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80'
      },
      views: 340,
      favorites: 24
    },
    {
      id: 8,
      title: 'Hawassa Lakeview Vacation Villa',
      price: 5000,
      currency: 'ETB',
      listing_type: 'short_rent',
      listingType: 'short_rent',
      type: 'Villa',
      category: 'Villa',
      location: 'Hawassa Lake Shore, Hawassa',
      bedrooms: 4,
      beds: 4,
      bathrooms: 3,
      baths: 3,
      area: 280,
      areaUnit: 'sqm',
      sqft: 280,
      status: 'active',
      is_featured: true,
      is_verified: true,
      is_favorite: true,
      isFavorite: true,
      image: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=600&q=80',
      owner: {
        id: 8,
        name: 'Tigist Alemayehu',
        role: 'Superhost',
        is_verified: true,
        phone: '+251 98 901 2345',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80'
      },
      views: 512,
      favorites: 48
    }
  ],

  // Appointments
  appointments: [
    {
      id: 1,
      propertyTitle: 'Modern Apartment Downtown',
      date: '2024-01-20',
      time: '10:00 AM',
      status: 'scheduled',
      attendees: 'John Doe, You',
      type: 'viewing'
    },
    {
      id: 2,
      propertyTitle: 'Luxury Villa with Pool',
      date: '2024-01-22',
      time: '2:30 PM',
      status: 'scheduled',
      attendees: 'Jane Smith, You',
      type: 'consultation'
    },
    {
      id: 3,
      propertyTitle: 'Cozy Studio Apartment',
      date: '2024-01-18',
      time: '9:00 AM',
      status: 'completed',
      attendees: 'Mike Johnson, You',
      type: 'viewing'
    }
  ],

  // Messages
  conversations: [
    {
      id: 1,
      name: 'John Doe',
      avatar: 'https://ui-avatars.com/api/?name=John+Doe',
      lastMessage: 'Great! See you tomorrow at 10 AM',
      unread: 2,
      date: '5 mins ago',
      online: true
    },
    {
      id: 2,
      name: 'Jane Smith',
      avatar: 'https://ui-avatars.com/api/?name=Jane+Smith',
      lastMessage: 'Can we schedule an appointment for next week?',
      unread: 0,
      date: '30 mins ago',
      online: true
    },
    {
      id: 3,
      name: 'Mike Johnson',
      avatar: 'https://ui-avatars.com/api/?name=Mike+Johnson',
      lastMessage: 'Thank you for viewing the property',
      unread: 0,
      date: '2 hours ago',
      online: false
    }
  ],

  messages: [
    {
      id: 1,
      sender: 'John Doe',
      message: 'Hi, I\'m interested in the apartment downtown. Can we schedule a viewing?',
      timestamp: '9:30 AM',
      direction: 'incoming'
    },
    {
      id: 2,
      sender: 'You',
      message: 'Sure! I\'m available tomorrow at 10 AM if that works for you.',
      timestamp: '9:35 AM',
      direction: 'outgoing'
    },
    {
      id: 3,
      sender: 'John Doe',
      message: 'Great! See you tomorrow at 10 AM',
      timestamp: '9:40 AM',
      direction: 'incoming'
    }
  ],

  // Reviews
  reviews: [
    {
      id: 1,
      property: 'Modern Apartment Downtown',
      author: 'Jane Doe',
      rating: 5,
      comment: 'Excellent property! Clean, well-maintained, and great location.',
      date: '2024-01-15',
      helpful: 8
    },
    {
      id: 2,
      property: 'Luxury Villa with Pool',
      author: 'Mike Smith',
      rating: 4,
      comment: 'Beautiful property with amazing views. The agent was very helpful.',
      date: '2024-01-10',
      helpful: 5
    }
  ],

  // Users
  users: [
    {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      role: 'buyer',
      status: 'active',
      joined: '2023-06-15',
      properties: 0
    },
    {
      id: 2,
      name: 'Jane Smith',
      email: 'jane@example.com',
      role: 'owner',
      status: 'active',
      joined: '2023-05-20',
      properties: 3
    },
    {
      id: 3,
      name: 'Mike Johnson',
      email: 'mike@example.com',
      role: 'agent',
      status: 'verified',
      joined: '2023-04-10',
      properties: 12
    },
    {
      id: 4,
      name: 'Sarah Wilson',
      email: 'sarah@example.com',
      role: 'buyer',
      status: 'pending',
      joined: '2024-01-05',
      properties: 0
    }
  ],

  // Reports
  reports: [
    {
      id: 1,
      title: 'Inappropriate Property Listing',
      type: 'content',
      reporter: 'John Doe',
      status: 'pending',
      date: '2024-01-18',
      property: 'Modern Apartment'
    },
    {
      id: 2,
      title: 'User Misconduct',
      type: 'user',
      reporter: 'Jane Smith',
      status: 'resolved',
      date: '2024-01-10',
      property: 'N/A'
    }
  ],

  // Analytics
  analytics: {
    views: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      data: [120, 150, 100, 200, 180, 140, 160]
    },
    revenue: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      data: [45000, 52000, 48000, 61000, 55000, 67000]
    },
    properties: {
      labels: ['Active', 'Pending', 'Sold', 'Expired'],
      data: [45, 12, 28, 15]
    }
  },

  // Verification Requests
  verifications: [
    {
      id: 1,
      name: 'John Doe',
      type: 'id',
      status: 'pending',
      submitted: '2024-01-15',
      documents: ['id-front.jpg', 'id-back.jpg']
    },
    {
      id: 2,
      name: 'Jane Smith',
      type: 'property',
      status: 'approved',
      submitted: '2024-01-10',
      documents: ['deed.pdf']
    }
  ],

  // Leads
  leads: [
    {
      id: 1,
      name: 'Sarah Wilson',
      phone: '+1 234 567 8901',
      email: 'sarah@example.com',
      interest: 'Modern Apartment Downtown',
      budget: '$400,000 - $500,000',
      status: 'active',
      date: '2024-01-18'
    },
    {
      id: 2,
      name: 'Robert Brown',
      phone: '+1 456 789 0123',
      email: 'robert@example.com',
      interest: 'Luxury Villa',
      budget: '$800,000 - $1,000,000',
      status: 'follow-up',
      date: '2024-01-15'
    }
  ],

  // Inquiries
  inquiries: [
    {
      id: 1,
      from: 'John Smith',
      property: 'Modern Apartment Downtown',
      message: 'Is the price negotiable?',
      date: '2024-01-18',
      status: 'pending'
    },
    {
      id: 2,
      from: 'Emma Wilson',
      property: 'Luxury Villa with Pool',
      message: 'When is the earliest viewing available?',
      date: '2024-01-17',
      status: 'responded'
    }
  ]
}

// Mock search function
export function searchProperties(query) {
  return mockDashboardData.properties.filter(p =>
    p.title.toLowerCase().includes(query.toLowerCase()) ||
    p.location.toLowerCase().includes(query.toLowerCase())
  )
}

// Mock filter function
export function filterProperties(filters) {
  return mockDashboardData.properties.filter(p => {
    if (filters.status && p.status !== filters.status) return false
    if (filters.minPrice && p.price < filters.minPrice) return false
    if (filters.maxPrice && p.price > filters.maxPrice) return false
    if (filters.beds && p.beds !== filters.beds) return false
    return true
  })
}

// Mock sort function
export function sortProperties(properties, sortBy = 'date') {
  const sorted = [...properties]
  if (sortBy === 'price-asc') sorted.sort((a, b) => a.price - b.price)
  if (sortBy === 'price-desc') sorted.sort((a, b) => b.price - a.price)
  if (sortBy === 'views') sorted.sort((a, b) => b.views - a.views)
  return sorted
}

export default mockDashboardData
