<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-200">
    <!-- Main Public Navbar -->
    <Navbar />

    <!-- Main Content Area with clearance for fixed navbar -->
    <main class="flex-1" :class="{ 'pt-16 lg:pt-20': !isHomePage }">
      <RouterView />
    </main>

    <!-- Public Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import Navbar from '../components/layout/Navbar.vue'
import Footer from '../components/layout/Footer.vue'

const route = useRoute()
const isHomePage = computed(() => route.path === '/' || route.name === 'home')
</script>
