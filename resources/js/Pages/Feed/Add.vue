<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
  providers: {
    type: Array,
  }
});

const form = useForm({
  provider_id: '',
  name: '',
  is_active: true,
});


const clearForm = () => {
  form.provider_id = '';
  form.name = '';
  form.is_active = false;
}

const onSubmit = () => {
  form.post('/feed/add');
}
</script>

<template>
    <Head title="Создание нового фида" />

    <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Создание нового фида
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div>
            <el-form :model="form" label-width="180px">
              <el-form-item label="Название фида">
                <el-input v-model="form.name" />
              </el-form-item>
              <el-form-item label="Поставщик фида">
                <el-select v-model="form.provider_id" placeholder="Укажите провайдера (застройщика)">
                  <el-option
                    v-for="provider of providers"
                    :key="provider.id"
                    :label="provider.name"
                    :value="provider.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="Активировать фид">
                <el-checkbox v-model="form.is_active" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSubmit">Создать фид</el-button>
                <el-button @click="clearForm">Очистить</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>