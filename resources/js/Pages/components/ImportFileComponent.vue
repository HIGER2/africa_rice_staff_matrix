<script setup>
import axios from "axios";
import { ref } from "vue";
import { Inertia } from '@inertiajs/inertia';

const selectedFile = ref(null);
const isUploading = ref(false);
const fileInput = ref(null);

// Fonction pour récupérer le fichier sélectionné

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0];
  console.log("Fichier sélectionné :", selectedFile.value);
};

const resetFile = () => {
  selectedFile.value = null;
  fileInput.value.value = null; // vide l’input
};

// Fonction pour envoyer le fichier via Axios
const uploadFile = async () => {
    if (!selectedFile.value) {
        alert("Please select a file before uploading!");
        return;
    }

    // Confirmation avant l'envoi
    const confirmed = confirm("Are you sure you want to upload this file?");
    if (!confirmed) return;

  isUploading.value = true
  const formData = new FormData();
  formData.append("file", selectedFile.value);

    window.axios.post('/allocations-file',formData, {
        headers: { "Content-Type": "multipart/form-data" },
    })
    .then(async(response) => {
        Inertia.reload()
        alert(response?.data?.message || response?.message)

        document.getElementById("my_modal_1").close();

    })
    .catch(error => {
        alert(error.response?.data?.message || error.message)
        console.error('Error saving allocations:', error.response?.data || error.message)
    })
    .finally(() => {
        isUploading.value = false
        resetFile()
    })
};
const openModal = () => {
  document.getElementById("my_modal_1").showModal();
};

// Fermer le modal
const closeModal = () => {
  document.getElementById("my_modal_1").close();
};
</script>


<template>
  <div>
     <button class="btn rounded-lg" @click="openModal">
        <i class="uil uil-import"></i> 
        Import
     </button>
    <dialog @click.self="closeModal" id="my_modal_1" class="modal  modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg mb-4 font-bold">Import File</h3>

            <!-- Formulaire pour upload -->
            <form @submit.prevent="uploadFile" class="space-y-4">
                <!-- Input de fichier -->
                <div>
                    <label class="py-4">Select a file to import</label>
                    <input type="file" name="file" 
                    ref="fileInput"
                    @change="handleFileChange" 
                    class="file-input file-input-bordered w-full"
                    accept=".xls, .xlsx"
                    required />
                </div>
                <a href="/template.xlsx" 
                download
                class="link link-primary">
                    Download template
                </a>
                <div class="modal-action">
                <!-- Bouton de soumission -->
                <button type="submit" class="btn btn-primary" :disabled="isUploading">
                    {{ isUploading ? "Uploading..." : "Upload" }}
                </button>

                <!-- Bouton de fermeture -->
                    <button type="button" @click="closeModal" class="btn">Close</button>
                </div>
            </form>
            </div>

    </dialog>

    
  </div>
</template>

<style>

</style>