import { ref } from 'vue'
import { useHttp } from './useHttp'
import { useToast } from './useToast'

export function useProperty() {
  const { get, post, put, patch, delete: deleteRequest, uploadFile } = useHttp()
  const { success, error, info } = useToast()

  // Get all properties (with filters and pagination)
  const getProperties = async (params = {}) => {
    try {
      const query = new URLSearchParams(params).toString()
      const endpoint = query ? `/properties?${query}` : '/properties'
      return await get(endpoint)
    } catch (err) {
      console.error('Failed to fetch properties:', err)
      return { success: false, error: 'Failed to fetch properties' }
    }
  }

  // Get single property by ID
  const getPropertyById = async (id) => {
    try {
      return await get(`/properties/${id}`)
    } catch (err) {
      console.error('Failed to fetch property:', err)
      return { success: false, error: 'Failed to fetch property' }
    }
  }

  // Create new property
  const createProperty = async (propertyData) => {
    try {
      const result = await post('/properties', propertyData)
      if (result.success) {
        success('Property Added', 'Property has been created successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to create property:', err)
      return { success: false, error: 'Failed to create property' }
    }
  }

  // Update property
  const updateProperty = async (id, propertyData) => {
    try {
      const result = await put(`/properties/${id}`, propertyData)
      if (result.success) {
        success('Property Updated', 'Property has been updated successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to update property:', err)
      return { success: false, error: 'Failed to update property' }
    }
  }

  // Delete property
  const deleteProperty = async (id) => {
    try {
      const result = await deleteRequest(`/properties/${id}`)
      if (result.success) {
        success('Property Deleted', 'Property has been deleted successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to delete property:', err)
      return { success: false, error: 'Failed to delete property' }
    }
  }

  // Change property status
  const changePropertyStatus = async (id, status) => {
    try {
      const result = await patch(`/properties/${id}/status`, { status })
      if (result.success) {
        success('Status Updated', `Property status changed to ${status}`)
      }
      return result
    } catch (err) {
      console.error('Failed to change property status:', err)
      return { success: false, error: 'Failed to change property status' }
    }
  }

  // Submit property for review
  const submitForReview = async (id) => {
    try {
      const result = await post(`/properties/${id}/submit-review`, {})
      if (result.success) {
        success('Submitted', 'Property has been submitted for review')
      }
      return result
    } catch (err) {
      console.error('Failed to submit property:', err)
      return { success: false, error: 'Failed to submit property for review' }
    }
  }

  // Upload property image
  const uploadPropertyImage = async (propertyId, file) => {
    try {
      const formData = new FormData()
      formData.append('image', file)
      
      const result = await uploadFile(`/properties/${propertyId}/images`, formData)
      if (result.success) {
        success('Image Uploaded', 'Property image has been uploaded successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to upload image:', err)
      return { success: false, error: 'Failed to upload image' }
    }
  }

  // Delete property image
  const deletePropertyImage = async (propertyId, imageId) => {
    try {
      const result = await deleteRequest(`/properties/${propertyId}/images/${imageId}`)
      if (result.success) {
        success('Image Deleted', 'Property image has been deleted')
      }
      return result
    } catch (err) {
      console.error('Failed to delete image:', err)
      return { success: false, error: 'Failed to delete image' }
    }
  }

  // Get property images
  const getPropertyImages = async (propertyId) => {
    try {
      return await get(`/properties/${propertyId}/images`)
    } catch (err) {
      console.error('Failed to fetch images:', err)
      return { success: false, error: 'Failed to fetch images' }
    }
  }

  // Search properties
  const searchProperties = async (query, filters = {}) => {
    try {
      const params = { search: query, ...filters }
      return await getProperties(params)
    } catch (err) {
      console.error('Failed to search properties:', err)
      return { success: false, error: 'Failed to search properties' }
    }
  }

  // Get property types
  const getPropertyTypes = async () => {
    try {
      return await get('/property-types')
    } catch (err) {
      console.error('Failed to fetch property types:', err)
      return { success: false, error: 'Failed to fetch property types' }
    }
  }

  // Get property statuses
  const getPropertyStatuses = async () => {
    try {
      return await get('/property-statuses')
    } catch (err) {
      console.error('Failed to fetch property statuses:', err)
      return { success: false, error: 'Failed to fetch property statuses' }
    }
  }

  return {
    getProperties,
    getPropertyById,
    createProperty,
    updateProperty,
    deleteProperty,
    changePropertyStatus,
    submitForReview,
    uploadPropertyImage,
    deletePropertyImage,
    getPropertyImages,
    searchProperties,
    getPropertyTypes,
    getPropertyStatuses
  }
}
