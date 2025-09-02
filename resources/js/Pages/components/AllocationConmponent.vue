<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import ItemTimeAllocationComponent from './ItemTimeAllocationComponent.vue'
import ButtonSaveComponent from './ButtonSaveComponent.vue'
import ButtonEmailComponent from './ButtonEmailComponent.vue'

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

interface SelectedRowData {
  employeeId: number
  name: string
  supervisor: string
  timeAllocations: TimeAllocation[]
  monthlyTotal?: MonthlyTotal | null
}

// 2. Props avec validation et valeurs par défaut
const props = defineProps<{
  selectedRow: SelectedRowData | null,
  staffId: string | null,
}>()

// 3. Émission d'événements typés
const emit = defineEmits<{
  'update:selectedRow': [value: SelectedRowData | null]
  'close': []
  'save': [data: SelectedRowData]
  'delete': [employeeId: number, allocationId: number]
  'sendEmail': [employeeId: number]
}>()

// 4. État local réactif
const loading = ref(false)
const selectId = ref<number | null>(null)

// 5. Computed pour éviter la mutation directe des props
const localSelectedRow = ref<SelectedRowData | null>([])
const employee = ref<number | null>(null)
// computed({
//   get: () => props.selectedRow,
//   set: (value) => emit('update:selectedRow', value)
// })

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

const excludedFields = ['id', 'agreement', 'bus']
const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec']

