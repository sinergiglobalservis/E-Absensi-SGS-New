<template>
    <Pages :title="title">
        <div class="container-fluid">
            <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <!-- <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Kode</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="holiday_code" readonly></FormInput>
                                </div>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Nama Hari Libur</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="holiday_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Tanggal</label>
                                </div>
                                <div class="col-md-6">
                                    <FormDate v-model="holiday_date"></FormDate>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Jenis Libur</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="holiday_national" :option="holidayOption"></FormSelect>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                                <div class="col-md-3">
                                    <Button type="submit" @click="submit">Simpan</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <button type="button" class="btn btn-primary btn-sm" style="float: right;" @click="uploadHoliday()">Upload
                Hari Libur</button>
            <button type="button" class="btn btn-success btn-sm me-1" style="float: right;" @click="excel()">Unduh
                Template</button>
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" buttons-pagination show-index
                border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                    <button type="button" class="btn btn-danger btn-sm" @click="showAlert(item.id)">Hapus</button>
                </template>
            </EasyDataTable>
        </div>

        <!-- modal upload hari libur -->
        <Teleport to="body">
            <FormModal :show="showModalUpload" @close="showModalUpload = false">
                <template #header>
                    <h3>Upload Jadwal</h3>
                </template>
                <template #body>
                    <div class="form-group">
                        <div class="row">
                            <FormInput type="file" @change="previewFiles"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            </FormInput>
                            <span style="color: red;font-size: 14px;margin: 10px;">*Upload file dalam bentuk xls</span>
                        </div>
                    </div>
                    <EasyDataTable :headers="headersUpload" :items="itemsUpload" :search-value="searchValue"
                        :rows-per-page="10" buttons-pagination show-index border-cell table-class-name="customize-table">
                    </EasyDataTable>
                </template>
                <template #footer>
                    <button class="btn btn-success btn-sm me-1" @click="upload">Upload</button>
                    <button class="modal-default-button btn btn-secondary btn-sm" @click="close">Tutup</button>
                </template>
            </FormModal>
        </Teleport>

        <!-- modals -->
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <FormModal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>Ubah Data Libur</h3>
                </template>
                <template #body>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Nama</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_holiday_name"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Tanggal</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_holiday_date"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Jenis Libur</label>
                            </div>
                            <div class="col-md-6">
                                <FormSelect v-model="update_holiday_national" :option="holidayOption"></FormSelect>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button class="modal-default-button btn btn-secondary btn-sm me-1" @click="close">Tutup</button>
                    <button class="btn btn-success btn-sm" @click="update">Update</button>
                </template>
            </FormModal>
        </Teleport>
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import FormInput from '@/components/forms/FormInput.vue';
import Button from '@/components/forms/FormButton.vue';
import FormModal from '@/components/forms/FormModal.vue';
import FormDate from '@/components/forms/FormDate.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';
import * as XLSX from 'xlsx/xlsx.mjs';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

