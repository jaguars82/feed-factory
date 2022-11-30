<script setup>

</script>

<template>
  <el-form :model="form" label-width="120px">
    <el-form-item label="Провайдер фида">
      <el-select v-model="form.provider_id" placeholder="Выберите провайдера">
        <el-option label="Zone one" value="shanghai" />
        <el-option label="Zone two" value="beijing" />
      </el-select>
    </el-form-item>
  </el-form>
</template>