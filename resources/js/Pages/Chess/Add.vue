<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import EntranceSelectionForm from '@/Pages/Chess/Partials/EntranceSelectionForm.vue';
import ChessParamsForm from '@/Pages/Chess/Partials/ChessParamsForm.vue';

defineProps({
  chessData: {
    type: Array
  }
});

const activeStep = ref(0)

const nextStep = () => {
  if (activeStep.value++ > 3) activeStep.value = 0
}

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
        <div class="bg-white shadow-sm sm:rounded-lg">

        <el-steps :active="activeStep" align-center>
          <el-step title="Step 1" description="Some description" />
          <el-step title="Step 2" description="Some description" />
          <el-step title="Step 3" description="Some description" />
          <el-step title="Step 4" description="Some description" />
        </el-steps>
        
        <ChessParamsForm v-if="activeStep === 1" />
        <EntranceSelectionForm v-if="activeStep === 2" :chessData="chessData" />

        <el-button style="margin-top: 12px" @click="nextStep">Дальше</el-button>
          
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>