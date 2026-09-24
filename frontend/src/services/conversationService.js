import api, { cachedGet, clearApiCache } from './api'
import { ENDPOINTS } from '../config/api'

const STORAGE_KEY = 'betlink_conversations'

const initialConversations = [
  {
    id: 1,
    property_id: 1,
    property: {
      id: 1,
      title: 'Modern Apartment Downtown',
      price: 4500000,
      location: 'Bole Atlas, Addis Ababa',
      image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80'
    },
    other_user: {
      id: 1,
      name: 'Abebe Kebede',
      role: 'Property Owner',
      avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80',
      online: true
    },
    unread_count: 0,
    updated_at: new Date(Date.now() - 3600000 * 1).toISOString(),
    messages: [
      {
        id: 101,
        sender_id: 1,
        sender_name: 'Abebe Kebede',
        body: 'Hello! Thanks for your interest in the Bole Atlas apartment. Are you available for a tour this weekend?',
        created_at: new Date(Date.now() - 3600000 * 5).toISOString(),
        is_read: true,
        type: 'text'
      },
      {
        id: 102,
        sender_id: 999,
        sender_name: 'You',
        body: 'Hi Abebe! Yes, Saturday around 10:00 AM would work perfectly for me. Is the title deed ready for verification?',
        created_at: new Date(Date.now() - 3600000 * 3).toISOString(),
        is_read: true,
        type: 'text'
      },
      {
        id: 103,
        sender_id: 1,
        sender_name: 'Abebe Kebede',
        body: 'Yes, full digital and physical documentation is ready. I look forward to meeting you!',
        created_at: new Date(Date.now() - 3600000 * 1).toISOString(),
        is_read: true,
        type: 'text'
      }
    ]
  },
  {
    id: 2,
    property_id: 2,
    property: {
      id: 2,
      title: 'Luxury Villa with Pool',
      price: 18500000,
      location: 'CMC, Addis Ababa',
      image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600&q=80'
    },
    other_user: {
      id: 2,
      name: 'Sara Mohammed',
      role: 'Landlord',
      avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=100&q=80',
      online: false
    },
    unread_count: 0,
    updated_at: new Date(Date.now() - 86400000).toISOString(),
    messages: [
      {
        id: 201,
        sender_id: 999,
        sender_name: 'You',
        body: 'Good morning Sara, is the luxury villa price negotiable for cash buyers?',
        created_at: new Date(Date.now() - 86400000 * 1.5).toISOString(),
        is_read: true,
        type: 'text'
      },
      {
        id: 202,
        sender_id: 2,
        sender_name: 'Sara Mohammed',
        body: 'Hello! Yes, we can consider a reasonable offer. Feel free to book a virtual or in-person visit.',
        created_at: new Date(Date.now() - 86400000).toISOString(),
        is_read: true,
        type: 'text'
      }
    ]
  }
]

function getStoredConversations() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) {
      const parsed = JSON.parse(raw)
      if (Array.isArray(parsed) && parsed.length > 0) {
        let modified = false
        parsed.forEach((c, idx) => {
          if (!c.messages || c.messages.length === 0) {
            const fallback = initialConversations[idx] || initialConversations[0]
            c.messages = fallback ? JSON.parse(JSON.stringify(fallback.messages)) : []
            modified = true
          }
          if (!c.other_user) {
            const fallback = initialConversations[idx] || initialConversations[0]
            c.other_user = fallback?.other_user || { id: 1, name: 'Abebe Kebede', role: 'Property Owner', online: true }
            modified = true
          }
        })
        if (modified) {
          localStorage.setItem(STORAGE_KEY, JSON.stringify(parsed))
        }
        return parsed
      }
    }
  } catch {}
  localStorage.setItem(STORAGE_KEY, JSON.stringify(initialConversations))
  return JSON.parse(JSON.stringify(initialConversations))
}

function saveStoredConversations(list) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(list))
  } catch {}
}

