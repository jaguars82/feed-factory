<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/inertia-vue3';


const form = useForm({
  name: '',
  type: 'email',
  email: '',
  email_login: '',
  email_pass: '',
});


const clearForm = () => {
  form.name = '';
  form.type = '';
  form.email = '';
  form.email_login = '';
  form.email_pass = '';
}

const onSubmit = () => {
  form.post('/transport/add');
}
</script>

<template>
    <Head title="Добавление транспорта файлов" />

    <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Добавление транспорта файлов
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div>
            <el-form :model="form" label-width="120px">
              <el-form-item label="Название">
                <el-input v-model="form.name" />
              </el-form-item>
              <el-form-item label="Тип">
                <el-select v-model="form.type" placeholder="Укажите тип транспорта">
                  <el-option label="Электронная почта" value="email" />
                </el-select>
              </el-form-item>
              <el-form-item label="Email">
                <el-input v-model="form.email" />
              </el-form-item>
              <el-form-item label="Логин">
                <el-input v-model="form.email_login" />
              </el-form-item>
              <el-form-item label="Пароль">
                <el-input v-model="form.email_pass" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSubmit">Добавить транспорт</el-button>
                <el-button @click="clearForm">Очистить</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>