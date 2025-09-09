<!-- EmployeeModal.vue -->
<script setup>
import { Inertia } from '@inertiajs/inertia'
import { ref, reactive, watch, nextTick, defineProps, defineEmits } from 'vue'

const props = defineProps({
  isVisible: { type: Boolean, default: false },
  employeeData: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['close', 'submit'])

const isEditMode = ref(false)
const isSubmitting = ref(false)
const loading = ref(false)
const formData = reactive({
  employeeId: null,
  name: '',
  lastName: '',
  grade_level: '',
  grade: '',
  division: '',
  position: '',
  organization: '',
  country_of_residence: '',
  base_station: '',
  unit_program: '',
  supervisors_matricule:null
})
const formFields = [
  // { label: "Employee ID", key: "employeeId", type: "number" },
  { label: "First Name", key: "name", type: "text" },
  { label: "Last Name", key: "lastName", type: "text" },
  { label: "Email", key: "email", type: "email" },
  // { label: "RES Number", key: "resno", type: "text" },
  { label: "Grade Level", key: "grade_level", type: "text" },
  { label: "Grade", key: "grade", type: "text" },
  { label: "Division", key: "division", type: "text" },
  { label: "Position", key: "position", type: "text" },
  { label: "Organization", key: "organization", type: "text" },
  { label: "Country of Residence", key: "country_of_residence", type: "text" },
  { label: "Base Station", key: "base_station", type: "text" },
  { label: "Program / Unit", key: "unit_program", type: "textarea" },
  { label: "Supervisor RES Number", key: "supervisors_matricule", type: "number" },
];

const fieldLabels = {
  name: 'Prénom',
  lastName: 'Nom',
  email: 'Email'
}

watch(
  () => props.employeeData,
  (newData) => {
    if (newData && Object.keys(newData).length > 0) {
      Object.keys(formData).forEach(key => {
        formData[key] = newData[key] || (typeof formData[key] === 'number' ? null : '')
      })
    }
  },
  { deep: true, immediate: true }
)

watch(
  () => props.isVisible,
  (newValue) => {
    if (newValue) {
      isEditMode.value = false
      nextTick(() => {
        const firstInput = document.querySelector('input:not([disabled])')
        if (firstInput) firstInput.focus()
      })
    }
  }
)

const closeModal = () => {
  isEditMode.value = false
  emit('close')
}

const enableEditMode = () => {
  isEditMode.value = true
}

const validateForm = () => {
  const requiredFields = ['name', 'lastName', 'email']
  for (const field of requiredFields) {
    if (!formData[field] || formData[field].toString().trim() === '') {
      alert(`Le champ ${fieldLabels[field]} est requis`)
      return false
    }
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(formData.email)) {
    alert('Veuillez saisir un email valide')
    return false
  }
  return true
}

const handleSubmit = async () => {
        if (!confirm('Are you sure you want to apply these changes?')) {
            return;
        }
        loading.value =true
        axios.post('/staff/update',
            {...formData}
        )
        .then(async(response) => {
            Inertia.reload();
            alert('Operation successfully')

        })
        .catch(error => {
            alert(error.response?.data?.message || error.message)
            console.error('Error saving :', error.response?.data || error.message)
        })
        .finally(() => {
        loading.value = false
        })
    
}
</script>

<template>
<Transition name="modal-overlay" appear>
    <div
      v-if="isVisible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-black/60 via-black/50 to-black/40  p-4"
      @click="closeModal"
    >
   
      <!-- Modal Container avec animation -->
      <Transition name="modal-content" appear>
        <div
          class="w-full max-w-xl p-4 bg-white rounded-lg shadow-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl"
          @click.stop
        >
          <!-- Header avec gradient -->
          <div class="relative bg-gradient-to-r p-2">
            <div class="flex justify-between items-center">
              <div class="flex items-center space-x-4">
                <!-- Icon -->
                <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center backdrop-blur-sm">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <!-- <h2 class="text-2xl font-bold text-black">
                    {{ isEditMode ? "Modifier l'employé" : "Profil employé" }}
                  </h2> -->
                  <p class="text-black text-sm mt-1">
                    {{ formData.name }} {{ formData.lastName }}
                  </p>
                </div>
              </div>
              
                <button type="button" @click="closeModal"
                        class="p-2 absolute top-0 right-2 text-2xl cursor-pointer">
                <i class="uil uil-times"></i>
                </button>
            </div>
          </div>

          <!-- Body avec scroll personnalisé -->
          <form @submit.prevent="handleSubmit" class="relative">
            <div class="p-8 space-y-8 max-h-[65vh] overflow-y-auto custom-scrollbar">
          <!-- <pre>  {{ employeeData }}</pre> -->

              <!-- Section Informations Personnelles -->
              <div class="space-y-5">
                <!-- <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800">Informations personnelles</h3>
                </div> -->
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3">
                  <div v-for="(item,index) in formFields" 
                  :class="[
                    'space-y-2',
                    (index === formFields.length - 1 && formFields.length % 2 !== 0) ? 'col-span-2' : ''

                ]"
                  >
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      {{ item.label }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                      <input
                        id="name"
                        :placeholder="item.label"
                        v-model="formData[item.key]"
                        type="text"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 disabled:bg-gray-50 disabled:text-gray-500 disabled:border-gray-200"
                        required
                      />
                    </div>
                    <!-- <span v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</span> -->
                  </div>
                </div>
              </div>

            
            </div>

            <!-- Footer avec gradient -->
            <div class=" py-2 ">
              <div class="flex justify-end gap-4">
                <button
                  type="button"
                  class="px-6 cursor-pointer p-2 rounded-xl bg-white border-2 border-gray-300 text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:ring-4 focus:ring-gray-300/50"
                  @click="closeModal"
                >
                  Cancel
                </button>
                
               
                <button
                  type="submit"
                  class="px-8 p-2 cursor-pointer rounded-xl bg-green-500 text-white transition-all duration-200 transform hover:scale-105 hover:shadow-xl focus:ring-4 focus:ring-green-500/50 disabled:opacity-50 disabled:transform-none disabled:shadow-none"
                  :disabled="loading"
                >
                  <div class="flex items-center space-x-2">
                    <span>{{ loading ? "processing..." : "Save" }}</span>
                  </div>
                </button>
              </div>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>