export const conversationService = {
  /**
   * Get user conversation threads
   */
  async getConversations(params = {}) {
    try {
      return await cachedGet(ENDPOINTS.CONVERSATIONS.LIST, { params, ttl: 15000 })
    } catch {
      const list = getStoredConversations()
      return {
        data: {
          data: list,
          total: list.length
        }
      }
    }
  },

  /**
   * Start a new conversation or inquiry
   */
  async startConversation(data) {
    clearApiCache('/conversations')
    try {
      return await api.post(ENDPOINTS.CONVERSATIONS.CREATE, data)
    } catch {
      const list = getStoredConversations()
      const existing = list.find(c => 
        (c.property_id === Number(data.property_id) || c.other_user?.id === Number(data.recipient_id))
      )
      if (existing) {
        if (data.initial_message) {
          existing.messages.push({
            id: Date.now(),
            sender_id: 999,
            sender_name: 'You',
            body: data.initial_message,
            created_at: new Date().toISOString(),
            is_read: false,
            type: 'text'
          })
          existing.updated_at = new Date().toISOString()
          saveStoredConversations(list)
        }
        return { data: existing }
      }

      const newConv = {
        id: Date.now(),
        property_id: Number(data.property_id || 1),
        property: {
          id: Number(data.property_id || 1),
          title: data.property_title || 'Selected Property',
          price: 0,
          location: 'Addis Ababa'
        },
        other_user: {
          id: Number(data.recipient_id || 1),
          name: data.recipient_name || 'Property Owner',
          role: 'Owner',
          avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80',
          online: true
        },
        unread_count: 0,
        updated_at: new Date().toISOString(),
        messages: data.initial_message ? [{
          id: Date.now(),
          sender_id: 999,
          sender_name: 'You',
          body: data.initial_message,
          created_at: new Date().toISOString(),
          is_read: false,
          type: 'text'
        }] : []
      }
      list.unshift(newConv)
      saveStoredConversations(list)
      return { data: newConv }
    }
  },

  /**
   * Get messages for a conversation
   */
  async getMessages(conversationId, params = {}) {
    try {
      return await api.get(ENDPOINTS.CONVERSATIONS.MESSAGES(conversationId), { params })
    } catch {
      const list = getStoredConversations()
      const match = list.find(c => c.id === Number(conversationId))
      return {
        data: {
          data: match?.messages || [],
          total: match?.messages?.length || 0
        }
      }
    }
  },

  /**
   * Send a message
   */
  async sendMessage(conversationId, data) {
    clearApiCache('/conversations')
    try {
      const isFormData = data instanceof FormData
      return await api.post(ENDPOINTS.CONVERSATIONS.SEND_MESSAGE(conversationId), data, {
        headers: isFormData ? { 'Content-Type': 'multipart/form-data' } : {},
      })
    } catch {
      const list = getStoredConversations()
      const conv = list.find(c => c.id === Number(conversationId))
      if (conv) {
        const text = typeof data === 'string' ? data : (data.body || data.message || '')
        const newMsg = {
          id: Date.now(),
          sender_id: 999,
          sender_name: 'You',
          body: text,
          created_at: new Date().toISOString(),
          is_read: false,
          type: 'text'
        }
        conv.messages.push(newMsg)
        conv.updated_at = new Date().toISOString()
        saveStoredConversations(list)
        return { data: newMsg }
      }
      return { success: true }
    }
  },

  /**
   * Edit a message body
   */
  async editMessage(conversationId, messageId, newBody) {
    clearApiCache('/conversations')
    try {
      return await api.put(`/conversations/${conversationId}/messages/${messageId}`, { body: newBody })
    } catch {
      const list = getStoredConversations()
      const conv = list.find(c => c.id === Number(conversationId))
      if (conv) {
        const msg = conv.messages.find(m => m.id === Number(messageId))
        if (msg) {
          msg.body = newBody
          msg.is_edited = true
          saveStoredConversations(list)
          return { data: msg }
        }
      }
      return { success: true }
    }
  },

  /**
   * Delete a message
   */
  async deleteMessage(conversationId, messageId) {
    clearApiCache('/conversations')
    try {
      return await api.delete(`/conversations/${conversationId}/messages/${messageId}`)
    } catch {
      const list = getStoredConversations()
      const conv = list.find(c => c.id === Number(conversationId))
      if (conv) {
        conv.messages = conv.messages.filter(m => m.id !== Number(messageId))
        saveStoredConversations(list)
      }
      return { success: true }
    }
  },

  /**
   * Delete conversation thread
   */
  async deleteConversation(conversationId) {
    clearApiCache('/conversations')
    try {
      return await api.delete(ENDPOINTS.CONVERSATIONS.DELETE(conversationId))
    } catch {
      let list = getStoredConversations()
      list = list.filter(c => c.id !== Number(conversationId))
      saveStoredConversations(list)
      return { success: true }
    }
  },

  /**
   * Mark conversation as read
   */
  async markAsRead(conversationId) {
    clearApiCache('/conversations')
    try {
      await api.put(ENDPOINTS.CONVERSATIONS.MARK_READ(conversationId))
    } catch {
      try {
        await api.post(ENDPOINTS.CONVERSATIONS.MARK_READ(conversationId))
      } catch {}
    }

    try {
      const list = getStoredConversations()
      const conv = list.find(c => c.id === Number(conversationId))
      if (conv) {
        conv.unread_count = 0
        if (Array.isArray(conv.messages)) {
          conv.messages.forEach(m => { m.is_read = true })
        }
        saveStoredConversations(list)
      }
    } catch {}

    window.dispatchEvent(new CustomEvent('message-read'))
    window.dispatchEvent(new CustomEvent('conversations-updated'))
    return { success: true }
  },

  /**
   * Mark all conversations as read
   */
  async markAllAsRead() {
    clearApiCache('/conversations')
    try {
      await api.post('/conversations/read-all')
    } catch {}

    try {
      const list = getStoredConversations()
      list.forEach(conv => {
        conv.unread_count = 0
        if (Array.isArray(conv.messages)) {
          conv.messages.forEach(m => { m.is_read = true })
        }
      })
      saveStoredConversations(list)
    } catch {}

    window.dispatchEvent(new CustomEvent('message-read'))
    window.dispatchEvent(new CustomEvent('conversations-updated'))
    return { success: true }
  }
}

export default conversationService
