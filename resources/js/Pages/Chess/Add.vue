<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { ref, unref, reactive, computed } from 'vue';
import { ElNotification } from 'element-plus';
import { InfoFilled } from '@element-plus/icons-vue';

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

const flatMatrix = [7, 3]; // [rows, columns]

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
  return totalRowsSelected / flatMatrix[0];
});
/* calculate max amount of flats on a floor in selection (entrance) */
const currentEntranceFlatsOnFloor = computed(() => {
    if (entranceSelectionState.value.startPointSelected === false || entranceSelectionState.value.endpointSelected === false) { return null; }
    return currentEntrance.value.endCell.columnNumber / flatMatrix[1];
});

const startSelectingEntrance = function() {
  setCurrentEntranceInitNumber();
  showSelectStartNote();
  entranceSelectionState.value.selectionStarted = true;
}

const saveSelectionAsEntrance = function() {
  //const entranceToSave = currentEntrance.value;
  const entranceToSave = unref(currentEntrance);
  entranceToSave.totalFloors = currrentEntranceTotalFloors.value;
  entranceToSave.flatsOnFloor = currentEntranceFlatsOnFloor.value;
  entrancesData.value.push(entranceToSave);
  cancelSelectingEntrance();
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
    if ((colNum >= flatMatrix[1] && colNum % flatMatrix[1] === 0) && ((rowsDelta >= flatMatrix[0] && rowsDelta % flatMatrix[0] === 0))) {
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
          <div class="p-6 text-gray-900 entrances-container">
            
            <!-- existing/saved entrances -->
            <div 
              v-if="entrancesData.length"
              class="flex flex-wrap"
            >
              <div
                class="flex flex-row align-middle justify-center m-1 border border-gray-200 rounded-sm"
                v-for="entrance in entrancesData"
                :key="entrance.number"
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
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.table-cell {
  width: 70px;
  min-width: 70px;
  font-size: 11px;
}
</style>