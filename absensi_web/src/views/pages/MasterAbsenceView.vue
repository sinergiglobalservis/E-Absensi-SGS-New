<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Kode Absensi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="absence_code" readonly></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Nama Absensi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="absence_name"></FormInput>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Durasi Absensi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="absence_long_date"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Tipe Absensi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="absence_type" :option="typeOptions"></FormSelect>
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
                    <h3>Data Master Absensi</h3>
                </template>
                <template #body>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Kode Absensi</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_absence_code"></FormInput>
                                <FormInput type="hidden" v-model="id"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Nama Absensi</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_absence_name"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Durasi Absensi</label>
                            </div>
                            <div class="col-md-6">
                                <FormInput v-model="update_absence_long_date"></FormInput>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Tipe Absensi</label>
                            </div>
                            <div class="col-md-6">
                                <FormSelect v-model="update_absence_type" :option="typeOptions"></FormSelect>
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
            { text: "Kode Absensi", value: "absence_code", sortable: true },
            { text: "Nama Absensi", value: "absence_name", sortable: true },
            { text: "Tipe Absen", value: "absence_type", sortable: true },
            { text: "Durasi", value: "absence_long_date", sortable: true },
            { text: "Aksi", value: "action" },
        ];

        return {
            headers
        };
    },
    data() {
        return {
            title: 'Master Tipe Absensi',
            items: [],
            id: null,
            update_absence_code: null,
            update_absence_name: null,
            update_absence_long_date: null,
            update_absence_type: null,
            absence_code: null,
            absence_name: null,
            absence_long_date: null,
            absence_type: null,
            showModal: false,
            data: '',
            modalData: '',
            searchValue: '',
            typeOptions: [
                { value: 1, text: 'SAKIT' },
                { value: 2, text: 'CUTI' },
                { value: 3, text: 'IZIN' },
            ]
        };
    },
    mounted() {
        this.getData(),
            this.getCode()
    },
    methods: {
        getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterAbsence/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    console.log(response.data)
                    this.absence_code = response.data
                })
                .catch((e) => {
                    // loader.hide();
                    // if error / fail then show response
                    console.log(e)
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
        getData() {
            // loader.show({
            //     loader: 'bars'
            // });
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterAbsence/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push(
                            {
                                id: item.id,
                                absence_code: item.absence_code,
                                absence_name: item.absence_name,
                                absence_long_date: item.absence_long_date,
                                absence_type: (item.absence_type == 1 ? 'SAKIT' : (item.absence_type == 2 ? 'CUTI' : 'IZIN')),
                                absence_type_int: item.absence_type
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
            if (this.absence_code == null || this.absence_code == '') {
                toast.warning("Kode Absensi tidak boleh kosong");
                return false;
            } else if (this.absence_name == null || this.absence_name == '') {
                toast.warning("Nama Absensi tidak boleh kosong");
                return false;
                // } else if(this.absence_long_date == null || this.absence_long_date == ''){
                //     toast.warning("Durasi Absensi tidak boleh kosong");
                //     return false;
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
                absence_code: this.absence_code,
                absence_name: this.absence_name,
                absence_long_date: this.absence_long_date,
                absence_type: this.absence_type,
                created_by: localStorage.getItem('id')
            };

            // show loader
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterAbsence/add', post)
                .then((r) => {
                    if (r.status) {
                        this.absence_code = '';
                        this.absence_name = '';
                        this.absence_long_date = '';
                        this.absence_type = '';
                        this.items = []; // clear table
                        this.getData(); // fetch ulang data table
                        this.getCode(); // fetch ulang generate kode
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
            console.log(this.$data)
            // set data to pop up modal
            this.showModal = true;
            this.id = item.id;
            this.update_absence_code = item.absence_code;
            this.update_absence_name = item.absence_name;
            this.update_absence_long_date = item.absence_long_date;
            this.update_absence_type = item.absence_type_int;
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_absence_code == null || this.update_absence_code == '') {
                toast.warning("Kode Absensi tidak boleh kosong");
                return false;
            } else if (this.update_absence_name == null || this.update_absence_name == '') {
                toast.warning("Nama Absensi tidak boleh kosong");
                return false;
            } else if (this.update_absence_long_date == null || this.update_absence_long_date == '') {
                toast.warning("Nama Absensi tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                absence_code: this.update_absence_code,
                absence_name: this.update_absence_name,
                absence_long_date: this.update_absence_long_date,
                absence_type: this.update_absence_type,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterAbsence/edit', update)
                .then((r) => {
                    if (r.status) {
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                        loader.hide();
                        setInterval(() => {
                            location.reload();
                        }, 2000);
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
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterAbsence/delete', del)
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