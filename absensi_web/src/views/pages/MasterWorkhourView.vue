<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Perusahaan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="customer_code" :option="customerOptions" @change="getData()">
                                        </FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Nama</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="workhour_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Jam Masuk</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="workhour_in"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Jam Pulang</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="workhour_out"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Kode</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="workhour_code" readonly></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Total Jam Kerja</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="total_hour"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Total Hari Kerja</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="total_day"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Keterangan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="keterangan"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3" style="margin: auto;">
                                <Button type="submit" @click="submit">Simpan</Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" buttons-pagination show-index
                border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                    <button type="button" class="btn btn-danger btn-sm" @click="showAlert(item.id)">Hapus</button>
                </template>
            </EasyDataTable>
        </div>



        <!-- modals -->
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <FormModal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>Data Master Jam Kerja</h3>
                </template>
                <template #body>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Perusahaan</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormSelect v-model="update_customer_code" :option="customerOptions"
                                                @change="getUserByCust()"></FormSelect>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Kode</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_workhour_code" readonly></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Nama</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_workhour_name"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_workhour_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_workhour_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="">Total Jam Kerja</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_total_hour"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="">Total Hari Kerja</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_total_day"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="">Keterangan</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_keterangan"></FormInput>
                                        </div>
                                    </div>
                                </div>
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
import FormSelect from '@/components/forms/FormSelect.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

// declare loader
let loader = useLoading();
export default {
    components: { Pages, Date, FormInput, Button, FormModal, FormSelect },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            { text: "Kode", value: "workhour_code", sortable: true },
            { text: "Nama", value: "workhour_name", width: 150, sortable: true },
            { text: "Jam Masuk", value: "workhour_in", width: 150, sortable: true },
            { text: "Jam Pulang", value: "workhour_out", width: 150, sortable: true },
            { text: "Total Jam", value: "total_hour", width: 150, sortable: true },
            { text: "Total Hari", value: "total_day", width: 150, sortable: true },
            { text: "Keterangan", value: "keterangan", width: 150, sortable: true },
            { text: "Aksi", value: "action", width: 150 },
        ];
        const items = ref([]);

        return {
            headers,
            items,
        };
    },
    data() {
        return {
            title: 'Master Jadwal Kerja',
            id: null,
            customer_code: null,
            workhour_code: null,
            workhour_name: null,
            workhour_out: null,
            workhour_in: null,
            total_hour: null,
            total_day: null,
            keterangan: null,
            showModal: false,
            data: '',
            modalData: '',
            searchValue: '',
            customerOptions: [],
        };
    },
    mounted() {
        // this.getData(),
        this.getCode(),
            this.customerData()
    },
    methods: {
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
                            value: item.customer_code,
                            text: item.customer_name,
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
        async getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterWorkhour/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    this.workhour_code = response.data;
                })
                .catch((e) => {
                    // loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
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
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterWorkhour/getByCustomerCode/" + this.customer_code, {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    this.items = [];
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push({
                            id: item.id,
                            customer_code: item.customer_code,
                            workhour_code: item.workhour_code,
                            workhour_name: item.workhour_name,
                            workhour_in: item.workhour_in,
                            workhour_out: item.workhour_out,
                            total_hour: item.total_hour,
                            total_day: item.total_day,
                            keterangan: item.keterangan,
                        })
                    });
                })
                .catch((e) => {
                    console.log(e)
                    // loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // validation create & update
        validation: function () {
            if (this.workhour_code == null || this.workhour_code == '') {
                toast.warning("Kode tidak boleh kosong");
                return false;
            } else if (this.workhour_name == null || this.workhour_name == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.workhour_in == null || this.workhour_in == '') {
                toast.warning("Jam masuk tidak boleh kosong");
                return false;
            } else if (this.workhour_out == null || this.workhour_out == '') {
                toast.warning("Jam pulang tidak boleh kosong");
                return false;
            } else if (this.total_hour == null || this.total_hour == '') {
                toast.warning("Total jam kerja tidak boleh kosong");
                return false;
            } else if (this.total_day == null || this.total_day == '') {
                toast.warning("Total hari kerja tidak boleh kosong");
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

            // show loader
            loader.show({
                loader: 'bars'
            });

            let post = {
                customer_code: this.customer_code,
                workhour_code: this.workhour_code,
                workhour_name: this.workhour_name,
                workhour_out: this.workhour_out,
                workhour_in: this.workhour_in,
                total_hour: this.total_hour,
                total_day: this.total_day,
                keterangan: this.keterangan,
                created_by: localStorage.getItem('id')
            };

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterWorkhour/add', post)
                .then((r) => {
                    console.log(r)
                    if (r.status) {
                        this.workhour_code = null;
                        this.workhour_name = null;
                        this.workhour_out = null;
                        this.workhour_in = null;
                        this.total_hour = null;
                        this.total_day = null;
                        this.keterangan = null;
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        this.getCode(); // generate ulang kode
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
            this.update_customer_code = items.customer_code
            this.update_workhour_code = items.workhour_code;
            this.update_workhour_name = items.workhour_name;
            this.update_workhour_out = items.workhour_out;
            this.update_workhour_in = items.workhour_in;
            this.update_total_hour = items.total_hour;
            this.update_total_day = items.total_day;
            this.update_keterangan = items.keterangan;
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_workhour_code == null || this.update_workhour_code == '') {
                toast.warning("Kode tidak boleh kosong");
                return false;
            } else if (this.update_workhour_name == null || this.update_workhour_name == '') {
                toast.warning("Nama Perusahaan tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                customer_code: this.customer_code,
                workhour_code: this.update_workhour_code,
                workhour_name: this.update_workhour_name,
                workhour_out: this.update_workhour_out,
                workhour_in: this.update_workhour_in,
                total_hour: this.update_total_hour,
                total_day: this.update_total_day,
                keterangan: this.update_keterangan,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterWorkhour/edit', update)
                .then((r) => {
                    if (r.status) {
                        this.items = [];
                        // this.getData();
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                        loader.hide();
                    }
                }).catch((e) => {
                    console.log(e)
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        close: function () {
            this.showModal = false;
        },
        delete: async function (id) {
            let del = {
                id: id,
                created_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterWorkhour/delete', del)
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // response success
                        toast.success("Data berhasil dihapus");
                        setInterval(() => {
                            location.reload();
                        }, 2000);
                    }
                }).catch((e) => {
                    loader.hide();
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