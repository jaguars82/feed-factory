<script setup>
import { ref, unref, reactive, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ElNotification, genFileId } from 'element-plus';
import { InfoFilled } from '@element-plus/icons-vue';
import { chessParams } from '@/helpers/chess-schemes';

defineProps({
  chessData: {
    type: Array
  }
});

/* notifications */
const showSelectStartNote = () => {
  ElNotification({
    title: 'Выберите начало подъезда',
    message: 'Для этого щёлкните по ячейке в его левом верхнем углу',
    duration: 0,
  });
}
const showStartSelectedNote = (col, row) => {
  ElNotification({
    title: `Ячейка ${col}${row}`,
    message: 'выбрана в качестве начала подъезда',
    type: 'success'
  });
}
const showSelectEndNote = () => {
  ElNotification({
    title: 'Выберите конец подъезда',
    message: 'Для этого щёлкните по ячейке в его правом нижнем углу',
    offset: 100,
    duration: 0
  });
}
const showEndSelectedNote = (col, row) => {
  ElNotification({
    title: `Ячейка ${col}${row}`,
    message: 'выбрана в качестве окончания подъезда',
    type: 'success'
  });
}
const showEndSelectError = () => {
  ElNotification({
    title: 'Ошибка при выборе диапазона',
    message: 'попробуйте указать конец подъезда ещё раз или выберите другую схему шахматки',
    type: 'error'
  });  
}
/* End of notifications */

const chosenSchemeName = ref('');

const chessLoading = ref(false);

const chessUpload = ref();

//const fileList = ref([]);

const handleExceed = (files) => {
  chessUpload.value.clearFiles();
  const file = files[0];
  file.uid = genFileId();
  chessUpload.value.handleStart(file);
}

const submitChessUpload = () => {
  chessUpload.value.submit();
}

const httpRequest = (params) => {
  cancelSelectingEntrance(); // cancelling current selection
  entrancesData.value = []; // clear selected entrances
  chessLoading.value = true;
  const chessUploadForm = useForm({
    operation: 'load_chess_example',
    chess: params.file
  });
  chessUploadForm.post('/chess', {
    onSuccess: () => chessLoading.value = false,
  });
}

const flatMatrix = computed(() => {
  const chosenSheme =  chessParams.filter(scheme => { 
    return scheme.name === chosenSchemeName.value; 
  });
  return chosenSheme.length ? chosenSheme[0].flatMatrix : [];
});

const schemeId = computed(() => {
  const chosenSheme = chessParams.filter(scheme => { 
    return scheme.name === chosenSchemeName.value;
   });
   return chosenSheme.length ? chosenSheme[0].id : null;
});

/* entrance selection state */
const entranceSelectionState = ref({
  selectionStarted: false,
  startPointSelected: false,
  endpointSelected: false
});

const currentEntrance = ref({
  number: 0,
  startCell: {
    row: null,
    column: null,
    columnNumber: null,
  },
  endCell: {
    row: null,
    column: null,
    columnNumber: null,
  }
});

function setCurrentEntranceInitNumber() {
  currentEntrance.value.number = entrancesData.value.length + 1;
}

/* set of all the entrances */
const entrancesData = ref([]);

/* calculate amount of floors in selection (entrance) */
const currrentEntranceTotalFloors = computed(() => {
  if (entranceSelectionState.value.startPointSelected === false || entranceSelectionState.value.endpointSelected === false) { return null; }
  const totalRowsSelected = currentEntrance.value.endCell.row - currentEntrance.value.startCell.row + 1;
  return totalRowsSelected / flatMatrix.value[0];
});
/* calculate max amount of flats on a floor in selection (entrance) */
const currentEntranceFlatsOnFloor = computed(() => {
    if (entranceSelectionState.value.startPointSelected === false || entranceSelectionState.value.endpointSelected === false) { return null; }
    return currentEntrance.value.endCell.columnNumber / flatMatrix.value[1];
});

const startSelectingEntrance = function() {
  setCurrentEntranceInitNumber();
  showSelectStartNote();
  entranceSelectionState.value.selectionStarted = true;
}

