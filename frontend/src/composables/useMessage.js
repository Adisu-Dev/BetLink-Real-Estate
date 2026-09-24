import { useHttp } from './useHttp'
import { useToast } from './useToast'

export function useMessage() {
  const { get, post, patch, delete: deleteRequest } = useHttp()
  const { success, error } = useToast()

  // Get all messages/inquiries
  const getMessages = async (params = {}) => {
    try {
      const query = new URLSearchParams(params).toString()
      const endpoint = query ? `/messages?${query}` : '/messages'
      return await get(endpoint)
    } catch (err) {
      console.error('Failed to fetch messages:', err)
      return { success: false, error: 'Failed to fetch messages' }
    }
  }

  // Get single message by ID
  const getMessageById = async (id) => {
    try {
      return await get(`/messages/${id}`)
    } catch (err) {
      console.error('Failed to fetch message:', err)
      return { success: false, error: 'Failed to fetch message' }
    }
  }

  // Get all conversations
  const getConversations = async () => {
    try {
      return await get('/conversations')
    } catch (err) {
      console.error('Failed to fetch conversations:', err)
      return { success: false, error: 'Failed to fetch conversations' }
    }
  }

  // Send message/reply
  const sendMessage = async (conversationId, messageData) => {
    try {
      const result = await post(`/conversations/${conversationId}/messages`, messageData)
      if (result.success) {
        success('Message Sent', 'Your message has been sent successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to send message:', err)
      return { success: false, error: 'Failed to send message' }
    }
  }

  // Archive message/conversation
  const archiveMessage = async (conversationId) => {
    try {
      const result = await patch(`/conversations/${conversationId}/archive`, {})
      if (result.success) {
        success('Archived', 'Conversation has been archived')
      }
      return result
    } catch (err) {
      console.error('Failed to archive message:', err)
      return { success: false, error: 'Failed to archive message' }
    }
  }

  // Mark message as read
  const markAsRead = async (messageId) => {
    try {
      return await patch(`/messages/${messageId}/read`, {})
    } catch (err) {
      console.error('Failed to mark message as read:', err)
      return { success: false, error: 'Failed to mark message as read' }
    }
  }

  // Delete message
  const deleteMessage = async (messageId) => {
    try {
      const result = await deleteRequest(`/messages/${messageId}`)
      if (result.success) {
        success('Deleted', 'Message has been deleted')
      }
      return result
    } catch (err) {
      console.error('Failed to delete message:', err)
      return { success: false, error: 'Failed to delete message' }
    }
  }

  return {
    getMessages,
    getMessageById,
    getConversations,
    sendMessage,
    archiveMessage,
    markAsRead,
    deleteMessage
  }
}
