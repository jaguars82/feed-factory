<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
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
  },
  currentStep: {
    type: Number
  }
});

const emit = defineEmits(['submitChessParams']);

const loading = ref(false);

const form = useForm({
  operation: 'save_chess_params',
  provider_id: '',
  developer_id: '',
  newbuilding_complex_id: '',
  newbuilding_id: '',
  complex_feed_name: '',
  building_feed_name: '',
  developer_alias: '',
  complex_alias: '',
  building_alias: '',
  name: '',
  last_completed_formstep: props.currentStep
});

/* 
 * flags and names for new complex and building
 * we create manually (not select from grch database)
 */
const newComplexFlag = ref(false);
const newBuildingFlag = ref(false);
const customChessNameFlag = ref(false);
const newComplexName = ref('');
const newBuildingName = ref('');
const customChessName = ref('');

/*
 * generated chess name
 */
const autoChessName = computed(() => {
  let name = '';
  if (form.developer_alias) {
    name += form.developer_alias;
  }
  if (form.complex_alias) {
    name += ` > ${form.complex_alias}`;
  }
  if (form.building_alias) {
    name += ` > ${form.building_alias}`;
  }
  return name;
});

/*
 * get a particular object from an array, filtered by a set key
 */
const getFilteredObject = (filterBy, dataSet) => {
  const item = dataSet.filter(elem => {
    return elem.id === form[filterBy];
  });
  return item[0];
}

const onDeveloperSelect = () => {
  form.newbuilding_complex_id = '';
  form.newbuilding_id = '';
  form.developer_alias = '';
  form.complex_alias = '';
  form.building_alias = '';
  form.complex_feed_name = '';
  form.building_feed_name = '';
  Inertia.visit('/chess/add', { 
    method: 'post',
    only: ['newbuildingComplexes'],
    preserveState: true,
    preserveScroll: true,
    data: {
      developerId: form.developer_id,
    },
    onFinish: () => {
      const currentDeveloper = getFilteredObject('developer_id', props.developers);
      form.developer_alias = currentDeveloper.name;
    }
  });
}

const onComplexSelect = () => {
  form.newbuilding_id = '';
  form.complex_alias = '';
  form.building_alias = '';
  form.complex_feed_name = '';
  form.building_feed_name = '';
  Inertia.visit('/chess/add', { 
    method: 'post',
    only: ['newbuildings'],
    preserveState: true,
    preserveScroll: true,
    data: {
      complexId: form.newbuilding_complex_id,
    },
    onFinish: () => {
      const currentComplex = getFilteredObject('newbuilding_complex_id', props.newbuildingComplexes);
      form.complex_alias = currentComplex.name;
      form.complex_feed_name = currentComplex.feed_name;
    }
  });
}

const onNewbuildingSelect = () => {
  const currentBuilding = getFilteredObject('newbuilding_id', props.newbuildings);
  form.building_alias = currentBuilding.name;
  form.building_feed_name = currentBuilding.feed_name;
}

/*
 * check if all the fields have been filled
 */
const canSubmitChessParams = computed(() => {
  if (form.provider_id === '') return false; // provider field (select)
  if (form.developer_alias === '') return false; // developer field (select)
  if (newComplexFlag.value === true && newComplexName.value === '') return false; // newbuilding-complex field (input)
  if (newComplexFlag.value === false && form.complex_alias === '') return false; // newbuilding-complex field (select)
  if (newBuildingFlag.value === true && newBuildingName.value === '') return false; // newbuilding field (input)
  if (newBuildingFlag.value === false && form.building_alias === '') return false; // newbuilding field (select)
  if (customChessNameFlag.value === true && customChessName.value === '' ) return false;
  return true;
  
});

const fillDuoFilds = () => {
  // filling duo-fields (were we can select a value or type it manually)
  // chess name
  if (customChessNameFlag.value === true) {
    form.name = customChessName.value;
  } else {
    form.name = autoChessName.value;
  }

  // newbuilding-complex alias and feed_name
  if (newComplexFlag.value === true) {
    form.complex_alias = newComplexName.value;
    form.complex_feed_name = newComplexName.value;
  } 

  // newbuilding alisa and feed_name
  if (newBuildingFlag.value === true) {
    form.building_alias = newBuildingName.value;
    form.building_feed_name = newBuildingName.value;
  }
}

const onSubmitChessParams = () => {
  loading.value = true;
  fillDuoFilds();
    form.post('/chess/add', {
    onSuccess: () => { 
      loading.value = false;
      emit('submitChessParams');
    },
  });
  
}
</script>

<template>
  <el-form v-loading="loading" :model="form" label-width="200px">
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
    <el-form-item label="Жилой комплекс">
      <el-col :span="6">
        <el-input v-if="newComplexFlag" v-model="newComplexName" placeholder="Название комплекса" @change="fillDuoFilds" />
        <el-select v-if="newComplexFlag === false && newbuildingComplexes.length" v-model="form.newbuilding_complex_id" placeholder="Выберите комплекс" @change="onComplexSelect">
          <el-option v-for="complex of newbuildingComplexes" :key="complex.id" :label="complex.name" :value="complex.id" />
        </el-select>
      </el-col>
      <el-col :span="18" class="px-5" >
        <el-checkbox v-model="newComplexFlag" label="Добавить новый" size="large" @change="fillDuoFilds" />
      </el-col> 
    </el-form-item>
    <el-form-item label="Позиция">
      <el-col :span="6">
        <el-input v-if="newBuildingFlag" v-model="newBuildingName" placeholder="Название позиции" @change="fillDuoFilds" />
        <el-select v-if="newBuildingFlag === false && newbuildings.length" v-model="form.newbuilding_id" placeholder="Позиция" @change="onNewbuildingSelect">
          <el-option v-for="newbuilding of newbuildings" :key="newbuilding.id" :label="newbuilding.name" :value="newbuilding.id" />
        </el-select>
      </el-col>
      <el-col :span="18" class="px-5">
        <el-checkbox v-model="newBuildingFlag" label="Добавить новую" size="large" @change="fillDuoFilds" />
      </el-col>
    </el-form-item>
    <el-form-item label="Название">
      <el-col :span="6">
        <span v-if="customChessNameFlag === false">{{ autoChessName }}</span>
        <el-input v-if="customChessNameFlag" v-model="customChessName" placeholder="Введите название" />
      </el-col>
      <el-col :span="18" class="px-5">
        <el-checkbox v-model="customChessNameFlag" label="Ввести вручную" size="large" />
      </el-col>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="onSubmitChessParams" :disabled="!canSubmitChessParams">Дальше</el-button>
    </el-form-item>
  </el-form>
</template>