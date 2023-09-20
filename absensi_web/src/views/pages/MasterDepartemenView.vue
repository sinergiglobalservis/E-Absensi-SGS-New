<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Perusahaan</label>
                                </div>
                                <div class="col-md-6">
                                    <v-select v-model="customer_code" :options="customerOptions"
                                        @update:modelValue="getData()"></v-select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Kode Departemen</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="departemen_code" readonly></FormInput>
                                </div>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Nama Departemen</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput hidden v-model="departemen_code"></FormInput>
                                    <FormInput v-model="departemen_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <label for="">Nama Departemen</label> -->
                                </div>
                                <div class="col-md-3">
                                    <Button type="submit" @click="submit">Tambah</Button>
                                </div>
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
                    <h3>Data Master Departemen</h3>
                </template>
                <template #body>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Kode Departemen</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="departemen_code"></FormInput>
                                <FormInput type="hidden" v-model="id"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Nama Departemen</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_departemen_name"></FormInput>
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
            { text: "Kode Departemen", value: "departemen_code", sortable: true },
            { text: "Nama Departemen", value: "departemen_name", sortable: true },
            { text: "Perusahaan", value: "customer_name", sortable: true },
            { text: "Aksi", value: "action" },
        ];
        const items = ref([]);

        return {
            headers,
            items,
        };
    },
    data() {
        return {
            title: 'Master Departemen',
            id: null,
            customer_code: null,
            departemen_code: null,
            departemen_name: null,
            // update data binding
            update_departemen_code: null,
            update_departemen_name: null,
            update_customer_code: null,
            showModal: false,
            data: '',
            modalData: '',
            searchValue: '',
            customerOptions: [],
        };
    },
    mounted() {
        this.customerData(),
            this.getCode()
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
        // generate code
        getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterDepartemen/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    this.departemen_code = response.data;
                })
                .catch((e) => {
                    // loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
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
                    // console.log(e)
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // get data after select customer
        async getData() {
            loader.show({
                loader: 'bars'
            });
            if (this.customer_code == null) {
                loader.hide();
                this.items = [];
            } else {
                this.items = [];
                return axios
                    .get(import.meta.env.VITE_API_PATH + "mst/masterDepartemen/getByCustomerCode/" + this.customer_code.code, {
                        dataType: "json",
                    })
                    .then((response) => {
                        loader.hide();
                        const data = response.data.data;
                        data.forEach(item => {
                            this.items.push({
                                id: item.id,
                                customer_code: item.customer_code,
                                departemen_code: item.departemen_code,
                                departemen_name: item.departemen_name,
                                customer_name: item.customer_name
                            })
                        });
                    })
                    .catch((e) => {
                        loader.hide();
                        // if error / fail then show response
                        const err = e.response.data;
                        toast.error(err.message);
                    });
            }
        },
        // validation create & update
        validation: function () {
            if (this.departemen_code == null || this.departemen_code == '') {
                toast.warning("Kode Departemen tidak boleh kosong");
                return false;
            } else if (this.departemen_name == null || this.departemen_name == '') {
                toast.warning("Nama Departemen tidak boleh kosong");
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

            let post = {
                customer_code: this.customer_code.code,
                departemen_code: this.departemen_code,
                departemen_name: this.departemen_name,
                created_by: localStorage.getItem('id')
            };

            // show loader
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterDepartemen/add', post)
                .then((r) => {
                    loader.hide();
                    if (r.status) {
                        this.departemen_code = '';
                        this.departemen_name = '';
                        // this.customer_code = '';
                        this.items = []; // clear table
                        this.getCode();
                        this.getData(); // fetch ulang data table
                        toast.success("Data berhasil disimpan");
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
            this.update_departemen_code = items.departemen_code;
            this.update_departemen_name = items.departemen_name;
            this.update_customer_code = items.customer_code;
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_departemen_code == null || this.update_departemen_code == '') {
                toast.warning("Kode Departemen tidak boleh kosong");
                return false;
            } else if (this.update_departemen_name == null || this.update_departemen_name == '') {
                toast.warning("Nama Departemen tidak boleh kosong");
                return false;
            } else if (this.update_customer_code == null || this.update_customer_code == '') {
                toast.warning("Perusahaan tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                departemen_code: this.update_departemen_code,
                departemen_name: this.update_departemen_name,
                customer_code: this.update_customer_code,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterDepartemen/edit', update)
                .then((r) => {
                    if (r.status) {
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                        loader.hide();

                        this.items = []; // clear table
                        this.getCode();
                        this.getData(); // fetch ulang data table
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
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterDepartemen/delete', del)
                .then((r) => {
                    if (r.status) {
                        // response success
                        loader.hide();
                        this.items = []; // clear table
                        this.getCode();
                        this.getData(); // fetch ulang data table
                        toast.success("Data berhasil dihapus");
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