<script setup>
import { reactive, ref, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';

const props = defineProps({
  developers: {
    type: 4
  }
});

const selectedFromGRCH = ref();

const chosenGRCHDeveloper = computed(() => {
  if (!selectedFromGRCH.value) return false; 
  const developer = props.developers.filter(developer => {
    return developer.id === selectedFromGRCH.value;
  });
  return developer[0];
});

const form = useForm({
  name: '',
  address: '',
  detail: '',
  phone: '',
  url: '',
  email: ''
});

const fillFromGRCH = () => {
  if (chosenGRCHDeveloper === false) { 
      clearForm(); 
    } else {
      form.name = chosenGRCHDeveloper.value.name;
      form.address = chosenGRCHDeveloper.value.address;
      form.detail = chosenGRCHDeveloper.value.detail;
      form.phone = chosenGRCHDeveloper.value.phone;
      form.url = chosenGRCHDeveloper.value.url;
      form.email = chosenGRCHDeveloper.value.email;     
    }
}

const clearForm = () => {
  form.name = '';
  form.address = '';
  form.detail = '';
  form.phone = '';
  form.url = '';
  form.email = '';  
}

const onSubmit = () => {
  form.post('/provider/add');
}

</script>

<template>
  <Head title="Добавление провайдера фидов" />

    <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Добавление провайдера фидов
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg">
          <!-- import developer selection -->
          <div>
            <el-select v-model="selectedFromGRCH" @change="fillFromGRCH" clearable>
              <el-option
                v-for="developer of developers"
                :key="developer.id"
                :label="developer.name"
                :value="developer.id"
              />
            </el-select>
          </div>

          <div>
            <el-form :model="form" label-width="120px">
              <el-form-item label="Наименование">
                <el-input v-model="form.name" />
              </el-form-item>
              <el-form-item label="Адрес">
                <el-input v-model="form.address" />
              </el-form-item>
              <el-form-item label="Описание">
                <el-input v-model="form.detail" type="textarea" />
              </el-form-item>
              <el-form-item label="Телефон">
                <el-input v-model="form.phone" />
              </el-form-item>
              <el-form-item label="Сайт">
                <el-input v-model="form.url" />
              </el-form-item>
              <el-form-item label="Email">
                <el-input v-model="form.email" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSubmit">Добавить провайдера</el-button>
                <el-button @click="clearForm">Очистить</el-button>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>