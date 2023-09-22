<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Kode</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="mst_schedule_code"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Nama Jadwal</label>
                                </div>
                                <div class="col-md-6">
                                    <!-- <FormInput hidden v-model="mst_schedule_code" readonly></FormInput> -->
                                    <FormInput v-model="mst_schedule_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Deskripsi</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="description"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Office Hour</label>
                                </div>
                                <div class="col-md-6">
                                    <input style="height: 3em;font-size: 10px;vertical-align: middle;min-width: 30px;"
                                        type="checkbox" v-model="isNonShift" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" v-for="(input, k) in shiftForm" :key="k" v-if="isNonShift == false">
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Nama Shift</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Nama jadwal" class="form-control"
                                                v-model="input.schedule_name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Kode Shift</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Kode shift" class="form-control"
                                                v-model="input.schedule_code" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" placeholder="Jam masuk" class="form-control"
                                                v-model="input.schedule_in" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" placeholder="Jam pulang" class="form-control"
                                                v-model="input.schedule_out" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <button class="btn btn-danger btn-sm mt-3" @click="remove(k)"
                                        v-show="k || (!k && shiftForm.length > 1)">Hapus Form</button>
                                    <button class="btn btn-success btn-sm mt-3 ms-2" @click="add(k)"
                                        v-show="k == shiftForm.length - 1">Tambah Form</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4" v-if="isNonShift != ''">
                    <!-- <hr class="mt-4"> -->

                    <!-- if non shift -->
                    <div class="row" v-if="isNonShift != false">
                        <!-- <div class="row" v-if="schedule_name != null"> -->
                        <div class="col-md-6">
                            <div class="nonshift">
                                <h5>Senin</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="monday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="monday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nonshift">
                                <h5>Selasa</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="tuesday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="tuesday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nonshift">
                                <h5>Rabu</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="wednesday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="wednesday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="nonshift">
                                <h5>Kamis</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="thursday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="thursday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nonshift">
                                <h5>Jumat</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="friday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="friday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nonshift">
                                <h5>Sabtu</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="saturday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="saturday_out"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nonshift">
                                <h5>Minggu</h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Masuk</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="sunday_in"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Jam Pulang</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="time" v-model="sunday_out"></FormInput>
                                        </div>
                                    </div>
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
            <hr>
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <EasyDataTable :headers="headers" :items="items" :search-value="searchValue" buttons-pagination show-index
                border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Detail</button>
                    <button type="button" class="btn btn-danger btn-sm" @click="showAlert(item.id)">Hapus</button>
                </template>
            </EasyDataTable>
        </div>



        <!-- modals -->
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <FormModal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>Detail Master Jam Kerja</h3>
                </template>
                <template #body>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Nama</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput hidden v-model="update_schedule_code" readonly></FormInput>
                                            <FormInput v-model="update_schedule_name"></FormInput>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Jam Masuk</label>
                                            </div>
                                            <div class="col-md-6">
                                                <FormInput type="time" v-model="update_schedule_in"></FormInput>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Jam Pulang</label>
                                            </div>
                                            <div class="col-md-6">
                                                <FormInput type="time" v-model="update_schedule_out"></FormInput>
                                            </div>
                                        </div>
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
            { text: "Kode", value: "mst_schedule_code", sortable: true },
            { text: "Nama Jadwal", value: "schedule_name", sortable: true },
            { text: "Hari", value: "day", sortable: true },
            { text: "Jam Masuk", value: "schedule_in", sortable: true },
            { text: "Jam Pulang", value: "schedule_out", sortable: true },
            { text: "Aksi", value: "action" },
        ];

        return {
            headers
        };
    },
    data() {
        return {
            title: 'Master Jadwal Jam Kerja',
            items: [],
            id: null,
            mst_schedule_code: null,
            mst_schedule_name: null,
            description: null,
            showModal: false,
            data: '',
            modalData: '',
            searchValue: '',
            customerOptions: [],
            isNonShift: false,
            monday_in: '',
            monday_out: '',
            tuesday_in: '',
            tuesday_out: '',
            wednesday_in: '',
            wednesday_out: '',
            thursday_in: '',
            thursday_out: '',
            friday_in: '',
            friday_out: '',
            saturday_in: '',
            saturday_out: '',
            sunday_in: '',
            sunday_out: '',
            shiftForm: [{
                schedule_name: null,
                schedule_code: null,
                schedule_in: null,
                schedule_out: null,
            }],
        };
    },
    mounted() {
        // this.getData(),
        // this.getCode();
        this.getData();
    },
    methods: {
        add() {
            this.shiftForm.push({
                schedule_name: null,
                schedule_code: null,
                schedule_in: null,
                schedule_out: null,
            })
        },

        remove(index) {
            this.shiftForm.splice(index, 1)
        },
        async getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterSchedule/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    // this.mst_schedule_code = response.data;
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
        getData() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterSchedule/show", {
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
                            mst_schedule_code: item.mst_schedule_code,
                            schedule_name: item.schedule_name,
                            day: item.day,
                            schedule_in: item.schedule_in,
                            schedule_out: item.schedule_out,
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
            if (this.mst_schedule_name == null || this.schedule_name == '') {
                toast.warning("Nama tidak boleh kosong");
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
                mst_schedule_code: this.mst_schedule_code,
                mst_schedule_name: this.mst_schedule_name,
                schedule_name: this.mst_schedule_name,
                description: this.description,
                // schedule_in: this.schedule_in,
                // schedule_out: this.schedule_out,
                isNonShift: this.isNonShift,
                monday_in: this.monday_in,
                monday_out: this.monday_out,
                tuesday_in: this.tuesday_in,
                tuesday_out: this.tuesday_out,
                wednesday_in: this.wednesday_in,
                wednesday_out: this.wednesday_out,
                thursday_in: this.thursday_in,
                thursday_out: this.thursday_out,
                friday_in: this.friday_in,
                friday_out: this.friday_out,
                saturday_in: this.saturday_in,
                saturday_out: this.saturday_out,
                sunday_in: this.sunday_in,
                sunday_out: this.sunday_out,
                created_by: localStorage.getItem('id'),
                shift: this.shiftForm
            };

            console.log(post);

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterSchedule/add', post)
                .then((r) => {
                    console.log(r)
                    if (r.status) {
                        this.mst_schedule_code = null;
                        this.mst_schedule_name = null;
                        this.description = null;
                        this.monday_in = '';
                        this.monday_out = '';
                        this.tuesday_in = '';
                        this.tuesday_out = '';
                        this.wednesday_in = '';
                        this.wednesday_out = '';
                        this.thursday_in = '';
                        this.thursday_out = '';
                        this.friday_in = '';
                        this.friday_out = '';
                        this.saturday_in = '';
                        this.saturday_out = '';
                        this.sunday_in = '';
                        this.sunday_out = '';
                        this.isNonShift = false;
                        this.items = []; // clear table
                        this.shiftForm = [{
                            schedule_name: null,
                            schedule_code: null,
                            schedule_in: null,
                            schedule_out: null,
                        }];
                        this.getData(); // fetch ulang data table
                        // this.getCode(); // generate ulang kode
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
            console.log(items)
            // set data to pop up modal
            this.showModal = true;
            this.id = items.id;
            this.update_schedule_code = items.mst_schedule_code;
            this.update_schedule_name = items.schedule_name;
            this.update_schedule_in = items.schedule_in;
            this.update_schedule_out = items.schedule_out;
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_schedule_code == null || this.update_schedule_code == '') {
                toast.warning("Kode tidak boleh kosong");
                return false;
            } else if (this.update_schedule_name == null || this.update_schedule_name == '') {
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
                schedule_code: this.update_schedule_code,
                schedule_name: this.update_schedule_name,
                schedule_in: this.update_schedule_in,
                schedule_out: this.update_schedule_out,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterSchedule/edit', update)
                .then((r) => {
                    if (r.status) {
                        this.items = [];
                        this.getData();
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
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterSchedule/delete', del)
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