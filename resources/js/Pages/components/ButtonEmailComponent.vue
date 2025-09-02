

<script lang="ts" setup>
import { ref } from 'vue';
const emit = defineEmits<{
  'save': any // Émission simple
}>()

const loading =ref(false)

const handleSave = async () => {
  loading.value = true
  emit('save',(finish) => {
    if (finish) {
        loading.value = false
    } 
  })
}
</script>



<template>
   <button class="px-3 py-1 cursor-pointer border-0 bg-blue-600 text-white rounded hover:bg-blue-700" 
    @click="handleSave">
    <div v-if="loading" class="flex items-center gap-1">
        <div class="w-5 h-5 border-5 border-white border-t-transparent rounded-full animate-spin"></div>
        Processing...
    </div>
    <span v-else>Send email</span>
</button>
</template>

<style>

</style>