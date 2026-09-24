import { useHttp } from './useHttp'
import { useToast } from './useToast'

export function useAppointment() {
  const { get, post, put, patch, delete: deleteRequest } = useHttp()
  const { success, error } = useToast()

  // Get all appointments
  const getAppointments = async (params = {}) => {
    try {
      const query = new URLSearchParams(params).toString()
      const endpoint = query ? `/appointments?${query}` : '/appointments'
      return await get(endpoint)
    } catch (err) {
      console.error('Failed to fetch appointments:', err)
      return { success: false, error: 'Failed to fetch appointments' }
    }
  }

  // Get single appointment by ID
  const getAppointmentById = async (id) => {
    try {
      return await get(`/appointments/${id}`)
    } catch (err) {
      console.error('Failed to fetch appointment:', err)
      return { success: false, error: 'Failed to fetch appointment' }
    }
  }

  // Create new appointment
  const createAppointment = async (appointmentData) => {
    try {
      const result = await post('/appointments', appointmentData)
      if (result.success) {
        success('Appointment Scheduled', 'Your appointment has been scheduled successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to create appointment:', err)
      return { success: false, error: 'Failed to create appointment' }
    }
  }

  // Update appointment
  const updateAppointment = async (id, appointmentData) => {
    try {
      const result = await put(`/appointments/${id}`, appointmentData)
      if (result.success) {
        success('Appointment Updated', 'Appointment has been updated successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to update appointment:', err)
      return { success: false, error: 'Failed to update appointment' }
    }
  }

  // Cancel appointment
  const cancelAppointment = async (id, reason = '') => {
    try {
      const result = await patch(`/appointments/${id}/cancel`, { reason })
      if (result.success) {
        success('Appointment Cancelled', 'Appointment has been cancelled')
      }
      return result
    } catch (err) {
      console.error('Failed to cancel appointment:', err)
      return { success: false, error: 'Failed to cancel appointment' }
    }
  }

  // Confirm appointment
  const confirmAppointment = async (id) => {
    try {
      const result = await patch(`/appointments/${id}/confirm`, {})
      if (result.success) {
        success('Confirmed', 'Appointment has been confirmed')
      }
      return result
    } catch (err) {
      console.error('Failed to confirm appointment:', err)
      return { success: false, error: 'Failed to confirm appointment' }
    }
  }

  // Complete appointment
  const completeAppointment = async (id) => {
    try {
      const result = await patch(`/appointments/${id}/complete`, {})
      if (result.success) {
        success('Completed', 'Appointment has been marked as completed')
      }
      return result
    } catch (err) {
      console.error('Failed to complete appointment:', err)
      return { success: false, error: 'Failed to complete appointment' }
    }
  }

  // Delete appointment
  const deleteAppointment = async (id) => {
    try {
      const result = await deleteRequest(`/appointments/${id}`)
      if (result.success) {
        success('Appointment Deleted', 'Appointment has been deleted')
      }
      return result
    } catch (err) {
      console.error('Failed to delete appointment:', err)
      return { success: false, error: 'Failed to delete appointment' }
    }
  }

  return {
    getAppointments,
    getAppointmentById,
    createAppointment,
    updateAppointment,
    cancelAppointment,
    confirmAppointment,
    completeAppointment,
    deleteAppointment
  }
}
