<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" v-if="roles == 2">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Perusahaan</label>
                            </div>
                            <div class="col-md-6">
                                <v-select v-model="customer" :options="customerOptions"
                                    @update:modelValue="getUserByCust(); getData(); getMasterSchedule();"></v-select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Nama Karyawan</label>
                            </div>
                            <div class="col-md-6">
                                <v-select v-model="user" :options="userOptions"></v-select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Jadwal</label>
                            </div>
                            <div class="col-md-6">
                                <FormSelect v-model="schedule_code" :option="scheduleOptions">
                                </FormSelect>
                            </div>
                        </div>
                    </div>
                    <div class=" form-group" v-if="isShifting == true">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Shift</label>
                            </div>
                            <div class="col-md-6">
                                <FormSelect v-model="shift" :option="shiftOptions"></FormSelect>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Mulai Tanggal</label>
                            </div>
                            <div class="col-md-6">
                                <FormDate v-model="schedule_start_date"></FormDate>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Sampai Tanggal</label>
                            </div>
                            <div class="col-md-6">
                                <FormDate v-model="schedule_end_date"></FormDate>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Permanen</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput type="checkbox" v-model="permanent" v-on:click="permanentCheck($event)">
                                </FormInput>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin: 20px 0;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-6">
                            <Button type="submit" @click="submit()">Simpan</Button>
                        </div>
                    </div>
                </div>
            </div>
            <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <button type="button" class="btn btn-primary btn-sm" style="float: right;" @click="uploadSchedule()">Upload
                Jadwal</button>
            <button type="button" class="btn btn-success btn-sm me-1" style="float: right;" @click="excel()">Unduh
                Template</button>
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" buttons-pagination show-index
                border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                </template>
            </EasyDataTable>
        </div>


        <!-- modal upload schedule -->
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
                            <span style="color: red;font-size: 14px;margin: 10px;">*Upload file dalam bentuk
                                xls</span>
                        </div>
                    </div>
                    <EasyDataTable :headers="headersUpload" :items="itemsUpload" :search-value="searchValue"
                        :rows-per-page="10" buttons-pagination show-index border-cell table-class-name="customize-table">
                        <template #item-action="item">
                            <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                        </template>
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
                    <h3>Data Jadwal</h3>
                </template>
                <template #body>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Nama</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_profile_name"></FormInput>
                                        <FormInput type="hidden" v-model="id"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Jadwal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_schedule" :option="scheduleOptions"></FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" v-if="isShifting == true">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Shift</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="shift" :option="shiftOptions"></FormSelect>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Mulai Tanggal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="date" v-model="update_schedule_start_date" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Sampai Tanggal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="date" v-model="update_schedule_end_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button class="btn btn-success btn-sm me-1" @click="update">Update</button>
                    <button class="modal-default-button btn btn-secondary btn-sm" @click="close">Tutup</button>
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
import FormSelect from '@/components/forms/FormSelect.vue';
import FormDate from '@/components/forms/FormDate.vue';
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
            { text: "Nama", value: "profile_name", sortable: true },
            { text: "Jadwal", value: "schedule_name", sortable: true },
            { text: "Mulai Tanggal", value: "schedule_start_date", sortable: true },
            { text: "Sampai Tanggal", value: "schedule_end_date", sortable: true },
            // { text: "Jam Masuk", value: "schedule_in", sortable: true },
            // { text: "Jam Pulang", value: "schedule_out", sortable: true },
            { text: "Aksi", value: "action" },
        ];

        const headersUpload = [
            { text: "Nama", value: "name", sortable: true },
            { text: "Jadwal", value: "nama_shift", sortable: true },
            { text: "Dari Tanggal", value: "schedule_start_date", sortable: true },
            { text: "Sampai Tanggal", value: "schedule_end_date", sortable: true },
        ];

        return {
            headers,
            headersUpload
        };
    },
    data() {
        return {
            title: 'Jadwal',
            roles: localStorage.getItem('mst_roles_id'),
            items: [],
            itemsUpload: [],
            id: null,
            user: null,
            userOptions: [],
            schedule_start_date: null,
            schedule_end_date: null,
            schedule_code: null,
            // upload modal
            showModalUpload: false,
            uploadData: [],
            // edit modal
            showModal: false,
            update_profile_name: null,
            update_schedule: null,
            update_schedule_start_date: null,
            update_schedule_end_date: null,
            data: '',
            modalData: '',
            searchValue: '',
            scheduleOptions: [],
            exportLink: '',
            permanent: false,
            isShifting: false,
            shiftOptions: [],
            shift: null,
            customer: null,
            customerOptions: [],
        };
    },
    mounted() {
        this.getAllCust();
        // this.getData();
        // this.getMasterSchedule();
        // this.getUserByCust();
    },
    methods: {
        permanentCheck(event) {
            if (event.target.checked) {
                this.schedule_end_date = '9999-12-30';
            } else {
                this.schedule_end_date = '';
            }
        },
        async getAllCust() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.customerOptions.push({
                            code: item.customer_code,
                            label: item.customer_name,
                        })
                    });
                })
                .catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getUserByCust() {
            this.userOptions = [];
            this.user = [];

            const cust_code = (this.customer == null ? localStorage.getItem('customer_code') : this.customer.code)
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/user/getBy/" + cust_code, {
                    dataType: "json",
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.userOptions.push({
                            code: item.id,
                            label: item.profile_name,
                        })
                    });
                })
                .catch((e) => {
                    console.log(e)
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        getMasterSchedule() {
            const cust_code = (this.customer == null ? localStorage.getItem('customer_code') : this.customer.code)

            this.scheduleOptions = [];

            return axios
                .get(import.meta.env.VITE_API_PATH + "trx/siteSchedule/groupScheduleByCustomerCode/" + cust_code, {
                    dataType: "json",
                })
                .then((response) => {
                    loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.scheduleOptions.push({
                            text: item.schedule_code,
                            value: item.schedule_code
                        })
                    });
                })
                .catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getData() {
            const cust_code = (this.customer == null ? localStorage.getItem('customer_code') : this.customer.code)

            this.items = [];
            return axios
                .get(import.meta.env.VITE_API_PATH + "trx/schedule/getScheduleByCustomer/" + cust_code, {
                    dataType: "json",
                })
                .then((response) => {
                    loader.hide();
                    const data = response.data.data;
                    if (data.length > 0) {
                        data.forEach(item => {
                            this.items.push({
                                id: item.id,
                                users_id: item.users_id,
                                profile_name: item.profile_name,
                                schedule_code: item.schedule_code,
                                schedule_name: item.schedule_name,
                                schedule_in: item.schedule_in,
                                schedule_out: item.schedule_out,
                                schedule_start_date: item.schedule_start_date,
                                schedule_end_date: item.schedule_end_date,
                            })
                        });
                    } else {
                        toast.warning('Belum ada jadwal tersimpan')
                    }
                })
                .catch((e) => {
                    loader.hide();
                    console.log(e)
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // insert jadwal
        async submit() {

            // show loader
            loader.show({
                loader: 'bars'
            });

            const sdate = new Date(this.schedule_start_date);
            const edate = new Date(this.schedule_end_date);

            const startDate = sdate.getFullYear() + "-" + (sdate.getMonth() + 1) + "-" + sdate.getDate();
            const endDate = edate.getFullYear() + "-" + (edate.getMonth() + 1) + "-" + edate.getDate();

            let post = {
                users_id: this.user.code,
                schedule_code: this.schedule_code,
                shift: this.shift,
                schedule_start_date: startDate,
                schedule_end_date: endDate,
                created_by: localStorage.getItem('id')
            };
            console.log(post);

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/schedule/add', post)
                .then((r) => {
                    loader.hide();
                    console.log(r)
                    if (r.status) {
                        this.items = [];
                        this.getData();
                        this.scheduleOptions = [];
                        this.getMasterSchedule();
                        this.shiftOptions = [];
                        this.user = null;
                        this.schedule_code = '';
                        this.schedule_start_date = null;
                        this.schedule_end_date = null;
                        this.permanent = false;
                        this.isShifting = false;
                        this.shift = null;
                        toast.success("Data berhasil disimpan");
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // pop up modal update
        modal(items) {
            // set data to pop up modal
            console.log(items)
            this.showModal = true;
            this.id = items.id;
            this.users_id = items.users_id;
            this.update_profile_name = items.profile_name;
            this.update_schedule = items.schedule_code;
            this.update_schedule_start_date = items.schedule_start_date;
            this.update_schedule_end_date = items.schedule_end_date;
        },
        // update data
        async update() {
            // run validation
            if (this.update_schedule == null || this.update_schedule == '') {
                toast.warning("Jadwal tidak boleh kosong");
                return false;
            }

            const sdate = new Date(this.update_schedule_start_date);
            const edate = new Date(this.update_schedule_end_date);

            const startDate = sdate.getFullYear() + "-" + (sdate.getMonth() + 1) + "-" + sdate.getDate();
            const endDate = edate.getFullYear() + "-" + (edate.getMonth() + 1) + "-" + edate.getDate();

            let update = {
                id: this.id,
                schedule_code: this.update_schedule,
                schedule_start_date: startDate,
                schedule_end_date: endDate,
                updated_by: localStorage.getItem('id'),
                users_id: this.users_id,
                created_by: localStorage.getItem('id'),
            };
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/schedule/edit', update)
                .then((r) => {
                    if (r.status) {
                        toast.success("Data berhasil diubah");
                        loader.hide();
                        this.showModal = false;

                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                    }
                }).catch((e) => {
                    // console.log(e)
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // pop up modal upload schedule
        uploadSchedule() {
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
                    const start = new Date(Math.round((item.schedule_start_date - 25569) * 86400 * 1000));
                    const stdate = start.getFullYear() + '-' + (start.getMonth() + 1) + '-' + start.getDate()
                    const end = new Date(Math.round((item.schedule_end_date - 25569) * 86400 * 1000));
                    const endate = end.getFullYear() + '-' + (end.getMonth() + 1) + '-' + end.getDate()

                    that.itemsUpload.push({
                        users_id: item.users_id,
                        name: item.name,
                        schedule_code: item.schedule_code,
                        nama_shift: item.nama_shift,
                        schedule_start_date: stdate,
                        schedule_end_date: endate,
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

            await axios.post(import.meta.env.VITE_API_PATH + 'trx/schedule/uploadSchedule', this.itemsUpload)
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
            const post = {
                customer_code: localStorage.getItem('customer_code')
            }
            return axios.post(import.meta.env.VITE_API_PATH + "trx/schedule/exportData", post)
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
    }
}
</script>

<style scoped></style>