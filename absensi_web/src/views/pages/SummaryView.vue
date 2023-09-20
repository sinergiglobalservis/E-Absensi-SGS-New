<template>
    <Pages title="Summary">
        <div class="container-fluid">
            <div class="mb-4">
                <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Perusahaan</label>
                                </div>
                                <div class="col-md-6">
                                    <v-select v-model="customer" :options="customerOptions"
                                        @update:modelValue="getCutoff()"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Dari Tanggal</label>
                                </div>
                                <div class="col-md-6">
                                    <FormDate v-model="dateFrom"></FormDate>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Sampai Tanggal</label>
                                </div>
                                <div class="col-md-6">
                                    <FormDate v-model="dateTo"></FormDate>
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
                <template #item-action="item">
                    <!-- <img src="@/assets/img/trash.png" :width="20" alt="trash" @click="showAlert(item.id)"> <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                <button type="button" class="btn btn-danger btn-sm" @click="showAlert(item.id)">Hapus</button> -->
                </template>
            </EasyDataTable>
        </div>
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import Button from '@/components/forms/FormButton.vue';
import FormDate from '@/components/forms/FormDate.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, Button, FormDate },
    props: {
        params: {
            default: null
        }
    },
    data() {
        return {
            title: 'Dashboard',
            searchValue: '',
            date: null,
            customerOptions: [],
            cutoff_start: null,
            cutoff_end: null,
            dateFrom: '',
            dateTo: '',
            user: '',
            exportLink: '',
        };
    },
    setup() {
        const headers = [
            { text: "NIK", value: "nik", sortable: true },
            { text: "Nama", value: "profile_name", width: 180, sortable: true },
            { text: "Total Hari Kerja", value: "total_hari", sortable: true },
            { text: "Total Hari Terlambat", value: "total_terlambat", sortable: true },
        ];
        const items = ref([]);

        return {
            headers,
            items,
        };
    },
    mounted() {
        this.customerData();
    },
    methods: {
        logged() {
            console.log(this.customer.code)
        },
        async customerData() {
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
        },
        async getCutoff() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCutoff/getByCustomerCode/" + this.customer.code, {
                    dataType: "json",
                })
                .then((response) => {
                    // binding data
                    loader.hide();
                    const data = response.data.data;
                    this.dateFrom = new Date().getFullYear() + '-' + new Date().getMonth() + '-' + data.cutoff_start;
                    this.dateTo = new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + data.cutoff_end;
                })
                .catch((e) => {
                    // if error / fail then show response
                    loader.hide();
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getData() {
            loader.show({
                loader: 'bars'
            });
            // clear table
            this.items = [];
            const dfrom = new Date(this.dateFrom);
            const df = dfrom.getFullYear() + '-' + (dfrom.getMonth() + 1) + '-' + dfrom.getDate();
            const dto = new Date(this.dateTo);
            const dt = dto.getFullYear() + '-' + (dto.getMonth() + 1) + '-' + dto.getDate();
            const post = {
                customer_code: this.customer.code,
                dateFrom: df,
                dateTo: dt
            }
            console.log(post);
            // hit api
            return axios
                .post(import.meta.env.VITE_API_PATH + "trx/attendee/summary", post)
                .then((response) => {
                    console.log(response)
                    // binding data
                    loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push({
                            id: item.id,
                            nik: item.nik,
                            profile_name: item.profile_name,
                            total_hari: item.total_hari,
                            total_terlambat: item.total_terlambat
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
        async excel() {
            loader.show({
                loader: 'bars'
            });
            this.exportLink = '';
            // check apakah user sudah dipilih atau belum
            if (this.customer == null || this.customer == '') {
                toast.error('Silahkan pilih customer terlebih dahulu')
                loader.hide();
                return false;
            }
            if (this.dateFrom == null || this.dateFrom == '') {
                toast.error('Silahkan pilih tanggal mulai terlebih dahulu')
                loader.hide();
                return false;
            }
            if (this.dateTo == null || this.dateTo == '') {
                toast.error('Silahkan pilih tanggal selesai terlebih dahulu')
                loader.hide();
                return false;
            }

            const dfrom = new Date(this.dateFrom);
            const dto = new Date(this.dateTo);
            const df = dfrom.getFullYear() + "-" + (dfrom.getMonth() + 1) + "-" + dfrom.getDate();
            const dt = dto.getFullYear() + "-" + (dto.getMonth() + 1) + "-" + dto.getDate();
            const post = {
                customer_code: this.customer.code,
                dateFrom: df,
                dateTo: dt,
            }
            console.log(post);
            return axios.post(import.meta.env.VITE_API_PATH + "trx/attendee/exportDataSummary", post)
                .then((response) => {
                    // binding data
                    loader.hide();
                    console.log(response);
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