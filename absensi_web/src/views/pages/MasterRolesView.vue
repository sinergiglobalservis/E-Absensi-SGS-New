<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Kode Role</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="role_code" readonly></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Nama Role</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="role_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Deskripsi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="role_description"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-3">
                                    <Button type="button" @click="submit">Simpan</Button>
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
                    <h3>Data Role</h3>
                </template>
                <template #body>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Kode Role</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_role_code"></FormInput>
                                <FormInput type="hidden" v-model="id"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Nama Role</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_role_name"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Deskripsi</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_role_description"></FormInput>
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
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, FormInput, Button, FormModal },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            { text: "Kode Role Akses", value: "role_code", sortable: true },
            { text: "Nama Role Akses", value: "role_name", sortable: true },
            { text: "Deskripsi", value: "role_description", sortable: true },
            { text: "Aksi", value: "action" },
        ];

        return {
            headers
        };
    },
    data() {
        return {
            title: 'Master Role Akses',
            id: null,
            items: [],
            update_role_code: null,
            update_role_name: null,
            update_role_description: null,
            role_code: null,
            role_name: null,
            role_description: null,
            showModal: false,
            searchValue: '',
        };
    },
    mounted() {
        this.getData(),
            this.getCode()
    },
    methods: {
        getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterRoles/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    this.role_code = response.data;
                })
                .catch((e) => {
                    // loader.hide();
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
                    this.delete(id)
                }
            })
        },
        // get data at the beginning
        async getData() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterRoles/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // show response success
                    // loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push({ id: item.id, role_code: item.role_code, role_name: item.role_name, role_description: item.role_description })
                    });
                })
                .catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        validation: function () {
            if (this.role_code == null || this.role_code == '') {
                toast.warning("Kode Role tidak boleh kosong");
                return false;
            } else if (this.role_name == null) {
                toast.warning("Nama Role tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            return true;
        },
        submit: async function () {
            if (!this.validation()) return false;

            let post = {
                role_code: this.role_code,
                role_name: this.role_name,
                role_description: this.role_description,
                created_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });
            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterRoles/add', post)
                .then((r) => {
                    if (r.status) {
                        this.role_code = '';
                        this.role_name = '';
                        this.role_description = '';
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
                    setInterval(() => {
                        location.reload();
                    }, 3000);
                });
        },
        modal(item) {
            // binding data to form modal
            this.showModal = true;
            this.id = item.id;
            this.update_role_code = item.role_code;
            this.update_role_name = item.role_name;
            this.update_role_description = item.role_description;
        },
        // update function
        update: async function () {
            // run validation
            if (this.update_role_code == null || this.update_role_code == '') {
                toast.warning("Kode Role tidak boleh kosong");
                return false;
            } else if (this.update_role_name == null) {
                toast.warning("Nama Role tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                role_code: this.update_role_code,
                role_name: this.update_role_name,
                role_description: this.update_role_description,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });
            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterRoles/edit', update)
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        this.items = [];
                        this.getData();
                        // response success
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
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
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterRoles/delete', del)
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
                    // response fail / error
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