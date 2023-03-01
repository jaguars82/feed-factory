<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  currentChessId: {
    type: Number
  },
  currentStep: {
    type: Number
  },
  transports: {
    type: Array,
  },
  feeds: {
    type: Array,
  }
});

const loading = ref(false);

const form = useForm({
  operation: 'save_feed_transport',
  chessId: props.currentChessId,
  transport_id: '',
  feed_id: '',
  sender_email: '',
  last_completed_formstep: props.currentStep,
  is_configuration_complete: true
});

const canSubmitTransportAndFeed = computed(() => {
  if (form.transport_id == '') return false;
  if (form.feed_id == '') return false;
  return true;
});

const onSubmitTransportAndFeed = () => {
  loading.value = true;
  form.post('/chess/add', {
    onSuccess: () => { 
      loading.value = false;
    },
  });
}

</script>

<template>

  <el-form v-loading="loading" :model="form" label-width="200px">
    <el-form-item label="Источник файла">
      <el-select v-model="form.transport_id" placeholder="Выберите источник">
        <el-option v-for="transport of transports" :key="transport.id" :label="transport.name" :value="transport.id" />
      </el-select>
    </el-form-item>
    <el-form-item label="Email отправителя">
      <el-input v-model="form.sender_email" placeholder="Введите email" />
    </el-form-item>
    <el-form-item label="Поместить в фид">
      <el-select v-model="form.feed_id" placeholder="Выберите фид">
        <el-option v-for="feed of feeds" :key="feed.id" :label="feed.name" :value="feed.id" />
      </el-select>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="onSubmitTransportAndFeed" :disabled="!canSubmitTransportAndFeed">Сохранить шахматку</el-button>
    </el-form-item>
  </el-form>

</template>