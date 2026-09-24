/**
 * BetLink Utility Formatters
 */

/**
 * Format numeric price to Ethiopian Birr (ETB) format
 * @param {number|string} amount
 * @param {string} currency
 */
export function formatPrice(amount, currency = 'ETB') {
  if (amount === undefined || amount === null || isNaN(Number(amount))) {
    return 'Price on request'
  }

  const num = Number(amount)
  const formatted = new Intl.NumberFormat('en-US', {
    maximumFractionDigits: 0,
  }).format(num)

  return `${currency} ${formatted}`
}

/**
 * Format area in square meters
 * @param {number|string} area
 */
export function formatArea(area) {
  if (!area) return 'N/A'
  return `${Number(area).toLocaleString()} m²`
}

/**
 * Format date string to readable format
 * @param {string|Date} date
 */
export function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

/**
 * Format listing type to human readable label
 * @param {string} type
 */
export function formatListingType(type) {
  const map = {
    sale: 'For Sale',
    rent: 'For Rent',
    short_rent: 'Short Stay',
  }
  return map[type] || type || 'Property'
}

/**
 * Get listing type badge styling
 * @param {string} type
 */
export function getListingTypeVariant(type) {
  const map = {
    sale: 'success',
    rent: 'info',
    short_rent: 'purple',
  }
  return map[type] || 'default'
}
