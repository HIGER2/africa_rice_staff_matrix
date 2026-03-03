<script setup lang="ts">
import EmployeeComponent from './components/EmployeeComponent.vue';

const props = defineProps({
  staff: Array,
  statistics: Array
})
</script>


<template>
<EmployeeComponent v-bind="$props" />     <!-- <TableTwo/> -->
      <!-- {{ staff }} -->
</template>