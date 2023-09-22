<template>
    <Pages title="Kehadiran">
        <div class="container-fluid">
            <div class="mb-4">
                <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Perusahaan</label>
                                </div>
                                <div class="col-md-8" v-if="role == 2">
                                    <v-select v-model="customer" :options="customerOptions"
                                        @update:modelValue="getUserByCust(); getCutoff();"></v-select>
                                </div>
                                <div class="col-md-8" v-if="role == 3">
                                    <v-select v-model="customer" :options="customerOptions"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Nama</label>
                                </div>
                                <div class="col-md-6">
                                    <v-select v-model="user" :options="userOptions"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Tanggal</label>
                                </div>
                                <div class="col-md-6">
                                    <DateRange v-model="date"></DateRange>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-6">
                                    <Button type="submit" @click="getData()">Show</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <button type="button" class="btn btn-success btn-sm" style="float: right;" @click="excel()">Excel</button>
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" buttons-pagination show-index
                border-cell table-class-name="customize-table">
                <!-- <template #item-attendee_time_in="{ attendee_time_in, item }">
                    <a @click="modal(item)">{{ attendee_time_in }}</a>
                </template> -->
                <template #item-location="{ attendee_latitude_in, attendee_longitude_in }">
                    <a :href="'https://maps.google.com/?q=' + attendee_latitude_in + ',' + attendee_longitude_in"
                        target="_blank">
                        Klik disini</a>
                </template>

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
                    <h3>Detail Data Kehadiran</h3>
                </template>
                <template #body>
                    <div class="row">
                        <div class="col-md-4">
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
                                        <label for="">Tanggal</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="attendee_date"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jam Masuk</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="attendee_time_in"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jam Pulang</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="attendee_time_out"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jadwal Kerja</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="schedule_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jadwal Jam Masuk</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="schedule_in"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Jadwal Jam Pulang</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput readonly v-model="schedule_out"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Lokasi Masuk</label>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="location" @click="locationDatang()">Klik disini</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Lokasi Pulang</label>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="location" @click="locationPulang()">Klik disini</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Foto</label>
                                        <!-- </div>
                                    <div class="col-md-6"> -->
                                        <img :src="images" class="images" alt="foto kehadiran">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button class="modal-default-button btn btn-secondary btn-sm me-1" @click="close">Tutup</button>
                </template>
            </FormModal>
        </Teleport>
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import Button from '@/components/forms/FormButton.vue';
import DateRange from '@/components/forms/FormDateRange.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import FormModal from '@/components/forms/FormModal.vue';
import FormInput from '@/components/forms/FormInput.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, Button, DateRange, FormSelect, FormModal, FormInput },
    props: {
        params: {
            default: null
        }
    },
    data() {
        return {
            title: 'Dashboard',
            // binding data modals
            images: null,
            attendee_date: null,
            attendee_time_in: null,
            attendee_latitude_in: null,
            attendee_longitude_in: null,
            attendee_latitude_out: null,
            attendee_longitude_out: null,
            customer_name: null,
            schedule_name: null,
            schedule_in: null,
            schedule_out: null,

            role: localStorage.getItem('mst_roles_id'),
            searchValue: '',
            date: null,
            customerOptions: [],
            userOptions: [],
            user: '',
            exportLink: '',
            customer: '',
            showModal: false,
            items: [],
        };
    },
    setup() {
        const headers = [
            { text: "NIK", value: "nik", sortable: true },
            { text: "Nama", value: "profile_name", sortable: true },
            { text: "Tanggal", value: "attendee_date", sortable: true },
            { text: "Jadwal", value: "schedule_name", sortable: true },
            { text: "Masuk", value: "attendee_time_in", sortable: true },
            { text: "Pulang", value: "attendee_time_out", sortable: true },
            // { text: "Lokasi", value: "location", sortable: true },
            { text: "Keterlambatan", value: "late_duration", sortable: true },
            { text: "Aksi", value: "action" }
        ];

        return {
            headers
        };
    },
    mounted() {
        this.customerData();
    },
    methods: {
        // logged() {
        //     console.log(this.customer.code)
        // },
        modal(item) {
            console.log(item)
            this.showModal = true;
            this.nik = item.nik;
            // this.images = item.images;
            this.images = item.img_in;
            this.attendee_date = item.attendee_date;
            this.attendee_time_in = item.attendee_time_in;
            this.attendee_time_out = item.attendee_time_out;
            this.attendee_latitude_in = item.attendee_latitude_in;
            this.attendee_longitude_in = item.attendee_longitude_in;
            this.attendee_latitude_out = item.attendee_latitude_out;
            this.attendee_longitude_out = item.attendee_longitude_out;
            this.schedule_name = item.schedule_name;
            this.schedule_in = item.schedule_in;
            this.schedule_out = item.schedule_out;
            this.profile_name = item.profile_name;
            this.customer_name = item.customer_name;
        },
        close: function () {
            this.showModal = false;
        },
        async customerData() {
            if (this.role == 2) {
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
            } else {
                return axios
                    .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/getByCustomerCode/" + localStorage.getItem('customer_code'), {
                        dataType: "json",
                    })
                    .then((response) => {
                        console.log(response);
                        // binding data
                        loader.hide();
                        const data = response.data.data;
                        this.customer = {
                            code: data.customer_code,
                            label: data.customer_name
                        };
                        this.customerOptions.push({
                            label: data.customer_name,
                            code: data.customer_code
                        })
                        this.getUserByCust();
                        this.getCutoff();
                    })
                    .catch((e) => {
                        console.log(e)
                        // if error / fail then show response
                        loader.hide();
                        const err = e.response.data;
                        toast.error(err.message);
                    });
            }
        },
        async getUserByCust() {
            if (this.customer == null) {
                this.date = [];
                return false;
            }
            this.userOptions = [];
            this.user = '';
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/user/getBy/" + this.customer.code, {
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
                    // if error / fail then show response
                    const err = e.response.data;
                    // if unauthorize then logout
                    if (err.code == 401) {
                        localStorage.clear()
                        sessionStorage.clear();
                        toast.error(err.message);
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        getCutoff() {
            if (this.customer == null) {
                this.date = [];
                return false;
            }
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCutoff/getByCustomerCode/" + this.customer.code, {
                    dataType: "json",
                })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    console.log(data)

                    const date = new Date();
                    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

                    if (data.cutoff_start > data.cutoff_end) {
                        this.date = [
                            date.getFullYear() + '-' + date.getMonth() + '-' + data.cutoff_start,
                            date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + data.cutoff_end
                        ]
                    } else if (data.cutoff_start < data.cutoff_end) {
                        if (data.cutoff_end > new Date(lastDay).getDate()) {
                            this.date = [
                                date.getFullYear() + '-' + date.getMonth() + '-' + data.cutoff_start,
                                date.getFullYear() + '-' + (date.getMonth()) + '-' + new Date(lastDay).getDate()
                            ]
                        } else {
                            this.date = [
                                date.getFullYear() + '-' + date.getMonth() + '-' + data.cutoff_start,
                                date.getFullYear() + '-' + (date.getMonth()) + '-' + date.cutoff_end
                            ]
                        }
                    }

                })
                .catch((e) => {
                    console.log(e)
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getData() {
            loader.show({
                loader: 'bars'
            });
            let datefrom = null;
            let dateto = null;
            if (this.date != null) {
                const dtFrom = new Date(this.date[0]);
                const dtTo = new Date(this.date[1]);
                datefrom = dtFrom.getFullYear() + "-" + (dtFrom.getMonth() + 1) + "-" + dtFrom.getDate();
                dateto = dtTo.getFullYear() + "-" + (dtTo.getMonth() + 1) + "-" + dtTo.getDate();
            }
            // clear table
            this.items = [];
            let userId = null;
            if (this.user == '' || this.user == null) {
                userId = '';
            } else {
                userId = this.user.code;
            }
            const post = {
                customer: this.customer.code,
                from: datefrom,
                to: dateto,
                users_id: userId
            }
            console.log(post);
            // hit api
            return axios
                .post(import.meta.env.VITE_API_PATH + "trx/attendee/getRekapByDate", post)
                .then((response) => {
                    // binding data
                    loader.hide();
                    const data = response.data.data;
                    console.log(data)
                    data.forEach(item => {
                        this.items.push({
                            id: item.id,
                            nik: item.nik,
                            profile_name: item.profile_name,
                            schedule_name: item.schedule_name,
                            schedule_in: item.schedule_in,
                            schedule_out: item.schedule_out,
                            attendee_date: item.attendee_date,
                            attendee_time_in: item.attendee_time_in,
                            attendee_time_out: item.attendee_time_out,
                            attendee_latitude_in: item.attendee_latitude_in,
                            attendee_longitude_in: item.attendee_longitude_in,
                            attendee_latitude_out: item.attendee_latitude_out,
                            attendee_longitude_out: item.attendee_longitude_out,
                            img_in: item.img_in,
                            img_out: item.img_out,
                            late_duration: item.late_duration.slice(0, -3),
                        })
                    });
                })
                .catch((e) => {
                    // if error / fail then show response
                    loader.hide();
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        locationDatang() {
            window.open('https://maps.google.com/?q=' + this.attendee_latitude_in + ',' + this.attendee_longitude_in, '_blank');
        },
        locationPulang() {
            window.open('https://maps.google.com/?q=' + this.attendee_latitude_out + ',' + this.attendee_longitude_out, '_blank');
        },
        async excel() {
            loader.show({
                loader: 'bars'
            });
            this.exportLink = '';
            // check apakah perusahaan sudah dipilih atau belum
            if (this.customer == null || this.customer == '') {
                toast.error('Silahkan pilih perusahaan terlebih dahulu')
                loader.hide();
                return false;
            }

            const dtFrom = new Date(this.date[0]);
            const dtTo = new Date(this.date[1]);
            const dateFrom = dtFrom.getFullYear() + "-" + (dtFrom.getMonth() + 1) + "-" + dtFrom.getDate();
            const dateTo = dtTo.getFullYear() + "-" + (dtTo.getMonth() + 1) + "-" + dtTo.getDate();
            const post = {
                customer: this.customer.code,
                users_id: this.user.code,
                from: dateFrom,
                to: dateTo,
            }
            return axios.post(import.meta.env.VITE_API_PATH + "trx/attendee/exportData", post)
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

<style scoped>
.modal-container {
    /* display: inline-block; */
    /* width: auto; */
    width: 90%;
}

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