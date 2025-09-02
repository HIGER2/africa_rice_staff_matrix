
<script lang="ts" setup>
import { ref } from 'vue';
import { computed } from 'vue';

// 1. Définition des props avec validation complète
interface TimeAllocation {
  id?: number | null
  agreement: string | number
  bus: string | number
  jan: number
  feb: number
  mar: number
  apr: number
  may: number
  jun: number
  jul: number
  aug: number
  sep: number
  oct: number
  nov: number
  dec: number
  total: number
}

interface MonthlyTotal {
  id?: number | null
  jan: number
  feb: number
  mar: number
  apr: number
  may: number
  jun: number
  jul: number
  aug: number
  sep: number
  oct: number
  nov: number
  dec: number
  total: number
}
const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec']
let  loading = ref(false)

// 2. Props avec validation et valeurs par défaut
const props = defineProps<{
  allocation: any | null
}>()

// 3. Émission d'événements typés
const emit = defineEmits<{
  'update:allocation': any,
  'removeItem': number,
  'save': any,
  'delete': [employeeId: number, allocationId: number]
  'sendEmail': [employeeId: number]
}>()


const  removeItem = () => {
    emit('removeItem')
}



const  deleteRow = () => {
    loading.value = true
    emit('delete',(finish) => {
      if (finish) {
          loading.value = false
      } 
    })
}

// 5. Computed pour éviter la mutation directe des props
const localAllocation = computed({
  get: () => props.allocation,
  set: (value) => emit('update:allocation', value)
})

const createInitialMonthlyTotal = (): MonthlyTotal => ({
  id: null,
  jan: 0,
  feb: 0,
  mar: 0,
  apr: 0,
  may: 0,
  jun: 0,
  jul: 0,
  aug: 0,
  sep: 0,
  oct: 0,
  nov: 0,
  dec: 0,
  total: 0,
})


const calculateMonthlyTotals = (allocations: TimeAllocation[]): MonthlyTotal => {
  const result = createInitialMonthlyTotal()
  
  months.forEach(month => {
    result[month as keyof MonthlyTotal] = allocations.reduce((sum, item) => {
      return sum + (Number(item[month as keyof TimeAllocation]) || 0)
    }, 0)
  })
  
  result.total = months.reduce((total, month) => {
    return total + (result[month as keyof MonthlyTotal] as number)
  }, 0)
  
  return result
}

// 9. Fonctions utilitaires
const calculateRowTotal = (allocation: TimeAllocation): number => {
  return months.reduce((total, month) => {
    const value = Number(allocation[month as keyof TimeAllocation]) || 0
    return total + value
  }, 0)
}


// 6. Variables de configuration (déplacées hors de la logique)
const headerConfig = {
  agreement: 'AGREEMENT',
  bus: 'BUS',
  jan: 'JAN',
  feb: 'FEB', // Correction: était 'FB'
  mar: 'MAR',
  apr: 'APR',
  may: 'MAY',
  jun: 'JUN',
  jul: 'JUL',
  aug: 'AUG',
  sep: 'SEP',
  oct: 'OCT',
  nov: 'NOV',
  dec: 'DEC',
  total: 'Total',
}

const excludedFields = ['id', 'total', 'agreement', 'bus']


const getRowTotal = (value: string | number, index: number) => {
    localAllocation.value.total = calculateRowTotal(localAllocation.value)
}

const getProgressWidth = (key: string, value: number): string => {
  if (excludedFields.includes(key)) return '0'
  return value ? `${value > 100 ? 100 : value}%` : '0'
}

const getColorClass = (value: number, key: string): string => {
  const threshold = key === 'total' ? 1200 : 100
  if (value < threshold) return 'bg-blue-500'
  if (value > threshold) return 'bg-red-500'
  return 'bg-green-300'
}
</script>

<template>
        <div class="w-full" v-if="allocation">
            <!-- {{ loading }} -->
            <!-- {{ localAllocation }} -->
          <div class="borders flex items-center gap-2 borders-gray-200 group w-full">
            <div :class="[
              'flex w-full items-start p-2 transition-all duration-75 rounded-md cursor-pointer mb-2 hover:bg-blue-50',
              !allocation.id ? 'bg-red-50' : 'bg-gray-100',
              loading ? 'bg-red-300' : ''
            ]">
              <div class="w-full text-center" v-for="(label, key) in headerConfig" :key="key">
                <label class="block mb-2 text-[12px] font-medium lowercase text-gray-700">{{ label }}</label>
                <div class="relative w-full h-8">
                  <!-- Progress background -->
                  <div class="absolute inset-0 bg-green-300 transition-all duration-200 rounded"
                       :style="{ width: getProgressWidth(key, allocation[key as keyof TimeAllocation] as number), zIndex: 0 }">
                  </div>
                  
                  <!-- Input field -->
                  <input :type="(key === 'agreement' || key === 'bus') ? 'text' : 'number'"
                         min="0"
                         max="100"
                         :disabled="key === 'total' || loading"
                         :class="{ 'bg-yellow-400': key === 'total' }"
                         @input="() => getRowTotal(allocation[key as keyof TimeAllocation] as string, index)"
                         v-model="allocation[key as keyof TimeAllocation]"
                         class="relative z-10 w-full h-full border text-center text-sm border-gray-300 focus:outline-none focus:ring-2 focus:rounded-md focus:ring-green-500 focus:border-green-500" />
                </div>
                <transition name="fade-slide" mode="out-in">
                  <span v-if="!excludedFields.includes(key)"
                        :key="allocation[key as keyof TimeAllocation]"
                        class="text-[11px] font-bold block">
                    {{ allocation[key as keyof TimeAllocation] }}%
                  </span>
                </transition>
              </div>
            </div>

            <!-- Action buttons -->
            <div>
              <button v-if="!allocation.id" 
                @click="removeItem()"
                type="button" 
                class="p-1 cursor-pointer">×</button
            >
              <button v-else
                      :class="'cursor-pointer group-hover:inline-block hidden'"
                      @click="deleteRow" 
                      class="p-1 cursor-pointer">
                <span class="text-red-500"><i class="uil uil-trash-alt"></i></span>
              </button>
            </div>
          </div>
        </div>
</template>


<style>

</style>