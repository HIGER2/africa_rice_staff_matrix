```````````````        <template>
    <div class="p-7 w-full h-screen overflow-hidden flex flex-col" >
        <h1 class="text-xl font-bold mb-4">Staff Matrix Table</h1>

        <div class="overflow-x-auto border border-gray-200 rounded-lg overflow-y-auto flex-1">
        <table class="min-w-max  text-sm w-full border-collapse">
            <thead class="bg-gray-200">
            <tr>
                <th
                v-for="(label, key) in headers"
                :key="key"
                class="px-4 py-2 border text-[11px] border-gray-300 text-left whitespace-nowrap"
                >
                {{ label }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="(row, index) in tableData"
                :key="index"
                class="hover:bg-blue-100 cursor-pointer"
                @click="openModal(row)"
            >
                <td
                v-for="(label, key) in headers"
                :key="key"
                class="px-4 py-3 border-b text-[12px] border-gray-200 whitespace-nowrap"
                >
                {{ row[key] }}
                </td>
            </tr>
            </tbody>
        </table>
        </div>

        <!-- Modal -->
        <div v-if="selectedRow" class="fixed inset-0 bg-black/50 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-[90%] max-w-5xl p-6 rounded-xl shadow-xl overflow-y-auto max-h-[90vh]">
            <h2 class="text-lg font-semibold mb-4">Modifier la ligne</h2>

            <div class="grid grid-cols-2 gap-4">
            <div v-for="(label, key) in headers" :key="key">
                <label class="block text-sm font-medium text-gray-700">{{ label }}</label>
                <input
                type="text"
                v-model="selectedRow[key]"
                class="mt-1 block w-full rounded-md p-2 border-gray-300 border"
                />
            </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
            <button class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" @click="selectedRow = null">Annuler</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" @click="saveRow">Enregistrer</button>
            </div>
        </div>
        </div>
    </div>
    </template>

    <script setup>
    import { ref } from 'vue'

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
    agreement: 'AGREEMENT',
    bus: 'BUS',
    jan: 'JAN',
    fb: 'FB',
    apr: 'APR',
    mar: 'MAR',
    may: 'MAY',
    jun: 'JUN',
    jul: 'JUL',
    aug: 'AUG',
    sep: 'SEP',
    oct: 'OCT',
    nov: 'NOV',
    decs: 'DEC',
    total: 'Total',
    remarks: 'Remarks',
    }

    // Générer des données fictives (50 lignes)
    const tableData = ref(
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
        agreement: 'Local',
        bus: 'Yes',
        jan: 1,
        fb: 1,
        apr: 1,
        mar: 1,
        may: 1,
        jun: 1,
        jul: 1,
        aug: 1,
        sep: 1,
        oct: 1,
        nov: 1,
        decs: 1,
        total: 12,
        remarks: 'RAS'
    }))
    )

    const selectedRow = ref(null)

    function openModal(row) {
    selectedRow.value = { ...row }
    }

    function saveRow() {
    const index = tableData.value.findIndex(r => r.resno === selectedRow.value.resno)
    if (index !== -1) {
        tableData.value[index] = { ...selectedRow.value }
    }
    selectedRow.value = null
    }
    </script>

    <style scoped>
    ::-webkit-scrollbar {
    height: 8px;
    }
    ::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.3);
    border-radius: 4px;
    }
    </style>
    ______-----```````````````