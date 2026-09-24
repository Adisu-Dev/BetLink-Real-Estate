<template>
  <div class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-200">
    <!-- Hero Section with Validated Standalone Search -->
    <HeroSection />

    <!-- Property Types & Categories -->
    <PropertyTypes />

    <!-- Featured & Verified Properties (Dynamic from API) -->
    <FeaturedProperties />

    <!-- Popular Locations in Ethiopia -->
    <PopularLocations />

    <!-- How BetLink Works -->
    <HowItWorks />
  </div>
</template>

<script setup>
import HeroSection from '../components/landing/HeroSection.vue'
import PropertyTypes from '../components/landing/PropertyTypes.vue'
import FeaturedProperties from '../components/landing/FeaturedProperties.vue'
import PopularLocations from '../components/landing/PopularLocations.vue'
import HowItWorks from '../components/landing/HowItWorks.vue'
</script>
