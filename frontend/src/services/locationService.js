import api from './api'
import { ENDPOINTS } from '../config/api'

const cache = {
  cities: null,
  subCities: {},
  neighborhoods: {},
  allSubCities: null,
}

export const locationService = {
  /**
   * Get all active Ethiopian regions / cities
   */
  async getCities() {
    if (cache.cities) return cache.cities
    const res = await api.get(ENDPOINTS.LOCATIONS.CITIES)
    cache.cities = res
    return res
  },

  /**
   * Get sub-cities / zones for a city
   * @param {number|string} cityId
   */
  async getSubCities(cityId) {
    if (cache.subCities[cityId]) return cache.subCities[cityId]
    const res = await api.get(ENDPOINTS.LOCATIONS.SUB_CITIES(cityId))
    cache.subCities[cityId] = res
    return res
  },

  /**
   * Get all sub-cities across all cities with parent city information
   */
  async getAllSubCities() {
    if (cache.allSubCities) return cache.allSubCities
    try {
      const res = await api.get('/locations/all')
      const list = res?.data || (Array.isArray(res) ? res : [])
      if (Array.isArray(list) && list.length > 0) {
        cache.allSubCities = list
        return list
      }
    } catch (e) {
      // fallback to multi-fetch
    }

    const citiesRes = await this.getCities()
    const citiesList = citiesRes?.data || (Array.isArray(citiesRes) ? citiesRes : [])
    if (!citiesList.length) return []

    const promises = citiesList.map(async (city) => {
      try {
        const res = await this.getSubCities(city.id)
        const list = res?.data || (Array.isArray(res) ? res : [])
        return list.map(sc => ({ ...sc, cityName: city.name, cityId: city.id }))
      } catch (e) {
        return []
      }
    })

    const results = await Promise.all(promises)
    const flat = results.flat()
    cache.allSubCities = flat
    return flat
  },

  /**
   * Get neighborhoods / kebeles for a sub-city
   * @param {number|string} subCityId
   */
  async getNeighborhoods(subCityId) {
    if (cache.neighborhoods[subCityId]) return cache.neighborhoods[subCityId]
    const res = await api.get(ENDPOINTS.LOCATIONS.NEIGHBORHOODS(subCityId))
    cache.neighborhoods[subCityId] = res
    return res
  },

  clearCache() {
    cache.cities = null
    cache.subCities = {}
    cache.neighborhoods = {}
    cache.allSubCities = null
  }
}

export default locationService
