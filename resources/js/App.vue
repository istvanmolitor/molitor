<template>
  <RouterView />
  <LoadingSpinner v-if="isPageLoading" fullscreen overlay label="Oldal betoltese..." />
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import LoadingSpinner from '@admin/components/ui/LoadingSpinner.vue'

const router = useRouter()
const isPageLoading = ref(true)

const removeBeforeResolveGuard = router.beforeResolve(() => {
  isPageLoading.value = true
})

const removeAfterEachHook = router.afterEach(() => {
  isPageLoading.value = false
})

const removeErrorHook = router.onError(() => {
  isPageLoading.value = false
})

onMounted(async () => {
  await router.isReady()
  isPageLoading.value = false
})

onUnmounted(() => {
  removeBeforeResolveGuard()
  removeAfterEachHook()
  removeErrorHook()
})
</script>

<style>
/* Global styles - ensure full height */
#app {
  height: 100%;
}
</style>
