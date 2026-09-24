<template>
  <UnifiedPropertyCard :property="property" />
</template>

<script setup>
import UnifiedPropertyCard from '../property/PropertyCard.vue'

defineProps({
  property: {
    type: Object,
    required: true,
  },
})
</script>
