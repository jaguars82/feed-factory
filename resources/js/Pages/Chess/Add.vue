<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { ElNotification } from 'element-plus';

defineProps({
  chessData: {
    type: Array
  }
});

const flatMatrix = [6, 3]; // [rows, columns]

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

const entrancesData = ref([]);

/* notification */
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
    message: 'попробуйте указать конец подъезда ещё раз',
    type: 'error'
  });  
}
/* End of notifications */

const startSelectingEntrance = function() {
  showSelectStartNote();
  entranceSelectionState.value.selectionStarted = true;
}

const onCellSelect = (row, col, colNum) => {
  if (entranceSelectionState.value.selectionStarted === false) return

  ElNotification.closeAll(); /* close all active notifications */

  if (entranceSelectionState.value.startPointSelected === true) {
    if ((col >= flatMatrix[1] && col % flatMatrix[1] === 0) && ((row >= flatMatrix[0] && row % flatMatrix[0] === 0))) {
      entranceSelectionState.value.endpointSelected = true;
      showEndSelectedNote(col, row);
    } else {
      showEndSelectError();
      console.log(`выбран ряд ${row}, колонка ${colNum}`);
      console.log(`в квртире рядов ${flatMatrix[0]}, колонок ${flatMatrix[1]}`);
    }

  } else {
    currentEntrance.value.startCell.row = row;
    currentEntrance.value.startCell.column = col;
    currentEntrance.value.startCell.columnNumber = colNum;
    entranceSelectionState.value.startPointSelected = true;
    showStartSelectedNote(col, row);
    showSelectEndNote();
  }

    console.log(col)
    console.log(row)
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
        <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">

            <div>
              <el-button
                @click="startSelectingEntrance"
              >
                <el-icon class="pr-1">
                  <Plus />
                </el-icon>
                Подъезд
              </el-button>

              <div v-if="entranceSelectionState.selectionStarted">
                <div>
                  <span>Начало подъезда:</span>
                  <span v-if="entranceSelectionState.startPointSelected">выбрано</span>
                  <span v-else>не выбрано</span>
                </div>
                <div>
                  <el-button>
                    <el-icon class="pr-1">
                      <Select />
                    </el-icon>
                    Сохранить
                  </el-button>
                  <el-button>
                    <el-icon class="pr-1">
                      <Close />
                    </el-icon>
                    Отменить
                  </el-button>
                </div>
              </div>

            </div>
            
            <div v-for="row of chessData" :key="row" class="flex">
              <div
                class="table-cell p-1 border border-gray-200 whitespace-nowrap overflow-visible"
                :class="[
                  `bg-[#${cell.bgColor1}]`,
                  cell.borders.right !== 'none' ? 'border-r-black' : '',
                  cell.borders.left !== 'none' ? 'border-l-black' : '',
                  cell.borders.top !== 'none' ? 'border-t-black' : '',
                  cell.borders.bottom !== 'none' ? 'border-b-black' : '',
                  entranceSelectionState.selectionStarted === false ? 'select-none' : 'cursor-pointer'
                ]"
                v-for="cell of row"
                :key="cell.address"
                @click="onCellSelect(cell.row, cell.column, cell.columnNumber)"
              >
                {{ cell.rawValue }}
              </div>
            </div>

            {{ chessData }}

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