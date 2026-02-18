<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-8 md:p-12 max-w-md w-full">
      <h1 class="text-4xl md:text-5xl font-bold text-center text-gray-900 dark:text-white mb-2">
        Hello World!
      </h1>
      <p class="text-center text-gray-600 dark:text-gray-300 mb-8">
        Vue 3 + Tailwind CSS Single Page Application
      </p>

      <div class="space-y-4">
        <div class="bg-blue-50 dark:bg-gray-700 rounded-lg p-4">
          <p class="text-sm text-gray-700 dark:text-gray-200">
            <span class="font-semibold">Current Count:</span> {{ count }}
          </p>
        </div>

        <div class="flex gap-3">
          <button
            @click="increment"
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105"
          >
            Increment
          </button>
          <button
            @click="decrement"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105"
          >
            Decrement
          </button>
        </div>

        <button
          @click="reset"
          class="w-full bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
        >
          Reset
        </button>
      </div>

      <div class="mt-8 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
        <p class="text-center text-green-800 dark:text-green-200 text-sm">
          ✓ Vue 3 is working perfectly!
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const count = ref(0);

const increment = () => {
  count.value++;
};

const decrement = () => {
  count.value--;
};

const reset = () => {
  count.value = 0;
};
</script>

<style scoped>
/* Scoped styles for this component */
</style>

