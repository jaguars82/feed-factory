<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  transports: {
    type: Array,
  }
});

</script>

<template>
  <Head title="Транспорт файлов шахматок" />

  <AuthenticatedLayout>
      
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Транспорт файлов шахматок
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div class="mb-4">
            <Link :href="route('transport.add')">
              <el-button type="primary">Добавить транспорт</el-button>
            </Link>
          </div>

          <el-table :data="transports" empty-text="Не добавлено ни одного транспорта">
            <el-table-column prop="name" label="Название" />
            <el-table-column prop="type" label="Тип" />
            <el-table-column prop="email" label="Адрес" />
          </el-table>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>