<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconLinkButton from '@/Components/IconLinkButton.vue'
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>
  <Head title="Панель управления" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Панель управления
      </h2>
    </template>

    <div class="py-12">
      <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="my-2 mx-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <p class="text-center text-lg mb-2 uppercase">Действия</p>
            <div class="flex flex-wrap">
              <IconLinkButton :href="route('feed.index')" text="Управление фидами" icon="list-check" />
              <IconLinkButton :href="route('chess.add')" text="Добавить объект (шахматку)" icon="table" />
              <IconLinkButton :href="route('chess.index')" text="Управление шахматками" icon="th" />
            </div>
          </div>
        </div>

        <div class="my-2 mx-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <p class="text-center text-lg mb-2 uppercase">База</p>
            <div class="flex flex-wrap">
              <IconLinkButton :href="route('provider.index')" text="Провайдеры фидов" icon="briefcase" />
              <IconLinkButton :href="route('transport.index')" text="Транспорт" icon="envelope" />
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
