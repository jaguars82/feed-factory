<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
  providers: {
    type: Array,
  }
});

</script>

<template>
  <Head title="Провайдеры фидов (застройщики)" />

    <AuthenticatedLayout>
      
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Провайдеры фидов (застройщики)
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white shadow-sm sm:rounded-lg">
            <el-table :data="providers">
              <el-table-column prop="name" label="Название" />
              <el-table-column prop="address" label="Адрес" />
              <el-table-column prop="url" label="Сайт" />
              <el-table-column prop="phone" label="Телефон" />
              <el-table-column prop="email" label="Email" />
            </el-table>
          </div>
        </div>
      </div>

      </AuthenticatedLayout>
    </template>

