


 <script setup>
    import { Inertia } from '@inertiajs/inertia'
import { onMounted, reactive, ref, watch } from 'vue'
    const props = defineProps({
    staff: Array,
    })
    let staffData = ref([])
    const originalStaffData = ref([])
    let loading = ref(false)
    let search =ref('')
    const exClude=['id','employeeId','agreement','bus','year','date','total']
    // Clé-valeur : clé technique + nom affiché
    const headers = {
    resno: 'RESNO',
    name: 'NAME',
    position: 'POSITION',
    grade_level: 'GRADE/LEVEL',
    grade: 'GRADE',
    organization: 'ORGANIZATION',
    country: 'COUNTRY',
    base_station: 'BASE STATION',
    division: 'DIVISION',
    unit_program: 'UNIT/PROGRAM',
    supervisor: 'SUPERVISOR',
    }
    const headerMorth={
        agreement: 'AGREEMENT',
        bus: 'BUS',
        jan: 'JAN',
        feb: 'FB',
        apr: 'APR',
        mar: 'MAR',
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

    const morth={
        jan: 'JAN',
        feb: 'FEB',
        apr: 'APR',
        mar: 'MAR',
        may: 'MAY',
        jun: 'JUN',
        jul: 'JUL',
        aug: 'AUG',
        sep: 'SEP',
        oct: 'OCT',
        nov: 'NOV',
        dec: 'DEC',
       
    }
    const content = {
    agreement: 'AGREEMENT',
    bus: 'BUS',
    jan: 'JAN',
    feb: 'FEB',
    apr: 'APR',
    mar: 'MAR',
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
    
    const initial = {
            id:null,
            agreement: 0,
            bus: 0,
            jan: 0,
            feb: 0,
            apr: 0,
            mar: 0,
            may: 0,
            jun: 0,
            jul: 0,
            aug: 0,
            sep: 0,
            oct: 0,
            nov: 0,
            dec: 0,
            total: 0,
        }
    //     [
    //     // {
    //     // agreement: '876587687',
    //     // bus: '8876876',
    //     // jan: '1',
    //     // feb: '2',
    //     // apr: '2',
    //     // mar: '3',
    //     // may: '4',
    //     // jun: '5',
    //     // jul: '6',
    //     // aug: '7',
    //     // sep: '3',
    //     // oct: '3',
    //     // nov: '3',
    //     // dec: '3',
    //     // total: '100',
    //     // year:'2025'
    //     // },
    //     // {
    //     // agreement: '876587687',
    //     // bus: '8876876',
    //     // jan: '1',
    //     // feb: '2',
    //     // apr: '2',
    //     // mar: '3',
    //     // may: '4',
    //     // jun: '5',
    //     // jul: '6',
    //     // aug: '7',
    //     // sep: '3',
    //     // oct: '3',
    //     // nov: '3',
    //     // dec: '3',
    //     // total: '100',
    //     // },
    //     // {
    //     // agreement: '876587687',
    //     // bus: '8876876',
    //     // jan: '1',
    //     // feb: '2',
    //     // apr: '2',
    //     // mar: '3',
    //     // may: '4',
    //     // jun: '5',
    //     // jul: '6',
    //     // aug: '7',
    //     // sep: '3',
    //     // oct: '3',
    //     // nov: '3',
    //     // dec: '3',
    //     // total: '100',
    //     // }
    // ]

    let selectedRow = ref(null)
    
    const getRowTotal = (val,index) => {
    const total =  Object
        .keys(selectedRow.value.timeAllocations[index])
        .filter((key)=>{
            if (!exClude.includes(key)) {
                return key
            }
        })
        .reduce((acc, value) => {
            if (selectedRow.value.timeAllocations[index][value]) {
                acc +=selectedRow.value.timeAllocations[index][value]
            }
            return acc;
        }, 0);
        
        selectedRow.value.timeAllocations[index].total = total
        console.log(selectedRow.value.timeAllocations[index]);
        
    };

    function openModal(row) {
        selectedRow.value = {
            ...row,
            timeAllocations:[...row.timeAllocations]
            }
    }
    
    function closeModal() {
        selectedRow.value = null
        
    }
    const morthTotal=(data,key)=>{

        // console.log(data,key);
        const item = data.reduce((acc,val)=>{
            if (val[key]) {
                acc += Number(val[key])
            }
            return acc
            // return Object.keys(item)[index]==key 
        },0)
        return `${item}%`
    }

    function addRow() {
        console.log(selectedRow.value.timeAllocations);
        // selectedRow.value.timeAllocations.push({...initial})
            selectedRow.value.timeAllocations.unshift({...initial});
    }

    function saveRow(index) {
        if (!confirm('Confirm ?')) {
            return
        }
        let items = {...selectedRow.value}
        loading.value =true
        axios.post('/time-allocations', {
            employeeId: items.employeeId,
            timeAllocations: items.timeAllocations
        })
        .then(response => {
            alert('Allocations saved successfully')
            console.log('Allocations saved successfully', response.data.data.time_allocations)
            selectedRow.value.timeAllocations = [...response.data.data.time_allocations]
            loading.value = false
        })
        .catch(error => {
            alert('Error saving allocations')
            console.error('Error saving allocations:', error.response?.data || error.message)
            loading.value = false
        })
    }

    function removeItem(index){
        selectedRow.value.timeAllocations.splice(index,1);
    }

    function progress(key, value) {
        return exClude.includes(key) ? 0 : value ? `${value > 100 ? 100 : value}%` : 0;
        // const total = selectedRow.value.timeAllocations[index].total || 0;
        // return total > 0 ? (selectedRow.value.timeAllocations[index][key] / total) * 100 : 0;   
        
    }

    watch(search, (value) => {
    const lowerVal = value.toLowerCase()
    staffData.value = originalStaffData.value.filter(item =>
        item.name.toLowerCase().includes(lowerVal) ||
        item.resno.toLowerCase().includes(lowerVal)
    )
    })

    
    onMounted(()=>{
    originalStaffData.value  = props.staff.map((items) => ({
    employeeId:items.employeeId,
    resno: items.resno || 'N/A',
    name: items.name  + ' ' + items.lastName,
    position: items.position || 'N/A',
    grade_level: items.grade_level || 'N/A',
    grade: items.grade || 'N/A',
    organization: items.organization || 'N/A',
    country: items.country || 'N/A',
    base_station: items.base_station || 'N/A',
    division: items.division || 'N/A',
    unit_program: items.unit_program || 'N/A',
    supervisor: items.supervisor
    ? `${items.supervisor.firstName || ''} ${items.supervisor.lastName || ''}`.trim() || 'N/A'
    : 'N/A',
    timeAllocations: items.time_allocations
  }))
    staffData.value = [...originalStaffData.value]
    
    //     console.log(
    // props.staff.map((items) => ({
    //     resno: items.resno || 'N/A',
    //     name: items.name || 'N/A',
    //     position: items.position || 'N/A',
    //     grade_level: items.grade_level || 'N/A',
    //     grade: items.grade || 'N/A',
    //     organization: items.organization || 'N/A',
    //     country: items.country || 'N/A',
    //     base_station: items.base_station || 'N/A',
    //     division: items.division || 'N/A',
    //     unit_program: items.unit_program || 'N/A',
    //     supervisor: items.supervisor || 'N/A',
    // }))
    // );

    })

    </script>



<template>
    <div class="p-7 w-full h-screen  flex flex-col" >
        <h1 class="text-xl font-bold mb-4">Staff Time Allocation</h1>
        <!-- <pre>{{ staff}}</pre> -->
        <div class="flex-1 ">
            <div class="w-full">
                <input 
                v-model="search"
                class="border border-gray-300 p-2 rounded-lg w-full mb-3"
                type="text" placeholder="search..." name="" id="">
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg overflow-y-auto min-h-10 max-h-[600px]">
                <table class="min-w-max  text-sm w-full border-collapse">
                    <thead class="bg-gray-200  sticky top-0 ">
                    <tr>
                        <th
                        v-for="(label, key) in headers"
                        :key="key"
                        class="px-4 py-2 border text-[11px] border-gray-300 text-left whitespace-nowrap"
                        >
                        {{ label }}
                        </th>
                        <!-- <th
                        v-for="(label, key) in headerMorth"
                        :key="key"
                        class="px-4 py-2 border text-[11px] border-gray-300 text-left whitespace-nowrap"
                        >
                        {{ label }}
                        </th> -->
                    </tr>
                    </thead>
                    <tbody>
                        <!-- <template v-for="(row, index) in staffData" :key="index">
                            <template v-if=" row.timeAllocations.length > 0">
                            <tr 
                            v-for="(item, key) in row.timeAllocations"
                                class="hover:bg-blue-50 cursor-pointer" @click="openModal(row)">
                                <td
                                class="px-4 py-2 border text-[11px] border-gray-300 whitespace-nowrap"
                                v-for="(label, key) in headers"
                                :key="key"
                                >{{ row[key] }}
                            </td>
                            <td v-for="(month, mIndex) in headerMorth" :key="mIndex" class="px-4 py-2 border text-[11px] border-gray-300 whitespace-nowrap">
                                {{ item[month.toLowerCase()] || 0 }}
                            </td>
                            </tr>
                            </template>
                            <tr 
                            @click="openModal(row)"
                            v-else>
                                <td
                                    class="px-4 py-2 border text-[11px] border-gray-300 whitespace-nowrap"
                                    v-for="(label, key) in headers"
                                    :key="key"
                                    >{{ row[key] }}
                                </td>
                                <td v-for="(month, mIndex) in headerMorth" :key="mIndex" class="px-4 py-2 border text-[11px] border-gray-300 whitespace-nowrap">
                                    0
                                </td>
                            </tr>
                        </template> -->
                        <tr
                            v-for="(row, index) in staffData"
                            :key="index"
                            class="hover:bg-blue-100 cursor-pointer"
                            @click="openModal(row)"
                        >
                            <td
                            v-for="(label, key) in headers"
                            :key="key"
                            class="px-4 py-3 border-b text-[12px] bg-white border-gray-200 whitespace-nowrap"
                            >
                            {{row[key] }}
                            
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="selectedRow"  class="fixed p-10 w-full inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white flex flex-col w-[100%] p-3 px-5 rounded-xl shadow-xl overflow-hidden max-h-[90vh]">
                    <div class="w-full">
                        <h2 class="text-lg font-semibold mb-4">Staff Time Allocation : {{ selectedRow.name  }} </h2>
                        <button @click="addRow" class="p-1 px-2 mb-2  bg-green-500 text-white  cursor-pointer rounded hover:bg-green-400">
                           + Add new
                        </button>
                        <div class="w-full flex items-center gap-3">
                            <!-- <span>Statistic:</span> -->
                            <div 
                            v-for="(month, mIndex) in morth" :key="mIndex"
                            class="flex items-center gap-1 py-3 font-medium text-[12px]">
                                <span class="text-green-500">{{ month}} :</span>
                                <!-- <span>{{ morthTotal(selectedRow.timeAllocations,'jan') }}</span> -->
                                <transition 
                                name="fade-slide"
                                        mode="out-in"
                                >
                                        <span :key="morthTotal(selectedRow.timeAllocations, month.toLocaleLowerCase())">
                                            {{ morthTotal(selectedRow.timeAllocations, month.toLocaleLowerCase()) }}
                                        </span>
                                </transition>
                            </div>
                        </div>
                    </div>
                <!-- <pre> {{ selectedRow }}</pre> -->
                <!-- {{ selectedRow.timeAllocations[0]['year'] }} -->
                <div class="flex-1 overflow-y-auto" v-if="selectedRow.timeAllocations.length>0">
                    <div class="w-full " v-for="(items, index) in selectedRow.timeAllocations" :key="index">
                        <!-- {{ selectedRow.timeAllocations[index]['year'] }} -->
                        <div v-if="selectedRow.timeAllocations[index]['date']" class="w-full flex items-center gap-1">
                            <div class="w-full rounded-full  h-[1px] bg-gray-300"></div>
                            <div class="min-w-max">{{ selectedRow.timeAllocations[index]['date'] }}</div>
                            <div class="w-full  rounded-full h-[1px] bg-gray-300"></div>
                        </div>
                        <div 
                        :class="[
                                ' borders flex gap-2 borders-gray-200  w-full',
                            ]"
                        >
                            <div :class="['flex w-full  items-start p-2 rounded-md  cursor-pointer  mb-2 hover:bg-blue-50',
                                !selectedRow.timeAllocations?.[index]?.id ? 'bg-red-50' : 'bg-gray-100 '
                            ] ">
                                <div class="w-full text-center"   v-for="(label, key) in content">
                                    <label class="block mb-2  text-[12px] font-medium lowercase text-gray-700">{{ label }}</label>
                                     <div class="relative w-full h-8">
                                        <!-- fond de progression -->
                                        <div
                                        class="absolute inset-0 bg-green-300 transition-all duration-200 rounded"
                                        :style="{ width: progress(key,selectedRow.timeAllocations[index][key]), zIndex: 0 }"
                                        ></div>

                                        <!-- input au-dessus -->
                                        <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        :disabled="key=='total'"
                                        :class="[
                                        {'bg-yellow-400': key=='total'},
                                        ]"
                                        @input="(event)=>getRowTotal(event.target.value,index)"
                                        v-model="selectedRow.timeAllocations[index][key]"
                                        class="relative z-10 w-full h-full border text-center  text-sm border-gray-300  focus:outline-none focus:ring-2 focus:rounded-md focus:ring-green-500 focus:border-green-500"
                                        />
                                    </div>
                                    <!-- <input
                                    type="number"
                                    min="0"
                                    max="100"
                                    @input="(event)=>getRowTotal(event.target.value,index)"
                                    v-model="selectedRow.timeAllocations[index][key]"
                                    :disabled="key=='total'"
                                    :class="[
                                        {'bg-green-300': key=='total'},
                                    ]"
                                    class=" 
                                    border text-center
                                    after:w-full
                                    after:h-full
                                    after:content-['']
                                    after:absolute
                                    after:inset-0
                                    after:z-10
                                    after:bg-amber-100
                                    border-gray-300 w-full p-1 text-sm focus:outline-none focus:ring-2 focus:rounded-md focus:ring-green-500 focus:border-green-500"
                                    /> -->
                                    <transition
                                        name="fade-slide"
                                        mode="out-in"
                                    >
                                        <span
                                        :key="selectedRow.timeAllocations[index][key]"
                                        v-if="!exClude.includes(key)"
                                        class="text-[11px] font-bold block"
                                        >
                                        {{ selectedRow.timeAllocations[index][key] }}%
                                        </span>
                                    </transition>
                                </div>
                            </div>
                            <button 
                            @click="removeItem(index)"
                            v-if="!selectedRow.timeAllocations?.[index]?.id" type="button" 
                            class="p-1 cursor-pointer">x</button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center mt-2 font-medium">
                    <span>No Data</span>
                </div>
                <div class="w-full">
                        <div class="mt-3 flex justify-end gap-2">
                        <button class="px-3 p-1  cursor-pointer  bg-gray-100 border border-gray-300  rounded hover:bg-gray-400" @click="closeModal">
                            cancel
                        </button>
                        <button class="px-3 py-1 cursor-pointer border-0 bg-blue-600 text-white rounded hover:bg-blue-700" @click="saveRow">
                            <div v-if="loading" >Processing...</div>
                            <span v-else> Save now</span>
                        </button>
                </div>
            </div>
            </div>
        </div>
    </div>
    </template>

    <style scoped>
    /* ::-webkit-scrollbar {
    height: 8px;
    }
    ::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.3);
    border-radius: 4px;
    } */

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-slide-enter-to {
  opacity: 1;
  transform: translateY(0);
}
.fade-slide-leave-from {
  opacity: 1;
  transform: translateY(0);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
.fade-update-enter-active,
.fade-update-leave-active {
  transition: all 0.3s ease;
}

.fade-update-enter-from,
.fade-update-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

    </style>
    ______-----```````````````