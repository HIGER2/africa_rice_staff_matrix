<template>
  <div class="p-4">
    <h1 class="text-xl font-bold mb-4">Staff Monthly Matrix</h1>

    <div class="overflow-x-auto overflow-y-auto border border-gray-300 rounded-lg">
      <table class="min-w-max w-full   border-collapse">
        <thead class="">
          <tr>
            <th v-for="(month,index) in headers" :key="month" 
            
            class="px-4 py-3 border-b border-gray-300 text-[9px] bg-gray-200"
            :class="[index === 'resno' ? 'sticky left-0' : ''] "
            >{{ month.toUpperCase() }}</th>

            <th v-for="month in monthKeys" :key="month" class="px-4 py-3 uppercase border-b bg-gray-200 border-gray-300 text-[9px]">{{ month }}</th>

            <th class="px-4 py-3 border-b border-gray-300 text-[9px] bg-gray-200">Total</th>
          </tr>
        </thead>

        <tbody>
          <template v-for="(user, index) in users" :key="index">
            <tr
              v-for="(agreement, aIndex) in user.agreement"
              :key="aIndex"
              class="hover:bg-blue-50 cursor-pointer"
              @click="openModal(user)"
            >
              <td class="px-4 sticky left-0  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.resno }}</td>
            <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1"  v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.name }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.position }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.grade_level }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.grade }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.organization }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.country }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.base_station }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.division }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.unit_program }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1" v-if="aIndex === 0" :rowspan="user.agreement.length">{{ user.supervisor }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1">{{ agreement.type }}</td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] p-1">{{ agreement.bus }}</td>
              <td
                v-for="month in monthKeys"
                :key="month"
                class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] text-center"
              >
                {{ agreement[month] }}
              </td>
              <td class="px-4  border border-gray-300 whitespace-nowrap bg-white text-[11px] font-semibold text-center">
                {{ getRowTotal(agreement) }}
              </td>
            </tr>

            <!-- Total par utilisateur -->
            <!-- <tr class="bg-gray-100 font-semibold text-blue-900">
              <td colspan="5" class="px-4 py-2 border text-right">Total {{ user.name }}</td>
              <td
                v-for="month in monthKeys"
                :key="month"
                class="px-4 py-2 border text-center"
              >
                {{ getUserMonthlyTotal(user, month) }}
              </td>
              <td class="px-4 py-2 border text-center">
                {{ getUserTotal(user) }}
              </td>
            </tr> -->
          </template>

          <!-- Grand total général -->
          <!-- <tr class="bg-blue-100 font-bold text-blue-900">
            <td colspan="5" class="px-4 py-2 border text-right">GRAND TOTAL</td>
            <td
              v-for="month in monthKeys"
              :key="month"
              class="px-4 py-2 border text-center"
            >
              {{ getGrandMonthlyTotal(month) }}
            </td>
            <td class="px-4 py-2 border text-center">
              {{ getGrandTotal }}
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
     
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

// Mois à afficher
const monthKeys = [
  'jan', 'feb', 'mar', 'apr', 'may', 'jun',
  'jul', 'aug', 'sep', 'oct', 'nov', 'decs'
]
const selectedUser = ref(null)

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
    agreement: 'AGREEMENT',
    bus: 'BUS',
    // jan: 'JAN',
    // fb: 'FB',
    // apr: 'APR',
    // mar: 'MAR',
    // may: 'MAY',
    // jun: 'JUN',
    // jul: 'JUL',
    // aug: 'AUG',
    // sep: 'SEP',
    // oct: 'OCT',
    // nov: 'NOV',
    // decs: 'DEC',
    // total: 'Total',
    // remarks: 'Remarks',
    }
// Données utilisateurs
const userss = [
  {
    name: 'John Doe',
    position: 'Manager',
    country: 'Côte d\'Ivoire',
  agreement: [
      { type: 'Local', bus: 'Yes', jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, oct: 10, nov: 11, decs: 12 },
      { type: 'Consultant', bus: 'No', jan: 2, feb: 2, mar: 2, apr: 2, may: 2, jun: 2, jul: 2, aug: 2, sep: 2, oct: 2, nov: 2, decs: 2 },
    ]
  },
  {
    name: 'Fatou Diop',
    position: 'Analyst',
    country: 'Senegal',
    agreement: [
      { type: 'Local', bus: 'Yes', jan: 5, feb: 5, mar: 5, apr: 5, may: 5, jun: 5, jul: 5, aug: 5, sep: 5, oct: 5, nov: 5, decs: 5 },
    ]
  }
]

 const users = ref(
    Array.from({ length: 50 }, (_, i) => ({
        resno: `RES-${1000 + i}`,
        name: `John Doe ${i + 1}`,
        position: ['Manager', 'Analyst', 'Lead'][i % 3],
        grade_level: ['A1', 'B2', 'C3'][i % 3],
        grade: ['G1', 'G2', 'G3'][i % 3],
        organization: ['Org1', 'Org2', 'Org3'][i % 3],
        country: ['Côte d\'Ivoire', 'Ghana', 'Senegal'][i % 3],
        base_station: ['Abidjan', 'Accra', 'Dakar'][i % 3],
        division: ['Div A', 'Div B', 'Div C'][i % 3],
        unit_program: ['Program X', 'Program Y', 'Program Z'][i % 3],
        supervisor: ['Jane Smith', 'Albert Jones', 'Fatou Diop'][i % 3],
        agreement: [
      { type: 'Local', bus: 'Yes', jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, oct: 10, nov: 11, decs: 12 },
      { type: 'Consultant', bus: 'No', jan: 2, feb: 2, mar: 2, apr: 2, may: 2, jun: 2, jul: 2, aug: 2, sep: 2, oct: 2, nov: 2, decs: 2 },
    ]
    }))
    )
    
function openModal(user) {
  console.log(user);
  
  selectedUser.value = user
}
// Fonction : total d'une ligne
function getRowTotal(agreement) {
  return monthKeys.reduce((sum, m) => sum + (agreement[m] || 0), 0)
}

// Fonction : total mensuel pour un utilisateur
function getUserMonthlyTotal(user, month) {
  return user.agreement.reduce((sum, a) => sum + (a[month] || 0), 0)
}

// Fonction : total global pour un utilisateur
function getUserTotal(user) {
  return monthKeys.reduce((total, month) => total + getUserMonthlyTotal(user, month), 0)
}

// Total global par mois
function getGrandMonthlyTotal(month) {
  return users.reduce((sum, user) => sum + getUserMonthlyTotal(user, month), 0)
}

// Total général
const getGrandTotal = computed(() =>
  monthKeys.reduce((total, month) => total + getGrandMonthlyTotal(month), 0)
)
</script>