// declare loader
let loader = useLoading();
export default {
    components: { Pages, Date, FormInput, Button, FormModal, FormSelect, FormDate },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            { text: "Nama Hari Libur", value: "holiday_name", sortable: true },
            { text: "Tanggal", value: "holiday_date", sortable: true },
            { text: "Jenis", value: "holiday_national", sortable: true },
            { text: "Aksi", value: "action" },
        ];

        const headersUpload = [
            { text: "Nama", value: "holiday_name", sortable: true },
            { text: "Tanggal", value: "holiday_date", sortable: true },
            { text: "Jenis", value: "holiday_national_str", sortable: true }
        ];

        return {
            headers,
            headersUpload
        };
    },
    data() {
        return {
            title: 'Libur Nasional & Cuti Bersama',
            // submit binding data
            // holiday_code: null,
            items: [],
            itemsUpload: [],
            exportLink: '',
            holiday_name: null,
            holiday_date: null,
            holiday_national: 0,

            // update binding data
            id: null,
            update_holiday_name: null,
            update_holiday_date: null,
            update_holiday_national: null,
            showModal: false,
            data: '',
            modalData: '',
            searchValue: '',
            holidayOption: [
                // { value: 0, text: 'Cuti Bersama' },
                { value: 1, text: 'Libur Nasional' },
            ],
            showModalUpload: false,
        };
    },
    mounted() {
        this.getData()
    },
    methods: {
        // alert for delete
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
                    this.delete(id)
                }
            })
        },
        // get data at beginning
        async getData() {
            // loader.show({
            //     loader: 'bars'
            // });
            return axios
                .get(import.meta.env.VITE_API_PATH + "trx/holiday/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push(
                            {
                                id: item.id,
                                holiday_name: item.holiday_name,
                                holiday_date: item.holiday_date,
                                holiday_national: (item.holiday_national == 1 ? 'Libur Nasional' : 'Cuti Bersama'),
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
        // validation create & update
        validation: function () {
            if (this.holiday_name == null || this.holiday_name == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.holiday_date == null || this.holiday_date == '') {
                toast.warning("Tanggal tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            return true;
        },
        // submit data
        submit: async function () {
            // run validation
            if (!this.validation()) return false;

            let newDate = null;
            const dt = new Date(this.holiday_date);
            newDate = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

            let post = {
                holiday_name: this.holiday_name,
                holiday_date: newDate,
                holiday_national: this.holiday_national,
                created_by: localStorage.getItem('id')
            };
            // show loader
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/holiday/add', post)
                .then((r) => {
                    if (r.status) {
                        this.holiday_name = '';
                        this.holiday_date = '';
                        this.holiday_national = '';
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        toast.success("Data berhasil disimpan");
                        loader.hide();
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        modal(items) {
            // set data to pop up modal
            this.showModal = true;
            this.id = items.id;
            this.update_holiday_name = items.holiday_name;
            this.update_holiday_date = items.holiday_date;
            this.update_holiday_national = items.holiday_national;
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_holiday_name == null || this.update_holiday_name == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.update_holiday_date == null || this.update_holiday_date == '') {
                toast.warning("Tanggal tidak boleh kosong");
                return false;
            } else if (this.update_holiday_national == null || this.update_holiday_national == '') {
                toast.warning("Jenis libur tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                holiday_name: this.update_holiday_name,
                holiday_date: this.update_holiday_date,
                holiday_national: this.update_holiday_national,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/holiday/edit', update)
                .then((r) => {
                    if (r.status) {
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        toast.success("Data berhasil diubah");
                        loader.hide();
                    }
                }).catch((e) => {
                    console.log(e)
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // close: function () {
        //     this.showModal = false;
        // },
        delete: async function (id) {
            let del = {
                id: id,
                created_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/holiday/delete', del)
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // response success
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        toast.success("Data berhasil dihapus");
                        loader.hide();
                    }
                }).catch((e) => {
                    loader.hide();
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        uploadHoliday() {
            this.showModalUpload = true;
            // this.itemsUpload = [];
        },
        previewFiles(e) {
            const files = e.target.files, f = files[0];
            const reader = new FileReader();
            const that = this;
            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                // console.log(workbook);
                // return;
                const sheetName = workbook.SheetNames[0]
                /* DO SOMETHING WITH workbook HERE */
                const worksheet = workbook.Sheets[sheetName];
                const cleanWorksheet = XLSX.utils.sheet_to_json(worksheet)
                // return new Date(Math.round((cleanWorksheet - 25569) * 86400 * 1000));
                console.log(cleanWorksheet);

                cleanWorksheet.forEach(item => {
                    const dt = new Date(Math.round((item.tanggal - 25569) * 86400 * 1000));
                    const date = dt.getFullYear() + '-' + (dt.getMonth() + 1) + '-' + dt.getDate()

                    that.itemsUpload.push({
                        holiday_name: item.nama_hari_libur,
                        holiday_date: date,
                        holiday_national: item.libur_nasional,
                        holiday_national_str: (item.libur_nasional == 1 ? 'Libur Nasional' : 'Cuti Bersama'),
                        created_by: localStorage.getItem('id'),
                        updated_by: localStorage.getItem('id'),
                    })
                });
            };
            reader.readAsArrayBuffer(f);
        },
        async upload() {
            loader.show({
                loader: 'bars'
            });

            await axios.post(import.meta.env.VITE_API_PATH + 'trx/holiday/uploadHoliday', this.itemsUpload)
                .then((r) => {
                    loader.hide();
                    if (r.status) {
                        toast.success("Data berhasil disimpan");
                        loader.hide();
                        this.showModalUpload = false;

                        this.items = []; // clear table
                        this.itemsUpload = []; // clear modal table
                        this.getData(); // fetch ulang data table
                    }
                }).catch((e) => {
                    loader.hide();
                    // console.log(e)
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        close() {
            this.showModal = false;
            this.showModalUpload = false;
        },
        async excel() {
            loader.show({
                loader: 'bars'
            });
            this.exportLink = '';
            return axios.post(import.meta.env.VITE_API_PATH + "trx/holiday/exportData")
                .then((response) => {
                    // binding data
                    loader.hide();
                    const data = response.data.data;
                    this.exportLink = data;
                })
                .catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        }
        // logged: function(){
        //     console.log(this.strDate);
        // },
    }
}
</script>

<style scoped></style>