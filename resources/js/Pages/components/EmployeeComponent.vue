


 <script setup>
    import { Inertia } from '@inertiajs/inertia'
import { onMounted, reactive, ref, watch } from 'vue'
import * as XLSX from 'xlsx'
import AllocationConmponent from './AllocationConmponent.vue'
import EditEmployeeComponent from './EditEmployeeComponent.vue'
import ImportFileComponent from './ImportFileComponent.vue'

    const props = defineProps({
    staff: Array,
    })
    let staffData = ref([])
    const loadingImport = ref(false)
    const originalStaffData = ref([])
    const isModalVisible = ref(false)
    const selectedEmployee = ref({})
    let loading = reactive({
        send: false,
        save: false,
        delete:false
    })

    let search =ref('')

    Array.prototype.isAnyExcluded = function (keys) {
        return keys.some(key => this.includes(key));
    };

    // Clé-valeur : clé technique + nom affiché
    // const headers = {
    // resno: 'RESNO',
    // name: 'NAME',
    // position: 'POSITION',
    // grade_level: 'GRADE/LEVEL',
    // grade: 'GRADE',
    // organization: 'ORGANIZATION',
    // country: 'COUNTRY',
    // base_station: 'BASE STATION',
    // division: 'DIVISION',
    // unit_program: 'UNIT/PROGRAM',
    // supervisor: 'SUPERVISOR',
    // }
    const headers = [
    // { label: "Employee ID", key: "employeeId" },
    // { label: "Supervisor ID", key: "supervisorId" },
    { label: "RES Number", key: "resno" },
    { label: "Staff", key: "employee_name" },
    // { label: "Last Name", key: "lastName" },
    { label: "Email", key: "email" },
    // { label: "Supervisor Matricule", key: "supervisors_matricule" },
    { label: "Supervisor Name", key: "supervisor_name" },
    { label: "Grade Level", key: "grade_level" },
    { label: "Grade", key: "grade" },
    { label: "Division", key: "division" },
    { label: "Position", key: "position" },
    { label: "Organization", key: "organization" },
    { label: "Country of Residence", key: "country_of_residence" },
    { label: "Base Station", key: "base_station" },
    { label: "Unit/Program", key: "unit_program" }
    ];

    let selectedRow = ref(null)
    // methode
    const openEmployeeModal = (employee) => {
    console.log('Ouverture de la modal pour:', employee)
    selectedEmployee.value = { ...employee } // Copie pour éviter les mutations
    isModalVisible.value = true
    }

    const closeModalEdit = () => {
        isModalVisible.value = false
        selectedEmployee.value = {}
    }

    function openModal(row) {
        selectedRow.value = {
            ...row
            }
    }
    
    function closeModal() {
        selectedRow.value = null
        
    }


    watch(search, (value) => {
        const lowerVal = value.toLowerCase()
        staffData.value = props.staff.filter(item =>
            item?.name.toLowerCase().includes(lowerVal) ||
            item?.lastName.toLowerCase().includes(lowerVal) 
            // item?.resno.toLowerCase().includes(lowerVal)
        )
    })
    
    watch(
    () => props.staff,
    (newData) => {
        staffData.value = newData
    },
    { deep: true, immediate: true }
    )
    const handleImport =async () => {
        loadingImport.value = true
            axios.get('/time-import')
            .then(response => {
                console.log(response.data.data[10]) // Ici tu récupères tes données
                const ws = XLSX.utils.json_to_sheet(response.data.data)
                                // Créer un classeur et y ajouter la feuille
                    const wb = XLSX.utils.book_new()
                    XLSX.utils.book_append_sheet(wb, ws, "Employees") 
                    // Exporter en fichier Excel
                    XLSX.writeFile(wb, "employees.xlsx")
            })
            .catch(error => {
                console.error(error)
            }).finally(()=>{
                loadingImport.value = false
            })
    }

    function onScroll(event){
        // console.log(event.scroll);
        
    }

    
    onMounted(()=>{

    })

    </script>



