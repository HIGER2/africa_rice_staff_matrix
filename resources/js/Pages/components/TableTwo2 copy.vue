<template>
  <div class="p-4">
    <table class="w-full border-collapse border border-gray-400 text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2">RESNO</th>
          <th class="border p-2">NAME</th>
          <th class="border p-2">POSITION</th>
          <th class="border p-2">GRADE/LEVEL</th>
          <th class="border p-2">GRADE</th>
          <th class="border p-2">ORGANIZATION</th>
          <th class="border p-2">COUNTRY</th>
          <th class="border p-2">BASE STATION</th>
          <th class="border p-2">DIVISION</th>
          <th class="border p-2">UNIT/PROGRAM</th>
          <th class="border p-2">SUPERVISOR</th>
          <th class="border p-2">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(user, index) in users" :key="index" @click="openModal(user)" class="cursor-pointer hover:bg-gray-50">
          <td class="border p-2">{{ user.resno }}</td>
          <td class="border p-2">{{ user.name }}</td>
          <td class="border p-2">{{ user.position }}</td>
          <td class="border p-2">{{ user.grade_level }}</td>
          <td class="border p-2">{{ user.grade }}</td>
          <td class="border p-2">{{ user.organization }}</td>
          <td class="border p-2">{{ user.country }}</td>
          <td class="border p-2">{{ user.base_station }}</td>
          <td class="border p-2">{{ user.division }}</td>
          <td class="border p-2">{{ user.unit_program }}</td>
          <td class="border p-2">{{ user.supervisor }}</td>
          <td class="border p-2">{{ userTotal(user) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="selectedUser" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg w-[90%] max-w-4xl overflow-y-auto max-h-[80vh]">
        <h2 class="text-xl font-bold mb-4">Agreements for {{ selectedUser.name }}</h2>
        <button class="bg-green-500 text-white px-4 py-1 rounded mb-4" @click="addAgreement">+ Add Agreement</button>

        <table class="w-full border-collapse border border-gray-300 text-sm mb-4">
          <thead>
            <tr class="bg-gray-200">
              <th class="border p-1">Type</th>
              <th class="border p-1">Bus</th>
              <th v-for="month in months" :key="month" class="border p-1">{{ month }}</th>
              <th class="border p-1">Total</th>
              <th class="border p-1">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(agreement, i) in selectedUser.agreement" :key="i">
              <td class="border p-1"><input v-model="agreement.type" class="w-full border px-1 py-0.5" /></td>
              <td class="border p-1"><input v-model="agreement.bus" class="w-full border px-1 py-0.5" /></td>
              <td v-for="month in months" :key="month" class="border p-1">
                <input type="number" v-model.number="agreement[month.toLowerCase()]" class="w-full border px-1 py-0.5" />
              </td>
              <td class="border p-1">{{ agreementTotal(agreement) }}</td>
              <td class="border p-1 text-center">
                <button class="text-red-500" @click="removeAgreement(i)">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>

        <button class="bg-blue-500 text-white px-4 py-1 rounded" @click="selectedUser = null">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Decs']

const users = ref([
  {
    resno: '001',
    name: 'John Doe',
    position: 'Engineer',
    grade_level: 'P2',
    grade: 'A',
    organization: 'ABC',
    country: 'CI',
    base_station: 'Abidjan',
    division: 'Tech',
    unit_program: 'Dev',
    supervisor: 'Jane Smith',
    agreement: [
      { type: 'Local', bus: 'Yes', jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, oct: 10, nov: 11, decs: 12 },
      { type: 'Consultant', bus: 'No', jan: 2, feb: 2, mar: 2, apr: 2, may: 2, jun: 2, jul: 2, aug: 2, sep: 2, oct: 2, nov: 2, decs: 2 },
    ]
  }
])

const selectedUser = ref(null)

function userTotal(user) {
  return user.agreement.reduce((acc, ag) => acc + agreementTotal(ag), 0)
}

function agreementTotal(ag) {
  return months.reduce((sum, m) => sum + (ag[m.toLowerCase()] || 0), 0)
}

function openModal(user) {
  selectedUser.value = user
}

function addAgreement() {
  selectedUser.value.agreement.push({ type: '', bus: '', jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, decs: 0 })
}

function removeAgreement(index) {
  selectedUser.value.agreement.splice(index, 1)
}
</script>

<style scoped>
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
