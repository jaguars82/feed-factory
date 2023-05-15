<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  feeds: {
    type: Array,
  }
});

</script>

<template>
  <Head title="Фиды" />

  <AuthenticatedLayout>
      
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Фиды
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div class="mb-4">
            <Link :href="route('feed.add')">
              <el-button type="primary">Создать фид</el-button>
            </Link>
          </div>

          <el-table :data="feeds" empty-text="Нет фидов">
            <el-table-column prop="name" label="Название фида" />
            <el-table-column prop="provider.name" label="Поставщик" />
            <el-table-column label="Активен" align="center" align-header="center">
              <template #default="scope">
                <el-icon :color="scope.row.is_active === 1 ? 'success' : 'danger'">
                  <Check v-if="scope.row.is_active === 1" />
                  <Close v-else />
                </el-icon>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>