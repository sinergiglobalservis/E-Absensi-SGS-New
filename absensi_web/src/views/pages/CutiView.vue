<template>
    <Pages title="Daftar Pengajuan Perizinan">
        <div class="container-fluid">
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" :rows-per-page="10"
                buttons-pagination show-index border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Detail</button>
                </template>
            </EasyDataTable>
        </div>
        <!-- modals view -->
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <FormModal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>Detail Data Pengajuan {{ absence_name }}</h3>
                </template>
                <template #body>
                    <div class="row">
                        <!-- left -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">NIP</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="nik"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Nama</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="profile_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jenis</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="absence_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Deskripsi</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="absence_description"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- middle -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Tanggal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="absence_date"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Dari</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="absence_start_date"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Sampai</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="absence_end_date"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Approval</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="approval_1" :option="approvalOptions" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" v-if="absence_latitude != '' && absence_longitude != ''">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Lokasi</label>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="location" @click="location()">Klik disini</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="absence_attachment != ''">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Foto</label>
                                    </div>
                                    <div class="col-md-6">
                                        <img :src="absence_attachment" class="images" alt="foto absen">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button v-if="int_approval_1 == 0" class="modal-default-button btn btn-success btn-sm me-1"
                        @click="submit">Simpan</button>
                    <button class="modal-default-button btn btn-secondary btn-sm me-1" @click="close">Tutup</button>
                </template>
            </FormModal>
        </Teleport>
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import FormInput from '@/components/forms/FormInput.vue';
import FormModal from '@/components/forms/FormModal.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, FormInput, FormModal, FormSelect },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            { text: "Penempatan", value: "customer_name", width: 200, sortable: true },
            { text: "Nama", value: "profile_name", width: 200, sortable: true },
            { text: "Jenis", value: "absence_name", width: 160, sortable: true },
            { text: "Deskripsi", value: "absence_description", width: 200, sortable: true },
            { text: "Tgl Pengajuan", value: "absence_date", width: 150, sortable: true },
            { text: "Status Pengajuan", value: "approval_1", width: 170, sortable: true },
            { text: "Aksi", value: "action", width: 50 },
        ];

        return {
            headers
        };
    },
    mounted() {
        this.getData()
    },
    data() {
        return {
            title: 'Cuti',
            searchValue: '',
            items: [],
            showModal: false,
            absence_latitude: null,
            absence_longitude: null,
            absence_attachment: null,
            id: null,
            int_approval_1: null,
            approval_1: null,
            approvalOptions: [
                { value: 1, text: 'DISETUJUI' },
                { value: 2, text: 'DITOLAK' }
            ]
        };
    },
    methods: {
        showAlert(id) {
            this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.hapus(id)
                }
            })
        },
        // get data at the beginning
        getData() {
            // hit api
            return axios
                .get(import.meta.env.VITE_API_PATH + "trx/absence/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // show response success
                    // loader.hide();
                    const data = response.data.data;
                    console.log(data)
                    data.forEach(item => {
                        this.items.push(
                            {
                                id: item.id,
                                nik: item.nik,
                                absence_name: item.absence_name,
                                customer_name: item.customer_name,
                                absence_description: item.absence_description,
                                absence_start_date: item.absence_start_date,
                                absence_end_date: item.absence_end_date,
                                absence_latitude: item.absence_latitude,
                                absence_longitude: item.absence_longitude,
                                // approval HR
                                approval_1: (item.approval_1 == 1 ? 'Disetujui' : (item.approval_1 == 2 ? 'Ditolak' : 'Menunggu')),
                                int_approval_1: item.approval_1,
                                absence_date: item.absence_date,
                                profile_name: item.profile_name,
                                absence_attachment: item.absence_attachment
                            }
                        )
                    });
                })
                .catch((e) => {
                    // loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        modal(item) {
            console.log(item)
            this.showModal = true;
            this.id = item.id;
            this.nik = item.nik;
            this.profile_name = item.profile_name;
            this.absence_name = item.absence_name;
            this.absence_date = item.absence_date;
            this.absence_description = item.absence_description;
            this.absence_start_date = item.absence_start_date;
            this.absence_end_date = item.absence_end_date;
            this.absence_latitude = item.absence_latitude;
            this.absence_longitude = item.absence_longitude;
            this.absence_attachment = item.absence_attachment;
            this.approval_1 = item.int_approval_1;
            this.int_approval_1 = item.int_approval_1;
        },
        close: function () {
            this.showModal = false;
        },
        location() {
            window.open('https://maps.google.com/?q=' + this.absence_latitude + ',' + this.absence_longitude, '_blank');
        },
        async submit() {
            let post = {
                id: this.id,
                nik: this.nik,
                approval_1: this.approval_1,
                updated_by: localStorage.getItem('id')
            };

            console.log(post);

            // show loader
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/absence/edit', post)
                .then((r) => {
                    if (r.status) {
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        this.showModal = false; // close modal
                        toast.success("Data berhasil disimpan");
                        loader.hide();
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        }
    }
}
</script>

<style scoped>
.images {
    width: 200px;
    border-radius: 20px;
}

.location:hover {
    color: #6d737c;
    text-decoration: underline;
    cursor: pointer;
}
</style>