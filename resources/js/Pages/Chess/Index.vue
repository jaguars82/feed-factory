<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  chesses: {
    type: Array,
  }
});

</script>

<template>
  <Head title="Шахматки" />

  <AuthenticatedLayout>
      
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Шахматки
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div class="mb-4">
            <Link :href="route('chess.add')">
              <el-button type="primary">Добавить шахматку</el-button>
            </Link>
          </div>

          <el-table :data="chesses" empty-text="Нет шахматок">
            <el-table-column prop="name" label="Название" />
            <el-table-column prop="complex_alias" label="ЖК" />
            <el-table-column prop="building_alias" label="Позиция" />
          </el-table>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>