// 7. Template par défaut pour les nouvelles allocations
const createInitialAllocation = (): TimeAllocation => ({
  id: null,
  agreement: '',
  bus: '',
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

const handleGetTimeAllocations = async (staffId: string) => {
  if (staffId) {
    loading.value = true
   window.axios.get('/staff/time-allocations/' + staffId)
    .then(response => {
        const { time_allocations, ...employeeInfo } = response.data.data
        localSelectedRow.value = time_allocations;
        employee.value = employeeInfo
    })
    .catch(error => {
        alert(error.response?.data?.message || error.message)
        console.error('Error saving allocations:', error.response?.data || error.message)
    })
    .finally(() => {
        loading.value = false
    })
  }
}

    // 8. Watchers pour réagir aux changements de props
    // watch(
    // () => props.selectedRow?.timeAllocations,
    // (newAllocations) => {
    //     if (newAllocations && localSelectedRow.value) {
    //     // Recalculer les totaux quand les allocations changent
    //     localSelectedRow.value.monthlyTotal = calculateMonthlyTotals(newAllocations)
    //     }
    // },
    // { deep: true }
    // )

    watch(() => props.staffId, async (newId) => {
    if (newId) {
        handleGetTimeAllocations(newId)
    }
    }, { immediate: true })

    // 9. Fonctions utilitaires
    const calculateRowTotal = (allocation: TimeAllocation): number => {
    return months.reduce((total, month) => {
        const value = Number(allocation[month as keyof TimeAllocation]) || 0
        return total + value
    }, 0)
    }

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

    const getTotalForAllAllocations = (allocations: TimeAllocation[]): number => {
    return allocations?.reduce((total, allocation) => {
        return total + calculateRowTotal(allocation)
    }, 0)
    }

    // 10. Gestionnaires d'événements

    const closeModal = () => {
    emit('close')
    localSelectedRow.value = null
    }

    const addRow = () => {
    if (!localSelectedRow.value) return
    localSelectedRow.value.unshift(createInitialAllocation())
    
    if (localSelectedRow.value.length === 1) {
        localSelectedRow.value.monthlyTotal = createInitialMonthlyTotal()
    }
    }

    const removeItem = (index: number) => {
    if (!localSelectedRow.value) return
    localSelectedRow.value.splice(index, 1)
    }


    function handleSaveTimeAllocations(callback: Function) {
        if (localSelectedRow.value.length === 0) {
            alert("Please add a time allocation before submitting.");
            callback(true)
            return false
        }
        if (!confirm('Are you sure you want to save these changes?')) {
            callback(true)

            return false;
        }
        let items = {...localSelectedRow.value}  
        
        window.axios.post('/time-allocations', {
            employeeId: employee.value.employeeId,
            timeAllocations: items,
            monthlyTimeTotal : items.monthlyTotal
        })
        .then(async(response) => {
            await handleGetTimeAllocations(employee.value.employeeId)
            alert(response?.data?.message || response?.message)
        })
        .catch(error => {
            alert( error.response?.data?.message || error.message)
            console.error('Error saving allocations:', error.response?.data || error.message)
        })
        .finally(() => {
            callback(true)
        })
    }

    function handleDeleteTimeAllocation(id,callback?: Function) {
        if (!confirm('Are you sure you want to delete these changes?')) {
            callback(true)
            return;
        }
        selectId.value =id
        axios.post('/time-delete',
            {
            employeeId: employee.value.employeeId,
            id : id
        }
        )
        .then(async(response) => {
            await handleGetTimeAllocations(employee.value.employeeId)
            alert('Allocations delete successfully')

        })
        .catch(error => {
            alert(error.response?.data?.message || error.message)
            console.error('Error saving allocations:', error.response?.data || error.message)
        })
        .finally(() => {
            callback(true)
        })
    }

    
    function handleSendEmail(callback) {
        if (localSelectedRow.value.length === 0) {
            alert("Please add a time allocation before submitting.");
            callback(true)
            return false
        }

        if (getTotalForAllAllocations(localSelectedRow.value) !=1200) {
            alert("⚠️ The total allocations must add up to exactly 1200.Please check your inputs before sending the email.")
            callback(true)
            return false
        }
        if (!confirm('Do you really want to send this email?')) {
            callback(true)
            return;
        }
        axios.post('/send-mail', {
            employeeId: employee.value.employeeId,
        })
        .then(response => {
            alert(response?.data?.message || response?.message)
        })
        .catch(error => {
            alert('Error send allocations')
        })
        .finally(() => {
            callback(true)
        })
    }

// 11. Fonctions d'affichage
const calculateTotalMonth = (allocations: TimeAllocation[], key: string): string | number => {
  if (excludedFields.includes(key)) return ''
  return allocations.reduce((acc, val) => {
    const value = Number(val[key as keyof TimeAllocation]) || 0
    return acc + value
  }, 0)
}


const getLabelText = (label: string, key: string): string => {
  const nonPercentageKeys = ['agreement', 'bus']
  return nonPercentageKeys.includes(key) ? '' : `${label}:`
}


const getColorClass = (value: number, key: string): string => {
  const threshold = key === 'total' ? 1200 : 100
  
  if (value < threshold) return 'bg-blue-500'
  if (value > threshold) return 'bg-red-500'
  return 'bg-green-300'
}
</script>

<template>
  <div v-if="staffId" 
       @click.self="closeModal"
       class="fixed p-10 w-full z-40 inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center">
    
    <div  class="bg-white flex flex-col w-[100%] p-3 px-5 rounded-xl shadow-xl overflow-hidden max-h-[90vh]">
      <!-- Header -->
       <!-- <pre>{{ selectedRow }}</pre> -->
        <template v-if="loading">
            <div class="w-full py-10 text-center text-lg font-bold">
                Loading...
            </div>
        </template>
        <template v-else>

            <div class="w-full relative">
        <div class="w-full flex items-center gap-2 text-[14px] py-3">
          <h2 class="font-semibold mb-4 uppercase">
            Staff: <span class="capitalize text-gray-600">{{ selectedRow?.name +' '+ selectedRow?.lastName }}</span>
          </h2>
          <h2 class="font-semibold mb-4 uppercase">
            Supervisor: <span class="capitalize text-gray-600">{{ selectedRow?.supervisor_name }}</span>
          </h2>
          <h2 class="font-semibold mb-4 uppercase">
            Year: <span class="capitalize text-gray-600">{{ new Date().getFullYear() }}</span>
          </h2>
        </div>

        <button type="button" @click="closeModal"
                class="p-2 absolute top-0 right-2 text-2xl cursor-pointer">
          <i class="uil uil-times"></i>
        </button>

        <button v-if="localSelectedRow?.length > 0" 
                @click="addRow" 
                class="p-1 px-2 mb-2 bg-green-500 text-white cursor-pointer rounded hover:bg-green-400">
          + Add new 
        </button>

        <!-- Totals Display -->
        <div class="w-full flex items-center gap-2" v-if="localSelectedRow">
          <div class="borders flex gap-2 borders-gray-200 w-full">
            <div class="flex w-full items-start p-2 rounded-md cursor-pointer">
              <div class="w-full flex items-center justify-center gap-1 text-[11px]"  
                v-for="(label, key) in headerConfig" :key="key">
                <span class="font-bold">{{ getLabelText(label, key) }}</span>
                                   
                <transition name="fade-slide" mode="out-in">
                  <span :class="[getColorClass(calculateTotalMonth(localSelectedRow, key), key), 'px-1 font-medium']"
                        :key="`${key}`">
                    {{ calculateTotalMonth(localSelectedRow, key) }}
                  </span>
                </transition>
              </div>
            <!-- <div class="w-full flex items-center justify-center gap-1 text-[11px]">
                <span class="font-bold">Total:</span>
                <transition name="fade-slide" mode="out-in">
                  <span :class="[getColorClass(getTotalForAllAllocations(localSelectedRow?.timeAllocations), 'total'), 'px-1 font-medium']"
                        :key="getTotalForAllAllocations(localSelectedRow?.timeAllocations)">
                    {{ getTotalForAllAllocations(localSelectedRow?.timeAllocations) }}
                  </span>
                </transition>
              </div> -->
            </div>
          </div>
        </div>
      </div>

      <!-- Time Allocations -->
    <!-- <pre>  {{ localSelectedRow }}</pre> -->
      <div class="flex-1 overflow-y-auto" v-if="localSelectedRow?.length > 0">
          <ItemTimeAllocationComponent
            v-for="(allocation, index) in localSelectedRow" :key="allocation.id || index"
            :allocation="allocation"
            @removeItem="removeItem(index)"
            @delete="handleDeleteTimeAllocation(allocation.id,index)"
       />
      </div>

      <!-- No data state -->
      <div v-else class="text-center flex flex-col items-center gap-2 mt-2 font-medium">
        <span>No Data</span>
        <button @click="addRow" 
                class="p-1 px-2 mb-2 max-w-max border border-green-500 text-green-500 cursor-pointer rounded">
          + Add new
        </button>
      </div>

      <!-- Footer buttons -->
      <div class="w-full">
        <div class="mt-3 flex justify-end gap-2">
          <button class="px-3 p-1 cursor-pointer bg-gray-100 border border-gray-300 rounded hover:bg-gray-400" 
                  @click="closeModal">
            Cancel
          </button>
          
         <ButtonSaveComponent
         @save="handleSaveTimeAllocations"
         />
          <ButtonEmailComponent
          @save="handleSendEmail"
          />
         
        </div>
      </div>
        </template>
    </div>
  </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>