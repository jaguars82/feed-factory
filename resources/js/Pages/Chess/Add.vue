<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import EntranceSelectionForm from '@/Pages/Chess/Partials/EntranceSelectionForm.vue';
import ChessParamsForm from '@/Pages/Chess/Partials/ChessParamsForm.vue';
import TransportAndFeedForm from '@/Pages/Chess/Partials/TransportAndFeedForm.vue';

defineProps({
  currentChessId: {
    type: Number || null
  },
  allSheets: {
    type: Array,
  },
  chessData: {
    type: Array,
  },
  aviableColors: {
    type: Array,
  },
  providers: {
    type: Array,
  },
  transports: {
    type: Array,
  },
  developers: {
    type: Array,
  },
  feeds: {
    type: Array,
  },
  newbuildingComplexes: {
    type: Array,
    default: []
  },
  newbuildings: {
    type: Array,
    default: []
  }
});

const activeStep = ref(0);

const nextStep = () => {
  if (activeStep.value++ > 3) activeStep.value = 0;
}

const goStep = (step) => {
  activeStep.value = step;
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
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

        <el-steps class="mt-3 mb-10" :active="activeStep" align-center>
          <el-step title="Этап 1" description="Настройки шахматки" />
          <el-step title="Этап 2" description="Разметка подъездов" />
          <el-step title="Этап 3" description="Транспорт файлов и привязка к фиду" />
        </el-steps>
        
        <div class="p-6 border border-gray-600 rounded-md">

          <ChessParamsForm
            v-if="activeStep === 0"
            :providers="providers"
            :developers="developers"
            :newbuildingComplexes="newbuildingComplexes"
            :newbuildings="newbuildings"
            :currentStep="activeStep"
            @submit-chess-params="goStep(1)"
          />
          <EntranceSelectionForm
            v-if="activeStep === 1"
            :currentChessId="currentChessId"
            :currentStep="activeStep"
            :allSheets="allSheets"
            :chessData="chessData"
            :aviableColors="aviableColors"
            @submit-entrances-data="goStep(2)"
          />
          <TransportAndFeedForm
            v-if="activeStep === 2"
            :currentChessId="currentChessId"
            :currentStep="activeStep"
            :transports="transports"
            :feeds="feeds"
          />

        </div>

        <!--<el-button style="margin-top: 12px" @click="nextStep">Дальше</el-button>-->
          
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>