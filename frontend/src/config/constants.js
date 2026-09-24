/**
 * BetLink Dynamic Configuration & Helpers
 */

export const CURRENCY = {
  CODE: 'ETB',
  SYMBOL: 'ETB',
  NAME: 'Ethiopian Birr',
}

/**
 * Dynamically resolves supported listing types
 */
export function getListingTypes() {
  return [
    { value: 'sale', label: 'For Sale' },
    { value: 'rent', label: 'For Rent' },
    { value: 'short_rent', label: 'Short-term Rental' },
  ]
}

/**
 * Normalizes raw listing type string
 */
export function normalizeListingType(type) {
  if (!type) return 'sale'
  const clean = String(type).toLowerCase().replace('-', '_')
  if (clean.includes('rent') && (clean.includes('short') || clean.includes('stay'))) return 'short_rent'
  if (clean.includes('rent')) return 'rent'
  return 'sale'
}

/**
 * Formats price with dynamic currency support
 */
export function formatCurrency(amount, currency = 'ETB') {
  if (amount === null || amount === undefined || isNaN(amount)) return '0 ' + currency
  return Number(amount).toLocaleString('en-US') + ' ' + currency
}
