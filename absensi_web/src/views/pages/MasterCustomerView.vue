<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Perusahaan</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Nama</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput hidden v-model="customer_code" readonly></FormInput>
                                    <FormInput v-model="customer_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Kode POS</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="customer_pos_code"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Alamat</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="customer_address"></FormInput>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="">Latitude</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="customer_latitude"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="">Longitude</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="customer_longitude"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="">Tgl Mulai Cut Off</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="cutoff_start"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="">Tgl Selesai Cut Off</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="cutoff_end"></FormInput>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- pic session -->
                    <h4>PIC</h4>
                    <div class="col-md-12" v-for="(input, k) in picForms" :key="k">
                        <h5 class="mt-3">No. {{ k + 1 }}</h5>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Username</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Username" class="form-control"
                                                v-model="input.username" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Email</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Email" class="form-control mt-2"
                                                v-model="input.email" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">NIP</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="NIP" class="form-control mt-2"
                                                v-model="input.nik" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="">Nama Lengkap</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Fullname" class="form-control mt-2"
                                                v-model="input.profile_name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="">Telepon</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Phone Number" class="form-control mt-2"
                                                v-model="input.profile_phone" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="">Alamat</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput type="text" placeholder="Address" class="form-control mt-2"
                                                v-model="input.profile_address" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="col-md-12">
                                    <button class="btn btn-danger btn-sm mt-3" @click="remove(k)"
                                        v-show="k || (!k && picForms.length > 1)">Hapus Form</button>
                                    <button class="btn btn-success btn-sm mt-3" @click="add(k)"
                                        v-show="k == picForms.length - 1">Tambah Form</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <button class="btn btn-primary" @click="showPic()">Show</button>
                </div> -->
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
                    <h3>Data Master Customer</h3>
                </template>
                <template #body>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h4>Perusahaan</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Kode</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_code" readonly></FormInput>
                                            <FormInput type="hidden" v-model="id"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Nama</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_name"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Alamat</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_address"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Kode POS</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_pos_code"></FormInput>
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
                                            <label for="">Longitude</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_longitude"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Latitude</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_customer_latitude"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Mulai Cut Off</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_cutoff_start"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Selesai Cut Off</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormInput v-model="update_cutoff_end"></FormInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Status</label>
                                        </div>
                                        <div class="col-md-6">
                                            <FormSelect v-model="update_status" :option="statusOptions"></FormSelect>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">PIC</label>
                                    </div>
                                    <div class="col-md-9">
                                        <v-select v-model="update_pic" :options="userOptions" multiple></v-select>
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
import FormSelect from '@/components/forms/FormSelect.vue';
import Button from '@/components/forms/FormButton.vue';
import FormModal from '@/components/forms/FormModal.vue';
import axios from 'axios';
import { ref } from 'vue';
import toast from '@/assets/js/toast';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

