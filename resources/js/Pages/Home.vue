<script setup lang="ts">
import EmployeeComponent from './components/EmployeeComponent.vue';

const props = defineProps({
  staff: Array,
})
</script>


<template>
    <EmployeeComponent :staff="staff"/>
     <!-- <TableTwo/> -->
      <!-- {{ staff }} -->
</template>