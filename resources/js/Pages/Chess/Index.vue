<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { ElNotification } from 'element-plus';

const props = defineProps({
  chesses: {
    type: Array,
  }
});

const dialogFileRenameVisible = ref(false);
const chessForFileRename = ref(null);

const fileRenameForm = useForm({
  id: null,
  attachment_filename: null,
});

function openFileRenameDialog(chessId) {
  const chess = props.chesses.find(chess => chess.id === chessId);
  chessForFileRename.value = chess;
  fileRenameForm.id = chess.id;
  fileRenameForm.attachment_filename = chess.attachment_filename;
  dialogFileRenameVisible.value = true;
}

function closeFileRenameDialog() {
  dialogFileRenameVisible.value = false;
  chessForFileRename.value = null;
}

function renameFile(chessId) {
  closeFileRenameDialog();
  Inertia.post(`/chess/rename-file/${chessId}`, {id: fileRenameForm.id, name: fileRenameForm.attachment_filename}, {
    preserveScroll: true,
    onSuccess: () => {
      ElNotification({
        title: 'Успех!',
        message: 'Файл для обновления шахматки переименован',
        type: 'success'
      });
    },
    onError: () => {
      ElNotification({
        title: 'Ошибка',
        message: 'Не удалось переимновать файл',
        type: 'error'
      });
    }
  });
}

function updateStatus (id, newStatus) {
  Inertia.post(`/chess/set-status/${id}`, {id: id, status: newStatus}, {
    preserveScroll: true,
    onSuccess: () => {
      ElNotification({
        title: newStatus == 1 ? 'Шахматка активирована' : 'Шахматка деактивирована',
        message: newStatus == 1 ? 'Она будет задействована в обновлениях' : 'Она больше не будет задействована в обновлениях',
        type: 'success'
      });
    },
    onError: () => {
      ElNotification({
        title: 'Ошибка',
        message: 'Не удалось переимновать файл',
        type: 'error'
      });
    }
  });
}

const dialogDeleteVisible = ref(false);
const chessForDelete = ref(null);

function askForDelete(chessId) {
  const chess = props.chesses.find(chess => chess.id === chessId);
  chessForDelete.value = chess;
  dialogDeleteVisible.value = true;
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
            <el-table-column label="Имя файла для обновления">
              <template #default="scope">
                <el-button class="mr-2" circle @click="openFileRenameDialog(scope.row.id)">
                  <el-icon style="vertical-align: middle">
                    <Edit />
                  </el-icon>
                </el-button>
                <span>{{ scope.row.attachment_filename }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Активна" align="center">
              <template #default="scope">
                <el-switch
                  :model-value="scope.row.is_active"
                  :active-value="1"
                  :inactive-value="0"
                  @change="(newStatus) => updateStatus(scope.row.id, newStatus)"
                />
              </template>
            </el-table-column>
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

    <!-- File rename dialog -->
    <el-dialog
      v-model="dialogFileRenameVisible"
      title="Изменение имени файла для обновления шахматки"
      width="500"
    >
      <div v-if="chessForFileRename !== null">
        <el-form :model="fileRenameForm" label-width="180px">
          <el-form-item label="Имя файла">
            <el-input v-model="fileRenameForm.attachment_filename" />
          </el-form-item>
        </el-form>
      </div>

      <template #footer>
        <div class="dialog-footer">
          <el-button @click="closeFileRenameDialog">Отмена</el-button>
          <el-button v-if="chessForFileRename !== null" type="primary" @click="renameFile(chessForFileRename.id)" :disabled="!fileRenameForm.attachment_filename">
            Переименовать
          </el-button>
        </div>
      </template>
    </el-dialog>

    <!-- Confirm chess deletion dialog -->
    <el-dialog
      v-model="dialogDeleteVisible"
      title="Подтвердите удаление"
      width="500"
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