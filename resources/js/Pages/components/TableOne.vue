


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
    const exClude= ['id','employeeId','agreement','bus','year','date','total']
    const keys = ['id','employeeId','agreement','bus','year','date',
                'jan','feb','mar','apr','may','jun','jul','aug','sep','oct'
                ,'nov','total'
                ]

    Array.prototype.isAnyExcluded = function (keys) {
        return keys.some(key => this.includes(key));
    };

    function isAnyExcluded(keys) {
        return keys.some(key => exClude.includes(key));
    }
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

    const headerTotal={
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
    total: 'TOTAL',
    }
    
    const initial = {
            id:null,
            agreement: 0,
            bus: 0,
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
        }

    const initialTotal = {
        id:null,
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

        selectedRow.value.monthlyTotal = {
            ...selectedRow.value.monthlyTotal,
            ...calculateMonthlyTotals(selectedRow.value.timeAllocations)
        }

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

    function calculateTotalMorth (data,key) {
        if (exClude.includes(key)) {
            return ''
        }
        return data.reduce((acc,val)=>{
            if (val[key]) {
                acc += Number(val[key])
            }
            return acc
            // return Object.keys(item)[index]==key 
        },0)
    }
    const morthTotal=(value,key)=>{
         let keys=['agreement','bus']
         console.log(key);
         
        if (keys.includes(key)) {
            return ''
        }
        return `${value}%`
    }

    const labelTotal=(label,key)=>{
        let keys=['agreement','bus']
        if (keys.includes(key)) {
            return ''
        }

        return `${label}:`
    }

    function addRow() {
        // selectedRow.value.timeAllocations.push({...initial})
            selectedRow.value.timeAllocations.unshift({...initial});
            if (selectedRow.value.timeAllocations.length ==1) {
                selectedRow.value.monthlyTotal ={...initialTotal}
            }
    }
    
    function calculateMonthlyTotals(data) {
    const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        const result = {};
        let grandTotal = 0;

        for (const month of months) {
            const monthTotal = data.reduce((sum, item) => sum + (Number(item[month]) || 0), 0);
            result[`${month}`] = monthTotal;
            grandTotal += monthTotal;
        }
        result.total = grandTotal;
        return result;
    }

    function saveRow(index) {
        if (!confirm('Confirm ?')) {
            return
        }
        let items = {...selectedRow.value}  
        // let monthly_time_totals = calculateMonthlyTotals(items.timeAllocations)
        
        loading.value =true
        axios.post('/time-allocations', {
            employeeId: items.employeeId,
            timeAllocations: items.timeAllocations,
            monthlyTimeTotal : items.monthlyTotal
        })
        .then(response => {
            alert('Allocations saved successfully')
            console.log('Allocations saved successfully', response.data.data.time_allocations)
            let indexOfStaff = staffData.value.findIndex(item => item.employeeId == items.employeeId);

            // staffData.value[indexOfStaff] = {
            //         ...items,
            //         timeAllocations: [...response.data.data.time_allocations]
            // };

            let indexOf = originalStaffData.value.findIndex(item => item.employeeId == items.employeeId);

            originalStaffData.value[indexOf] = {
                    ...items,
                    timeAllocations: [...response.data.data.time_allocations],
                    monthlyTotal: {...response.data.data.monthly_total}
            };
            
            staffData.value[indexOfStaff] = {... originalStaffData.value[indexOf]}
            
            selectedRow.value.timeAllocations = [...response.data.data.time_allocations]
            selectedRow.value.monthlyTotal = {...response.data.data.monthly_total}
        })
        .catch(error => {
            alert('Error saving allocations')
            console.error('Error saving allocations:', error.response?.data || error.message)
           
        })
        .finally(() => {
            loading.value = false
            // closeModal()
        })
    }

    function removeItem(index){
        selectedRow.value.timeAllocations.splice(index,1);
        selectedRow.value.monthlyTotal =  calculateMonthlyTotals(selectedRow.value.timeAllocations)
        if (selectedRow.value.timeAllocations.length ==0) {
            selectedRow.value.monthlyTotal =null
        }
    }

    function progress(key, value) {
        return exClude.includes(key) ? 0 : value ? `${value > 100 ? 100 : value}%` : 0;
        // const total = selectedRow.value.timeAllocations[index].total || 0;
        // return total > 0 ? (selectedRow.value.timeAllocations[index][key] / total) * 100 : 0;   
        
    }
    function getColor(value ,key){
        let per = key=='total' ? 1200 : 100

        return  value < per ? 'bg-blue-500' :
            value > per ?  'bg-red-500' :
            'bg-green-300'
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
    name: items?.lastName?.toLowerCase() +' '+items?.name.toLowerCase(),
    position: items.position || 'N/A',
    grade_level: items.grade_level || 'N/A',
    grade: items.grade || 'N/A',
    organization: items.organization || 'N/A',
    country: items.country || 'N/A',
    base_station: items.base_station || 'N/A',
    division: items.division || 'N/A',
    unit_program: items.unit_program || 'N/A',
    supervisor: items.supervisor
    ? `${items.supervisor.firstName.toLowerCase() || ''} ${items.supervisor.lastName.toLowerCase() || ''}`.trim() || 'N/A'
    : 'N/A',
    timeAllocations: items.time_allocations,
    monthlyTotal: items.monthly_total,

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
                type="search" placeholder="search..." name="" id="">
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
        <div v-if="selectedRow" 
        @click.self="closeModal"
        class="fixed p-10 w-full inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white flex flex-col w-[100%] p-3 px-5 rounded-xl shadow-xl overflow-hidden max-h-[90vh]">
                    <div class="w-full relative">
                        <div class="w-full flex items-center gap-2 text-[14px] py-3">
                            <h2 class=" font-semibold mb-4 uppercase">Staff : <span class="capitalize text-gray-600"> {{ selectedRow.name  }}</span> </h2>
                            <h2 class=" font-semibold mb-4 uppercase">Supervisor : <span class="capitalize text-gray-600"> {{  selectedRow.supervisor  }}</span> </h2>
                            <h2 class=" font-semibold mb-4 uppercase">Year : <span class="capitalize text-gray-600">{{ new Date().getFullYear() }}</span> </h2>
                        </div>
                        <button 
                            @click="closeModal"
                            class="p-2 absolute top-0 right-2 text-2xl cursor-pointer">
                            <i class="uil uil-times">
                            </i>
                        </button>
                        <button v-if="selectedRow.timeAllocations.length>0" @click="addRow" class="p-1 px-2 mb-2  bg-green-500 text-white  cursor-pointer rounded hover:bg-green-400">
                           + Add new
                        </button>
                        <div class="w-full flex items-center gap-2">
                            <div class="borders flex gap-2 borders-gray-200  w-full">
                            <div :class="[
                                'flex w-full  items-start p-2 rounded-md  cursor-pointer ',
                            ] ">
                            <!-- <pre> {{ selectedRow.monthlyTotal }}</pre> -->

                                <div v-if="selectedRow.monthlyTotal" class="w-full flex items-center  justify-center gap-1  text-[11px]"  
                                v-for="(label, key) in content">
                                    <span class="font-bold ">{{labelTotal(label,key)}}</span>
                                    <transition 
                                    name="fade-slide"
                                            mode="out-in"
                                    >
                                    <span 
                                    :class="[getColor(selectedRow.monthlyTotal[key],key),' px-1 font-medium']"
                                    :key="selectedRow.monthlyTotal[key]">
                                            {{morthTotal(selectedRow.monthlyTotal[key],key)}}
                                    </span>
                                        <!-- <span :key="morthTotal(selectedRow.timeAllocations, label.toLocaleLowerCase())">
                                                {{ morthTotal(selectedRow.timeAllocations, label.toLocaleLowerCase()) }}
                                        </span> -->
                                    </transition>
                                </div>
                            </div>
                        </div>
                            <!-- <span>Statistic:</span> -->
                            <!-- <div 
                            v-for="(month, mIndex) in content" :key="mIndex"
                            class="flex items-center border w-full py-3 font-medium text-[12px]">
                                <span class="text-green-500">{{ month}} :</span>
                                <transition 
                                name="fade-slide"
                                        mode="out-in"
                                >
                                        <span :key="morthTotal(selectedRow.timeAllocations, month.toLocaleLowerCase())">
                                            {{ morthTotal(selectedRow.timeAllocations, month.toLocaleLowerCase()) }}
                                        </span>
                                </transition>
                            </div> -->
                        </div>
                    </div>
                <!-- {{ selectedRow.timeAllocations[0]['year'] }} -->
                <div class="flex-1 overflow-y-auto" v-if="selectedRow.timeAllocations.length>0">
                    <!-- <pre>{{ selectedRow }}</pre> -->
                    <div class="w-full " v-for="(items, index) in selectedRow.timeAllocations" :key="index">
                        <!-- {{ selectedRow.timeAllocations[index]['year'] }} -->
                        <!-- <div v-if="selectedRow.timeAllocations[index]['date']" class="w-full flex items-center gap-1">
                            <div class="w-full rounded-full  h-[1px] bg-gray-300"></div>
                            <div class="min-w-max">{{ selectedRow.timeAllocations[index]['date'] }}</div>
                            <div class="w-full  rounded-full h-[1px] bg-gray-300"></div>
                        </div> -->
                        <div 
                        class="borders flex gap-2 borders-gray-200  w-full"
                        >
                            <div :class="[
                                'flex w-full  items-start p-2 rounded-md  cursor-pointer  mb-2 hover:bg-blue-50',
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
                                        :type="(key=='agreement' || key=='bus') ?'text' :'number'"
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
                <div v-else class="text-center flex flex-col items-center gap-2 mt-2 font-medium">
                    <span>No Data</span>
                    <button @click="addRow" class="p-1 px-2 mb-2 max-w-max border border-green-500 text-green-500  cursor-pointer rounded ">
                        + Add new
                    </button>
                </div>
                <div class="w-full">
                        <div class="mt-3 flex justify-end gap-2">
                        <button class="px-3 p-1  cursor-pointer  bg-gray-100 border border-gray-300  rounded hover:bg-gray-400" @click="closeModal">
                            cancel
                        </button>
                        <button class="px-3 py-1 cursor-pointer border-0 bg-blue-600 text-white rounded hover:bg-blue-700" @click="saveRow">
                            <div v-if="loading" class="flex items-center gap-1" >
                                <div class="w-5 h-5 border-5 border-white border-t-transparent rounded-full animate-spin"></div>
                                Processing...</div>
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