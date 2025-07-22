<script setup lang="ts">
import TableOne from './components/TableOne.vue';
import TableTwo from './components/TableTwo.vue';

const props = defineProps({
  staff: Array,
})
</script>


<template>
    <TableOne :staff="staff"/>
     <!-- <TableTwo/> -->
      <!-- {{ staff }} -->
</template>