<template>
    <div class="w-full h-screen overflow-hidden  flex flex-col bg-white" >
    <div class="w-full p-3 h-[100px]">
        <div class="flex p-4 rounded-lg mb-3 justify-between items-center z-30  top-0 sticky bg-white ">
            <h1 class="text-xl font-bold mb-4">Staff Activity Contribution (SAC)</h1>
            <div class="flex items-center gap-2 ">
                    <input 
                    v-model="search"
                    class="border border-gray-300 p-2 rounded-lg "
                    type="search" placeholder="search..." name="" id="">
                    <ImportFileComponent/>
                    <button 
                    :disabled="loadingImport"
                    @click="handleImport"
                    type="button" 
                    class="p-2  disabled:bg-blue-400 disabled:cursor-not-allowed px-3  flex items-center gap-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400">
                    <i class="uil uil-export"></i> 
                    <span v-if="!loadingImport">Export</span>
                    <span v-else>Loading...</span>
                    </button>
                </div>

        </div>
    </div>
    <div class="flex-1 p-3  ">
                
        <div 
        @scroll="onScroll"
        class="w-full rounded-lg h-full overflow-hidden   border border-gray-200">
            <div class=" overflow-x-auto  overflow-y-auto min-h-10 max-h-[calc(100vh-120px)]">
                <table class="min-w-max   text-sm w-full border-collapse">
                <thead class=" bg-white z-20  sticky top-0 ">
                <tr>
                    <th
                    v-for="(item, key) in headers"
                    :key="item.key"
                    :class="item.key=='resno' && 'sticky left-0 border'
                    "
                    class="px-4 py-4 bg-white border text-[11px] border-gray-300 text-left whitespace-nowrap"
                    >
                    {{ item.label}} 
                    </th>
                    <th class="sticky bg-white right-0 z-10">
                        Action
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
                        class="group  cursor-pointer"
                        
                    >
                        <td
                        v-for="(item, key) in headers"
                        :key="item.key"
                        :class="item.key=='resno' && 'sticky left-0 z-10'
                        "
                        class="px-4 border py-3 border-b group-hover:bg-gray-100 text-[12px] bg-white border-gray-200 whitespace-nowrap"
                        >
                        {{row[item.key]}}
                        </td>
                        <td
                        class="px-4 sticky right-0 z-10 border py-3 border-b text-[12px] group-hover:bg-gray-100 bg-white border-gray-200 whitespace-nowrap"
                        >

                        <div class="flex items-center gap-3 cursor-pointer">
                            <!-- Bouton Activity -->
                            <button
                                type="button"
                                @click="openModal(row)"
                                class="flex items-center gap-2 cursor-pointer px-4 py-1 rounded-xl
                                    bg-white text-gray-700 font-medium border border-gray-200
                                    hover:bg-gray-50 hover:shadow-sm
                                    active:scale-95 active:shadow-inner
                                    transition-all duration-200"
                            >
                                <i class="uil uil-eye text-gray-500 text-lg"></i>
                                Activity
                            </button>

                            <!-- Bouton Edit -->
                            <button
                                type="button"
                                @click="openEmployeeModal(row)"
                                class="flex items-center cursor-pointer gap-2 px-4 py-1 rounded-xl
                                    bg-white text-gray-700 font-medium border border-gray-200
                                    hover:bg-gray-50 hover:shadow-sm
                                    active:scale-95 active:shadow-inner
                                    transition-all duration-200"
                            >
                                <i class="uil uil-pen text-gray-500 text-lg"></i>
                                Edit
                            </button>
                            </div>

                            <!-- <div class="flex items-center gap-2 px-2  ">
                                <button 
                                type="button"
                                @click="openModal(row)"
                                class="p-2 bg-slate-200 cursor-pointer text-slate-950 font-medium rounded-lg">
                                <i class="uil uil-eye"></i>Activity</button>
                                <button 
                                @click="openEmployeeModal(row)"
                                type="button"
                                class="p-2 bg-slate-200 cursor-pointer text-slate-950 font-medium rounded-lg">
                                <i class="uil uil-pen"></i>Edit
                            </button>
                            </div> -->
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
        <!-- Modal -->
            <AllocationConmponent 
            @update:selectedRow="handleUpdateSelectedRow"
            @close="closeModal"
            @save="handleSaveTimeAllocations"
            @sendEmail="handleSendEmail"
            :selectedRow="selectedRow"
            :staffId="selectedRow?.employeeId"
            />
            <EditEmployeeComponent
            :is-visible="isModalVisible"
            :employee-data="selectedEmployee"
            @close="closeModalEdit"
            @submit="handleEmployeeUpdate"
            />
        <!-- <div v-if="selectedRow" 
        @click.self="closeModal"
        class="fixed p-10 w-full z-20 inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center">
            <div class="bg-slatext-slate-950 font-medium flex flex-col w-[100%] p-3 px-5 rounded-xl shadow-xl overflow-hidden max-h-[90vh]">
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
                    <button v-if="selectedRow.timeAllocations?.length>0" @click="addRow" class="p-1 px-2 mb-2  bg-green-500 text-white  cursor-pointer rounded hover:bg-green-400">
                        + Add new
                    </button>
                    <div class="w-full flex items-center gap-2">
                        <div class="borders flex gap-2 borders-gray-200  w-full">
                    <div 
                    v-if="selectedRow.timeAllocations?.length > 0"
                    :class="[
                            'flex w-full  items-start p-2 rounded-md  cursor-pointer ',
                        ] ">
                            <div  class="w-full flex items-center  justify-center gap-1  text-[11px]"  
                            v-for="(label, key) in content">
                                <span class="font-bold ">{{labelTotal(label,key)}}</span>
                                <transition 
                                name="fade-slide"
                                        mode="out-in"
                            >
                                <span 
                                :class="[getColor(calculateTotalMorth(selectedRow.timeAllocations,key)),' px-1 font-medium']"
                                :key="selectedRow.monthlyTotal[key]">
                                        {{calculateTotalMorth(selectedRow.timeAllocations,key)}}
                                </span>
                               
                                </transition>
                            </div>
                            <div  class="w-full flex items-center  justify-center gap-1  text-[11px]" >
                                <span class="font-bold ">Total</span>
                                <transition 
                                name="fade-slide"
                                mode="out-in"
                                >
                                <span 
                                :class="[getColor(totalMonths(selectedRow.timeAllocations),'total'),' px-1 font-medium']"
                                :key="key">
                                        {{totalMonths(selectedRow.timeAllocations)}}
                                </span>
                                </transition>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto" v-if="selectedRow.timeAllocations.length>0">
                    <div class="w-full " v-for="(items, index) in selectedRow.timeAllocations" :key="index">
                      
                        <div 
                       
                        class="borders flex items-center gap-2 borders-gray-200 group  w-full"
                        >
                            <div 
                            :class="[
                                'flex w-full  items-start p-2 transition-all duration-75 rounded-md  cursor-pointer  mb-2 hover:bg-blue-50',
                                !selectedRow.timeAllocations?.[index]?.id ? 'bg-red-50' : 'bg-gray-100 ',
                               ( loading.delete &&  selectId == selectedRow.timeAllocations?.[index]?.id ) ? 'bg-red-300':''
                            ] ">
                                <div class="w-full text-center"   v-for="(label, key) in content">
                                    <label class="block mb-2  text-[12px] font-medium lowercase text-gray-700">{{ label }}</label>
                                    <div class="relative w-full h-8">
                                        <div
                                        class="absolute inset-0 bg-green-300 transition-all duration-200 rounded"
                                        :style="{ width: progress(key,selectedRow.timeAllocations[index][key]), zIndex: 0 }"
                                        ></div>

                                        <input
                                        :type="(key=='agreement' || key=='bus') ?'text' :'number'"
                                        min="0"
                                        max="100"
                                        :disabled="key=='total' || loading.delete"
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

                            <div>
                        <button 
                            @click="removeItem(index)"
                            v-if="!selectedRow.timeAllocations?.[index]?.id" type="button" 
                            class="p-1 cursor-pointer">x
                            </button>
                            <button
                                  v-if="!loading.delete && selectedRow.timeAllocations?.[index]?.id"
                                    :class="'cursor-pointer  group-hover:inline-block hidden'"
                                    @click="deleteRow(selectedRow.timeAllocations?.[index]?.id,index)" 
                                    class="p-1 cursor-pointer ">
                                    <span class="text-red-500" ><i class="uil uil-trash-alt"></i></span>
                            </button>
                            </div>
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
                         <button class="px-3 py-1 cursor-pointer border-0 bg-green-500 text-white rounded hover:bg-green-700" @click="saveRow">
                            <div v-if="loading.save" class="flex items-center gap-1" >
                                <div class="w-5 h-5 border-5 border-white border-t-transparent rounded-full animate-spin"></div>
                                Processing...</div>
                            <span v-else> Save now</span>
                        </button>
                        <button class="px-3 py-1 cursor-pointer border-0 bg-blue-600 text-white rounded hover:bg-blue-700" @click="sendMail">
                            <div v-if="loading.send" class="flex items-center gap-1" >
                                <div class="w-5 h-5 border-5 border-white border-t-transparent rounded-full animate-spin"></div>
                                Processing...</div>
                            <span v-else> Send email</span>
                        </button>
                </div>
            </div>
            </div>
        </div> -->
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