const saveSelectionAsEntrance = function() {
  //const entranceToSave = currentEntrance.value;
  const entranceToSave = unref(currentEntrance);
  entranceToSave.id = `${entranceToSave.number}-${entranceToSave.startCell.column}${entranceToSave.startCell.row}${entranceToSave.endCell.column}${entranceToSave.endCell.row}`
  entranceToSave.totalFloors = currrentEntranceTotalFloors.value;
  entranceToSave.flatsOnFloor = currentEntranceFlatsOnFloor.value;
  entrancesData.value.push(entranceToSave);
  cancelSelectingEntrance();
}

const deleteEntranceFromSelected = function (id) {
  const indx = entrancesData.value.findIndex(elem => elem.id === id);
  entrancesData.value.splice(indx, indx >= 0 ? 1 : 0);
}

const cancelSelectingEntrance = function() {
  ElNotification.closeAll();
  entranceSelectionState.value.selectionStarted = false;
  entranceSelectionState.value.startPointSelected = false;
  entranceSelectionState.value.endpointSelected = false;
  currentEntrance.value = {
    number: 0,
    startCell: {
      row: null,
      column: null,
      columnNumber: null,
    },
    endCell: {
      row: null,
      column: null,
      columnNumber: null,
    }
  }
}

const onCellSelect = (row, col, colNum) => {
  if (entranceSelectionState.value.selectionStarted === false) return

  ElNotification.closeAll(); /* close all active notifications */

  /* selecting end point of the entrance */
  if (entranceSelectionState.value.startPointSelected === true) {
    const rowsDelta = row - currentEntrance.value.startCell.row + 1;
    if ((colNum >= flatMatrix.value[1] && colNum % flatMatrix.value[1] === 0) && ((rowsDelta >= flatMatrix.value[0] && rowsDelta % flatMatrix.value[0] === 0))) {
      entranceSelectionState.value.endpointSelected = true;
      currentEntrance.value.endCell.row = row;
      currentEntrance.value.endCell.column = col;
      currentEntrance.value.endCell.columnNumber = colNum;
      showEndSelectedNote(col, row);
    } else {
      showEndSelectError();
    }
  /* selecting start point of the entrance */
  } else {
    currentEntrance.value.startCell.row = row;
    currentEntrance.value.startCell.column = col;
    currentEntrance.value.startCell.columnNumber = colNum;
    entranceSelectionState.value.startPointSelected = true;
    showStartSelectedNote(col, row);
    showSelectEndNote();
  }
}
</script>