// declare loader
let loader = useLoading();
export default {
    components: { Pages, Date, FormInput, FormSelect, Button, FormModal },
    props: {
        params: {
            default: null
        }
    },
    setup() {
        const headers = [
            // { text: "Kode Perusahaan", value: "customer_code", sortable: true },
            { text: "Nama Perusahaan", value: "customer_name", sortable: true },
            { text: "Alamat", value: "customer_address", width: 400, sortable: true },
            { text: "Status", value: "status", width: 100, sortable: true },
            // { text: "Latitude", value: "customer_latitude",  sortable: true },
            // { text: "Longitude", value: "customer_longitude",  sortable: true },
            { text: "Aksi", width: 150, value: "action" },
        ];
        return {
            headers
        };
    },
    data() {
        return {
            title: 'Master Customer',
            items: [],
            id: null,
            customer_code: null,
            customer_name: null,
            customer_address: null,
            customer_pos_code: null,
            customer_longitude: null,
            customer_latitude: null,
            cutoff_start: null,
            cutoff_end: null,
            statusOptions: [
                { value: 0, text: 'Nonaktif' },
                { value: 1, text: 'Aktif' },
            ],
            picForms: [{
                email: null,
                username: null,
                nik: null,
                profile_name: null,
                profile_phone: null,
                profile_address: null,
            }],
            showModal: false,
            modalData: '',
            searchValue: '',
            userOptions: [],
            update_pic: [],
        };
    },
    mounted() {
        this.getData();
        this.getCode();
        this.getUserByCust();
        // this.getJadwal();
    },
    methods: {
        showPic() {
            console.log(this.picForms);
        },
        add() {
            this.picForms.push({
                email: null,
                username: null,
                nik: null,
                profile_name: null,
                profile_phone: null,
                profile_address: null,
            })
        },

        remove(index) {
            this.picForms.splice(index, 1)
        },
        async getUserByCust() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/user/getBy/" + localStorage.getItem('customer_code'), {
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
        getCode() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/getCode", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    this.customer_code = response.data;
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
            // loader.show({
            //     loader: 'bars'
            // });
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/show", {
                    dataType: "json",
                })
                .then((response) => {
                    // loader.hide();
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push({
                            id: item.id,
                            customer_code: item.customer_code,
                            customer_name: item.customer_name,
                            customer_address: item.customer_address,
                            customer_pos_code: item.customer_pos_code,
                            customer_longitude: item.customer_longitude,
                            customer_latitude: item.customer_latitude,
                            cutoff_start: item.cutoff_start,
                            cutoff_end: item.cutoff_end,
                            status: (item.status == 1 ? 'Aktif' : 'Nonaktif'),
                            statusInt: item.status,
                            pic: item.pic
                        })
                    });

                    console.log(this.items);
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
            if (this.customer_code == null || this.customer_code == '') {
                toast.warning("Kode tidak boleh kosong");
                return false;
            } else if (this.customer_name == null || this.customer_name == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.customer_address == null || this.customer_address == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.customer_longitude == null || this.customer_longitude == '') {
                toast.warning("Longitude tidak boleh kosong");
                return false;
            } else if (this.customer_latitude == null || this.customer_latitude == '') {
                toast.warning("Latitude tidak boleh kosong");
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
            // if (!this.validation()) return false;

            // show loader
            // loader.show({
            //     loader: 'bars'
            // });
            // if pic

            let post = {
                customer_code: this.customer_code,
                customer_name: this.customer_name,
                customer_address: this.customer_address,
                customer_pos_code: this.customer_pos_code,
                customer_longitude: this.customer_longitude,
                customer_latitude: this.customer_latitude,
                cutoff_start: this.cutoff_start,
                cutoff_end: this.cutoff_end,
                status: 1,
                created_by: localStorage.getItem('id'),
                pic: this.picForms,
            };
            console.log(post)

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterCustomer/add', post)
                .then((r) => {
                    console.log(r)
                    if (r.status) {
                        this.customer_code = null;
                        this.customer_name = null;
                        this.customer_address = null;
                        this.customer_pos_code = null;
                        this.customer_longitude = null;
                        this.customer_latitude = null;
                        this.cutoff_start = null;
                        this.cutoff_end = null;
                        this.items = []; // clear table
                        this.picForms = [{
                            email: null,
                            username: null,
                            nik: null,
                            profile_name: null,
                            profile_phone: null,
                            profile_address: null,
                        }];
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
            console.log(items)
            this.showModal = true;
            this.id = items.id;
            this.update_customer_code = items.customer_code;
            this.update_customer_name = items.customer_name;
            this.update_customer_address = items.customer_address;
            this.update_customer_pos_code = items.customer_pos_code;
            this.update_customer_longitude = items.customer_longitude;
            this.update_customer_latitude = items.customer_latitude;
            this.update_customer_latitude = items.customer_latitude;
            this.update_cutoff_start = items.cutoff_start;
            this.update_cutoff_end = items.cutoff_end;
            this.update_status = items.statusInt;
            this.update_pic = items.pic
        },
        // update data
        update: async function () {
            // run validation
            if (this.update_customer_code == null || this.update_customer_code == '') {
                toast.warning("Kode tidak boleh kosong");
                return false;
            } else if (this.update_customer_name == null || this.update_customer_name == '') {
                toast.warning("Nama Perusahaan tidak boleh kosong");
                return false;
            } else if (this.update_customer_address == null || this.update_customer_address == '') {
                toast.warning("Nama tidak boleh kosong");
                return false;
            } else if (this.update_customer_longitude == null || this.update_customer_longitude == '') {
                toast.warning("Longitude tidak boleh kosong");
                return false;
            } else if (this.update_customer_latitude == null || this.update_customer_latitude == '') {
                toast.warning("Latitude tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                id: this.id,
                customer_code: this.update_customer_code,
                customer_name: this.update_customer_name,
                customer_address: this.update_customer_address,
                customer_pos_code: this.update_customer_pos_code,
                customer_longitude: this.update_customer_longitude,
                customer_latitude: this.update_customer_latitude,
                cutoff_start: this.update_cutoff_start,
                cutoff_end: this.update_cutoff_end,
                status: this.update_status,
                updated_by: localStorage.getItem('id')
            };

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterCustomer/edit', update)
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
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/masterCustomer/delete', del)
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