<template>
    <Pages title="Jadwal Jam Kerja Perusahaan">
        <div class="container-fluid">
            <div class="mb-4">
                <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">Perusahaan</label>
                                    </div>
                                    <div class="col-md-8">
                                        <v-select v-model="customer" :options="customerOptions"
                                            @update:modelValue="getData()"></v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" v-for="(input, k) in scheduleForm" :key="k">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for=""><b>Jadwal {{ k + 1 }}</b></label>
                                            </div>
                                            <div class="col-md-6">
                                                <FormSelect class="form-control" v-model="input.schedule_code"
                                                    :option="scheduleOptions"
                                                    @change="getScheduleDetail(input.schedule_code, k)" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for=""><b>Kode Jadwal</b></label>
                                            </div>
                                            <div class="col-md-6">
                                                <FormInput placeholder="Kode Jadwal" class="form-control"
                                                    v-model="input.schedule_code" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <button class="btn btn-danger btn-sm mt-3" @click="removeSchedule(k)"
                                            v-show="k || (!k && scheduleForm.length > 1)">Hapus Form</button>
                                        <button class="btn btn-success btn-sm mt-3 ms-2" @click="addSchedule(k)"
                                            v-show="k == scheduleForm.length - 1">Tambah Form</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row" v-for="(i, k) in scheduleForm[k].scheduleDaily" :key="k">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="">Hari</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <FormInput readonly placeholder="Hari" class="form-control"
                                                            v-model="i.hari" />
                                                        <FormInput hidden class="form-control" v-model="i.day" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="">Jam Masuk</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <FormInput type="time" placeholder="Jam Masuk" class="form-control"
                                                            v-model="i.schedule_in" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="">Jam Pulang</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <FormInput type="time" placeholder="Jam Pulang" class="form-control"
                                                            v-model="i.schedule_out" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <hr> -->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3 mt-4" style="margin: auto;">
                                    <Button type="submit" @click="submit">Simpan</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span>Search </span>
            <button type="button" class="btn btn-primary btn-sm" style="float: right;" @click="uploadSchedule()">Upload
                Jadwal</button>
            <button type="button" class="btn btn-success btn-sm me-1" style="float: right;" @click="excel()">Unduh
                Template</button>
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
                    <h3>Detail Jadwal</h3>
                </template>
                <template #body>
                    <div class="row">
                        <!-- left -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Hari</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="day"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Kode Jadwal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="schedule_code"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Nama Jadwal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="schedule_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jam Masuk</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="time" v-model="schedule_in"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jam Pulang</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="time" v-model="schedule_out"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button class="modal-default-button btn btn-success btn-sm me-1" @click="update">Ubah</button>
                    <button class="modal-default-button btn btn-secondary btn-sm me-1" @click="close">Tutup</button>
                </template>
            </FormModal>
        </Teleport>

        <!-- modal upload -->
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
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import FormInput from '@/components/forms/FormInput.vue';
import FormModal from '@/components/forms/FormModal.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import Button from '@/components/forms/FormButton.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';
import * as XLSX from 'xlsx/xlsx.mjs';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, FormInput, FormModal, FormSelect, Button },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            { text: "Nama Jadwal", value: "schedule_name", sortable: true },
            { text: "Jam Masuk", value: "schedule_in", sortable: true },
            { text: "Jam Pulang", value: "schedule_out", sortable: true },
            { text: "Hari", value: "hari", sortable: true },
            { text: "Aksi", value: "action", width: 50 },
        ];

        const headersUpload = [
            { text: "Kode", value: "schedule_code", sortable: true },
            { text: "Nama", value: "schedule_name", sortable: true },
            { text: "Jam Masuk", value: "schedule_in", sortable: true },
            { text: "Jam Pulang", value: "schedule_out", sortable: true },
            { text: "Hari", value: "hari", sortable: true },
        ];

        return {
            headers,
            headersUpload
        };
    },
    mounted() {
        this.getJadwal();
        this.customerData();
    },
    data() {
        return {
            title: 'Cuti',
            searchValue: '',
            items: [],
            showModal: false,
            customerOptions: [],
            exportLink: '',
            customer: '',
            scheduleOptions: [],
            scheduleForm: [{
                schedule_code: null,
                schedule_name: null,
                scheduleDaily: [],
            }],
            showModalUpload: false,
            itemsUpload: [],
        };
    },
    methods: {
        addSchedule() {
            this.scheduleForm.push({
                schedule_code: null,
                schedule_name: null,
                scheduleDaily: [],
            })
        },
        removeSchedule(index) {
            this.scheduleForm.splice(index, 1)
        },
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
        async excel() {
            loader.show({
                loader: 'bars'
            });
            this.exportLink = '';
            const post = {
                customer_code: localStorage.getItem('customer_code')
            }
            return axios.post(import.meta.env.VITE_API_PATH + "trx/siteSchedule/exportData", post)
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
        },
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
        getJadwal() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterSchedule/getAll", {
                    dataType: "json",
                })
                .then((response) => {
                    const data = response.data.data;
                    data.forEach(item => {
                        this.scheduleOptions.push({
                            text: item.schedule_name,
                            value: item.schedule_code
                        })
                    });
                })
                .catch((e) => {
                    // loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getScheduleDetail(schedule_code, k) {
            if (schedule_code == '') {
                toast.error('Pilih Jadwal')
                return false;
            }
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterSchedule/getByScheduleCode/" + schedule_code, {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();

                    function getWeekdayNumber(str) {
                        let date = new Date(str);
                        if (date.toString() === 'Invalid Date')
                            return str.includes('Monday') ? 1
                                : str.includes('Tuesday') ? 2
                                    : str.includes('Wednesday') ? 3
                                        : str.includes('Thursday') ? 4
                                            : str.includes('Friday') ? 5
                                                : str.includes('Saturday') ? 6
                                                    : str.includes('Sunday') ? 0
                                                        : undefined;

                        return date.getDay();
                    }

                    const data = response.data.data;
                    // this.scheduleForm[k] = [];
                    this.scheduleForm[k].schedule_name = data[0].schedule_name;
                    // this.scheduleForm[k].schedule_code = data[0].schedule_code;
                    this.scheduleForm[k].scheduleDaily = [];
                    let hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                    data.forEach(item => {
                        this.scheduleForm[k].scheduleDaily.push({
                            id: item.id,
                            day: item.day,
                            hari: hari[getWeekdayNumber(item.day)],
                            schedule_in: item.schedule_in,
                            schedule_out: item.schedule_out,
                            weekday: getWeekdayNumber(item.day)
                        })
                    });
                    console.log(this.scheduleForm)
                })
                .catch((e) => {
                    console.log(e)
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async customerData() {
            if (this.customer.code != '') {
                return axios
                    .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/show", {
                        dataType: "json",
                    })
                    .then((response) => {
                        // binding data
                        loader.hide();
                        const data = response.data.data;
                        data.forEach(item => {
                            this.customerOptions.push({
                                label: item.customer_name,
                                code: item.customer_code
                            })
                        });
                    })
                    .catch((e) => {
                        // if error / fail then show response
                        loader.hide();
                        const err = e.response.data;
                        toast.error(err.message);
                    });
            }
        },
        // get data at the beginning
        getData() {
            // hit api
            return axios
                .get(import.meta.env.VITE_API_PATH + "trx/siteSchedule/getScheduleByCustomer/" + this.customer.code, {
                    dataType: "json",
                })
                .then((response) => {
                    // show response success
                    // loader.hide();
                    let hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                    const data = response.data.data;
                    console.log(data)
                    data.forEach(item => {
                        this.items.push(
                            {
                                id: item.id,
                                schedule_name: item.schedule_name,
                                schedule_code: item.schedule_code,
                                schedule_in: item.schedule_in,
                                schedule_out: item.schedule_out,
                                day: item.day,
                                hari: hari[item.weekday],
                                weekday: item.weekday
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
        },// submit data
        submit: async function () {
            if (this.customer.code == '' || this.customer.code == null) {
                toast.error('Customer tidak boleh kosong');
                return false;
            } else if (this.scheduleForm[0].schedule_code == null) {
                toast.error('Jadwal tidak boleh kosong');
                return false;
            }
            // show loader
            loader.show({
                loader: 'bars'
            });
            let post = {
                customer_code: this.customer.code,
                schedule: this.scheduleForm,
                created_by: localStorage.getItem('id'),
            };
            console.log(post)

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/siteSchedule/add', post)
                .then((response) => {
                    console.log(response)
                    if (response.data.status) {
                        this.items = []; // clear table
                        this.scheduleForm = [{
                            schedule_code: null,
                            schedule_name: null,
                            scheduleDaily: [],
                        }];
                        this.getData(); // fetch ulang data table
                        this.getJadwal();
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
        modal(item) {
            console.log(item)
            this.showModal = true;
            this.id = item.id;
            this.schedule_name = item.schedule_name;
            this.schedule_code = item.schedule_code;
            this.schedule_in = item.schedule_in;
            this.schedule_out = item.schedule_out;
            this.day = item.day;
            this.weekday = item.weekday;
        },
        close: function () {
            this.showModal = false;
            this.showModalUpload = false;
        },
        async update() {
            let post = {
                id: this.id,
                schedule_name: this.schedule_name,
                schedule_code: this.schedule_code,
                schedule_in: this.schedule_in,
                schedule_out: this.schedule_out,
                day: this.day,
                weekday: this.weekday,
                updated_by: localStorage.getItem('id')
            };

            console.log(post);

            // show loader
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'trx/siteSchedule/edit', post)
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
</style>