<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
  chessData: {
    type: Array
  }
})
</script>

<template>
  <Head title="Добавление новой шахматки" />

    <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Добавление новой шахматки
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div v-for="row of chessData" :key="row" class="flex">
              <div
                class="table-cell p-1 border border-gray-200 whitespace-nowrap overflow-visible"
                :class="[
                  `bg-[#${cell.bgColor1}]`,
                  cell.borders.right !== 'none' ? 'border-r-black' : '',
                  cell.borders.left !== 'none' ? 'border-l-black' : '',
                  cell.borders.top !== 'none' ? 'border-t-black' : '',
                  cell.borders.bottom !== 'none' ? 'border-b-black' : '',
                ]"
                v-for="cell of row"
                :key="cell.address"
              >
                {{ cell.rawValue }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.table-cell {
  width: 70px;
  min-width: 70px;
  font-size: 11px;
}
</style>