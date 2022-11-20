<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  font: {
    type: String,
    default: 'fontawesome'
  },
  icon: {
    type: String,
    default: 'arrow-up-right-from-square'
  },
  text: {
    type: String,
    default: ''
  },
  href: {
    type: String,
    default: ''
  }
})

</script>

<template>
  <Link :href="href">
    <div class="group w-40 h-32 p-4 text-center font-semibold border rounded-md border-gray-200 hover:border-gray-300 hover:bg-slate-100 hover:cursor-pointer">
      <font-awesome-icon class="text-gray-600 group-hover:text-gray-800" v-if="font === 'fontawesome'" :icon="`fa-solid fa-${icon}`" size="xl" />
      <p v-if="text" class="mt-3 text-gray-800">{{ text }}</p>
    </div>
  </Link>
</template>