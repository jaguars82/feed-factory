<script setup>
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  providers: {
    type: Array,
  },
  developers: {
    type: Array,
  },
  newbuildingComplexes: {
    type: Array,
  },
  newbuildings: {
    type: Array,
  }
});

const emit = defineEmits(['submitChessParams']);

const form = useForm({
  provider_id: '',
  developer_id: '',
  newbuilding_complex_id: '',
  newbuilding_id: ''
});

const onDeveloperSelect = () => {
  form.newbuilding_complex_id = '';
  form.newbuilding_id = '';
  Inertia.visit('/chess', { 
    method: 'post',
    only: ['newbuildingComplexes'],
    preserveState: true,
    preserveScroll: true,
    data: {
      developerId: form.developer_id,
    },
    onFinish: () => {
      //
    }
  });
}

const onComplexSelect = () => {
  form.newbuilding_id = '';
  Inertia.visit('/chess', { 
    method: 'post',
    only: ['newbuildings'],
    preserveState: true,
    preserveScroll: true,
    data: {
      complexId: form.newbuilding_complex_id,
    },
    onFinish: () => {
      //
    }
  });
}

const onNewbuildingSelect = () => {
  //
}

const onSubmitChessParams = () => {
  emit('submitChessParams');
}
</script>

<template>
  <el-form :model="form" label-width="200px">
    <el-form-item label="Провайдер фида">
      <el-select v-model="form.provider_id" placeholder="Выберите провайдера">
        <el-option v-for="provider of providers" :key="provider.id" :label="provider.name" :value="provider.id" />
      </el-select>
    </el-form-item>
    <el-form-item label="Застройщик">
      <el-select v-model="form.developer_id" placeholder="Застройщик" @change="onDeveloperSelect">
        <el-option v-for="developer of developers" :key="developer.id" :label="developer.name" :value="developer.id" />
      </el-select>
    </el-form-item>
    <el-form-item v-if="newbuildingComplexes.length" label="Жилой комплекс">
      <el-select v-model="form.newbuilding_complex_id" placeholder="Жилой комплекс" @change="onComplexSelect">
        <el-option v-for="complex of newbuildingComplexes" :key="complex.id" :label="complex.name" :value="complex.id" />
      </el-select>
    </el-form-item>
    <el-form-item v-if="newbuildings.length" label="Позиция">
      <el-select v-model="form.newbuilding_id" placeholder="Позиция" @change="onNewbuildingSelect">
        <el-option v-for="newbuilding of newbuildings" :key="newbuilding.id" :label="newbuilding.name" :value="newbuilding.id" />
      </el-select>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="onSubmitChessParams">Дальше</el-button>
    </el-form-item>
  </el-form>
</template>