<template>
  <div>
    <!-- chess scheme selection -->
    <el-select v-model="chosenSchemeName" clearable class="m-2" placeholder="Выберите схему шахматки" size="large">
      <el-option
        v-for="scheme in chessParams"
        :key="scheme.id"
        :label="scheme.name"
        :value="scheme.name"
      />
    </el-select>
  </div>

  <!-- chess example upload area -->
  <el-upload
    ref="chessUpload"
    action="string"
    :http-request="httpRequest"
    :auto-upload="true"
    :show-file-list="false"
  >
    <template #trigger>
      <el-button type="primary">Выбрать файл</el-button>
    </template>
    <!--
    <el-button class="ml-3" type="success" @click="submitChessUpload">
      Загрузить на сайт
    </el-button>
    -->
  </el-upload>

  <div v-loading="true" v-if="chessLoading">
  </div>
  <template v-else>
    <div v-if="flatMatrix.length" class="p-6 text-gray-900 entrances-container">
      <!-- existing/saved entrances -->
      <div 
        v-if="entrancesData.length"
        class="flex flex-wrap"
      >
        <div
          class="flex flex-col"
          v-for="entrance in entrancesData"
          :key="entrance.id"
        > 
          <div class="bg-gray-200 rounded-t-md px-1 text-right">
            <el-icon
              class="text-gray-400 hover:text-gray-600 cursor-pointer"
              size="sm"
              @click="deleteEntranceFromSelected(entrance.id)">
              <CircleClose />
            </el-icon>
          </div>
          <div
            class="flex flex-row align-middle justify-center border border-gray-200 rounded-b-md"
          >
            <div class="px-1 border-r self-center border-r-gray-200">
              <span>№ {{ entrance.number }}</span>
            </div>
            <div class="flex flex-col">
              <div class="px-1 border-b border-b-gray-200">
                <span>{{ entrance.totalFloors }}</span>
              </div>
              <div class="px-1">
                <span>{{ entrance.flatsOnFloor }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <el-affix target=".entrances-container">
        <div class="w-100 bg-white pb-4">
          <div v-if="entranceSelectionState.selectionStarted">
            <div>
              <span>Подъезд № </span>
              <el-input-number v-model="currentEntrance.number" :min="1" :max="30" />
            </div>
            <div>
              <span>Начало подъезда: </span>
              <span v-if="entranceSelectionState.startPointSelected">ячейка <strong>{{ currentEntrance.startCell.column }}{{ currentEntrance.startCell.row }}</strong></span>
              <span v-else>не выбрано</span>
            </div>
            <div>
              <span>Конец подъезда: </span>
              <span v-if="entranceSelectionState.endpointSelected">ячейка <strong>{{ currentEntrance.endCell.column }}{{ currentEntrance.endCell.row }}</strong></span>
              <span v-else>не выбрано</span>
            </div>
            <div v-if="entranceSelectionState.endpointSelected && entranceSelectionState.startPointSelected">
              <span>Параметры выделенного подъезда: этажей - <strong>{{ currrentEntranceTotalFloors }}</strong>, квартир на этаже - до <strong>{{ currentEntranceFlatsOnFloor }}</strong></span>
            </div>
            <div>
              <el-button
                :disabled="entranceSelectionState.startPointSelected === false || entranceSelectionState.endpointSelected === false"
                @click="saveSelectionAsEntrance"
              >
                <el-icon class="pr-1">
                  <Select />
                </el-icon>
                Сохранить
              </el-button>
                <el-popconfirm
                  width="250px"
                  confirm-button-text="Да"
                  cancel-button-text="Нет"
                  :icon="InfoFilled"
                  icon-color="#626AEF"
                  title="Текущее выделение будет потеряно. Вы уверены?"
                  @confirm="cancelSelectingEntrance"
                >
                  <template #reference>
                    <el-button
                    >
                      <el-icon class="pr-1">
                        <Close />
                      </el-icon>
                      Отменить
                    </el-button>
                  </template>
                </el-popconfirm>
            </div>
          </div>

          <el-button
            v-else
            @click="startSelectingEntrance"
          >
            <el-icon class="pr-1">
              <Plus />
            </el-icon>
            Подъезд
          </el-button>
        </div>
      </el-affix>
      
      <!-- chess-table container -->
      <div class="w-100 overflow-auto">
        <div v-for="row of chessData" :key="row" class="flex">
          <div
            class="table-cell p-1 border border-gray-200 whitespace-nowrap overflow-visible"
            :class="[
              //`bg-[#${cell.bgColor1}]`,
              entranceSelectionState.selectionStarted ? 'hover:bg-slate-300' : '',
              cell.borders.right !== 'none' ? 'border-r-black' : '',
              cell.borders.left !== 'none' ? 'border-l-black' : '',
              cell.borders.top !== 'none' ? 'border-t-black' : '',
              cell.borders.bottom !== 'none' ? 'border-b-black' : '',
              entranceSelectionState.selectionStarted === false ? 'select-none' : 'cursor-pointer',
              entranceSelectionState.startPointSelected
              && entranceSelectionState.endpointSelected
              && cell.row <= currentEntrance.endCell.row
              && cell.row >= currentEntrance.startCell.row
              && cell.columnNumber <= currentEntrance.endCell.columnNumber
              && cell.columnNumber >= currentEntrance.startCell.columnNumber
              ? 'bg-slate-200' : '',
              entranceSelectionState.startPointSelected
              && cell.row === currentEntrance.startCell.row
              && cell.columnNumber === currentEntrance.startCell.columnNumber
              ? 'border-t-8 border-t-slate-600 border-l-8 border-l-slate-600 bg-slate-200': '',
              entranceSelectionState.endpointSelected
              && cell.row === currentEntrance.endCell.row
              && cell.columnNumber === currentEntrance.endCell.columnNumber
              ? 'border-b-8 border-b-slate-600 border-r-8 border-r-slate-600 bg-slate-200': '',
            ]"
            v-for="cell of row"
            :key="cell.address"
            @click="onCellSelect(cell.row, cell.column, cell.columnNumber)"
          >
            {{ cell.rawValue }}
          </div>
        </div>
      </div>

    </div>
  </template>

</template>

<style scoped>
.table-cell {
  width: 70px;
  min-width: 70px;
  font-size: 11px;
}
</style>