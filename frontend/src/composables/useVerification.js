import { useHttp } from './useHttp'
import { useToast } from './useToast'

export function useVerification() {
  const { get, uploadFile, post } = useHttp()
  const { success, error } = useToast()

  // Get verification status
  const getVerificationStatus = async () => {
    try {
      return await get('/verification/status')
    } catch (err) {
      console.error('Failed to fetch verification status:', err)
      return { success: false, error: 'Failed to fetch verification status' }
    }
  }

  // Upload verification document
  const uploadDocument = async (documentType, file) => {
    try {
      const formData = new FormData()
      formData.append('document_type', documentType)
      formData.append('document', file)

      const result = await uploadFile('/verification/documents', formData)
      if (result.success) {
        success('Document Uploaded', 'Your verification document has been uploaded successfully')
      }
      return result
    } catch (err) {
      console.error('Failed to upload document:', err)
      return { success: false, error: 'Failed to upload document' }
    }
  }

  // Submit verification for review
  const submitVerification = async () => {
    try {
      const result = await post('/verification/submit', {})
      if (result.success) {
        success('Submitted', 'Your verification has been submitted for review')
      }
      return result
    } catch (err) {
      console.error('Failed to submit verification:', err)
      return { success: false, error: 'Failed to submit verification' }
    }
  }

  return {
    getVerificationStatus,
    uploadDocument,
    submitVerification
  }
}
