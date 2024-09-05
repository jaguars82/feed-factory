<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { ElNotification } from 'element-plus';

const props = defineProps({
  chesses: {
    type: Array,
  }
});

const dialogDeleteVisible = ref(false);
const chessForDelete = ref(null);

function askForDelete(chessId) {
  const chess = props.chesses.find(chess => chess.id === chessId);
  chessForDelete.value = chess;
  dialogDeleteVisible.value = true;
  // console.log(chessForDelete.value);
}

function closeDeleteDialog() {
  dialogDeleteVisible.value = false;
  chessForDelete.value = null;
}

function deleteChess(chessId) {
  closeDeleteDialog();
  Inertia.delete(`/chess/delete/${chessId}`, {
    preserveScroll: true,
    onSuccess: () => {
      ElNotification({
        title: 'Успех!',
        message: 'Шахматка удалена',
        type: 'success'
      });
    },
    onError: () => {
      ElNotification({
        title: 'Ошибка',
        message: 'Что-то пошло не так...',
        type: 'error'
      });
    }
  });
}

</script>

<template>
  <Head title="Шахматки" />

  <AuthenticatedLayout>
      
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Шахматки
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-sm sm:rounded-lg">

          <div class="mb-4">
            <Link :href="route('chess.add')">
              <el-button type="primary">Добавить шахматку</el-button>
            </Link>
          </div>

          <el-table :data="chesses" empty-text="Нет шахматок">
            <el-table-column prop="name" label="Название" />
            <el-table-column prop="complex_alias" label="ЖК" />
            <el-table-column prop="building_alias" label="Позиция" />
            <el-table-column align="right">
              <template #default="scope">
                <el-button type="danger" circle @click="askForDelete(scope.row.id)">
                  <el-icon style="vertical-align: middle">
                    <Delete />
                  </el-icon>
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>

    <!-- Confirm chess deletion dialog -->
    <el-dialog
      v-model="dialogDeleteVisible"
      title="Подтвердите удаление"
      width="500"
      :before-close="handleClose"
    >
      <span v-if="chessForDelete !== null">Вы действительно хотите удалить шахматку {{ chessForDelete.name }}?</span>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="closeDeleteDialog">Отмена</el-button>
          <el-button v-if="chessForDelete !== null" type="primary" @click="deleteChess(chessForDelete.id)">
            Удалить
          </el-button>
        </div>
      </template>
    </el-dialog>

  </AuthenticatedLayout>
</template>