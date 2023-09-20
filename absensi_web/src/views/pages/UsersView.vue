<template>
    <Pages :title="title">
        <div class="container-fluid">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Perusahaan</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="customer_code" :option="customerOptions"
                                        @change="getDepartemen(); getData()">
                                    </FormSelect>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">No. Identitas</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="identity_number"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">NIP</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="nik"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Nama Lengkap</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="profile_name"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Username</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="username"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Email</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput type="email" v-model="email"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Alamat</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput type="text" v-model="profile_address"></FormInput>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Tempat Lahir</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="birthplace"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Tanggal Lahir</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput type="date" v-model="birthdate"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Telepon</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput type="text" v-model="profile_phone"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Role Akses</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="mst_roles_id" :option="roleOptions"></FormSelect>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Departemen</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="departemen_code" :option="departemenOptions"></FormSelect>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Jenis Kelamin</label>
                                </div>
                                <div class="col-md-6">
                                    <FormSelect v-model="profile_gender" :option="jkOptions"></FormSelect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3" style="margin: auto;text-align: center;">
                                <Button type="button" @click="submit">Simpan</Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- iframe for export data -->
            <iframe hidden width="900px" height="900px" :src="this.exportLink" frameborder="0"></iframe>

            <!-- table -->
            <button class="me-4 btn btn-success btn-sm mb-1" @click="sendVerification()">Kirim Verifikasi</button>
            <!-- <button class="btn btn-success btn-sm" style="float: right;" @click="upload()">Upload Template</button>
            <button class="btn btn-primary btn-sm me-1" style="float: right;" @click="unduh()">Unduh
                Template</button> -->
            <span>Search </span>
            <input type="text" v-model="searchValue" style="margin: 5px 5px;">
            <EasyDataTable v-model:items-selected="itemsSelected" :headers="headers" :items="items"
                :search-value="searchValue" buttons-pagination border-cell table-class-name="customize-table">
                <template #item-action="item">
                    <button class="btn btn-success btn-sm me-1" id="show-modal" @click="modal(item)">Ubah</button>
                    <!-- <button type="button" class="btn btn-danger btn-sm" @click="showAlert(item.id)">Hapus</button> -->
                </template>
            </EasyDataTable>
        </div>

        <!-- modals -->
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <FormModal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>User</h3>
                </template>
                <template #body>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">No. Identitas</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_identity_number"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">NIP</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_nik"></FormInput>
                                        <FormInput type="hidden" v-model="id"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Username</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_username" readonly></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Nama Lengkap</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_profile_name"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Email</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="email" v-model="update_email"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Alamat</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="text" v-model="update_profile_address"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Telepon</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="text" v-model="update_profile_phone"></FormInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Tempat Lahir</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput v-model="update_birthplace"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Tanggal Lahir</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormInput type="date" v-model="update_birthdate"></FormInput>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Perusahaan</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_customer_code" :option="customerOptions"
                                            @change="getDepartemen()">
                                        </FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Departemen</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_departemen_code" :option="departemenOptions">
                                        </FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Jenis Kelamin</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_profile_gender" :option="jkOptions"></FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Status</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_status_code" :option="statusOptions"></FormSelect>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Role Akses</label>
                                    </div>
                                    <div class="col-md-6">
                                        <FormSelect v-model="update_mst_roles_id" :option="roleOptions"></FormSelect>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <button class="btn btn-danger btn-sm me-auto" @click="resetPassword">Reset Password</button>
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
import toast from '@/assets/js/toast';
import axios from 'axios';
import { ref } from 'vue';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: { Pages, FormModal, FormInput, FormSelect, Button },
    props: {
        params: {
            default: null
        }
    },
    data() {
        return {
            title: 'Daftar Karyawan',
            // insert new form
            nik: null,
            username: null,
            email: null,
            mst_roles_id: null,
            profile_name: null,
            customer_code: null,
            departemen_code: null,
            profile_gender: null,
            profile_address: null,
            profile_phone: null,
            identity_number: null,
            birthdate: null,
            birthplace: null,
            // update data form
            update_nik: null,
            update_username: null,
            update_profile_name: null,
            update_email: null,
            update_mst_roles_id: null,
            update_status: null,
            update_status_code: null,
            update_customer_code: null,
            update_departemen_code: null,
            update_profile_gender: null,
            update_profile_address: null,
            update_profile_phone: null,
            update_identity_number: null,
            update_birthplace: null,
            update_birthdate: null,
            // datatable
            searchValue: '',
            showModal: false,
            jkOptions: [
                { value: 'L', text: 'Laki-laki' },
                { value: 'P', text: 'Perempuan' }
            ],
            statusOptions: [
                { value: 0, text: 'Non Aktif' },
                { value: 1, text: 'Aktif' }
            ],
            roleOptions: [],
            customerOptions: [],
            departemenOptions: [],
            items: [],
            itemsSelected: [],
            exportLink: '',
        };
    },
    setup() {
        const headers = [
            { text: "NIP", value: "nik", sortable: true },
            { text: "Nama", value: "profile_name", width: 200, sortable: true },
            { text: "Penempatan", value: "customer", sortable: true },
            { text: "Departemen", value: "departemen_name", sortable: true },
            { text: "Status", value: "status", sortable: true },
            { text: "Role Akses", value: "role_name", sortable: true },
            { text: "Aksi", value: "action" }
        ];

        // const itemsSelected = ref([]);

        return {
            headers
        };
    },
    mounted() {
        // this.getData();
        this.roleOption();
        this.getCustomer();
    },
    methods: {
        // options customer
        async getCustomer() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterCustomer/show",
                    {
                        dataType: "json",
                        headers: {
                            Authorization: 'bearer ' + localStorage.getItem('token')
                        }
                    })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.customerOptions.push(
                            {
                                value: item.customer_code,
                                text: item.customer_name
                            }
                        )
                    });
                })
                .catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        async getDepartemen() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterDepartemen/getByCustomerCode/" + (this.customer_code != null ? this.customer_code : this.update_customer_code),
                    {
                        dataType: "json",
                        headers: {
                            Authorization: 'bearer ' + localStorage.getItem('token')
                        }
                    })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    this.departemenOptions = [];
                    data.forEach(item => {
                        this.departemenOptions.push({
                            value: item.departemen_code,
                            text: item.departemen_name
                        })
                    });
                })
                .catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // option roles
        async roleOption() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/masterRoles/show",
                    {
                        dataType: "json",
                        headers: {
                            Authorization: 'bearer ' + localStorage.getItem('token')
                        }
                    })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.roleOptions.push({ value: item.id, text: item.role_name })
                    });
                })
                .catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        // get data by cust code
        getData() {
            this.items = [];
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/user/getBy/" + (this.customer_code != null ? this.customer_code : this.update_customer_code),
                    {
                        dataType: "json",
                        headers: {
                            Authorization: 'bearer ' + localStorage.getItem('token')
                        }
                    })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.items.push({
                            id: item.id,
                            status: (item.status == 1 ? 'Aktif' : 'Nonaktif'),
                            role_name: item.role_name,
                            role_id: item.mst_roles_id,
                            email: item.email,
                            departemen_code: item.departemen_code,
                            departemen_name: item.departemen_name,
                            profile_name: item.profile_name,
                            intStatus: item.status,
                            username: item.username,
                            nik: item.nik,
                            customer_code: item.customer_code,
                            customer: item.customer_name,
                            profile_gender: item.profile_gender,
                            profile_address: item.profile_address,
                            profile_phone: item.profile_phone,
                            birthplace: item.birthplace,
                            birthdate: item.birthdate,
                            identity_number: item.identity_number,
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
                        toast.error('Sesi anda telah berakhir');
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        modal(item) {
            // removing binding data customer
            console.log(item)
            this.customer_code = null;
            this.departemenOptions = [];
            // binding data to form modal
            this.showModal = true;
            this.id = item.id;
            this.update_nik = item.nik;
            this.update_username = item.username;
            this.update_profile_name = item.profile_name;
            this.update_profile_gender = item.profile_gender;
            this.update_profile_address = item.profile_address;
            this.update_profile_phone = item.profile_phone;
            this.update_email = item.email;
            this.update_status = item.status;
            this.update_mst_roles_id = item.role_id;
            this.update_customer_code = item.customer_code;
            this.update_departemen_code = item.departemen_code;
            this.update_status_code = item.intStatus;
            this.update_birthdate = item.birthdate;
            this.update_birthplace = item.birthplace;
            this.update_identity_number = item.identity_number;
            this.getDepartemen();
        },
        validation: function () {
            // run validation
            if (this.nik == null || this.nik == '') {
                toast.warning("NIK tidak boleh kosong");
                return false;
            } else if (this.username == null || this.username == '') {
                toast.warning("Username tidak boleh kosong");
                return false;
            } else if (this.email == null || this.email == '') {
                toast.warning("Email tidak boleh kosong");
                return false;
            } else if (!/^[^@]+@\w+(\.\w+)+\w$/.test(this.email)) {
                toast.warning("Email tidak valid")
                return false;
            } else if (this.customer_code == null || this.customer_code == '') {
                toast.warning("Perusahaan tidak boleh kosong");
                return false;
            } else if (this.mst_roles_id == null || this.mst_roles_id == '') {
                toast.warning("Role Akses tidak boleh kosong");
                return false;
            } else if (this.profile_address == null || this.profile_address == '') {
                toast.warning("Alamat tidak boleh kosong");
                return false;
            } else if (this.profile_phone == null || this.profile_phone == '') {
                toast.warning("Telepon tidak boleh kosong");
                return false;
            } else if (this.departemen_code == null || this.departemen_code == '') {
                toast.warning("Departemen tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }
            return true;
        },
        async submit() {
            if (!this.validation()) return false;

            let post = {
                // insert into table user
                email: this.email,
                username: this.username,
                created_by: localStorage.getItem('id'),
                // insert into table customer
                customer_code: this.customer_code,
                // insert into table profile
                nik: this.nik,
                profile_name: this.profile_name,
                profile_gender: this.profile_gender,
                profile_address: this.profile_address,
                profile_phone: this.profile_phone,
                identity_number: this.identity_number,
                birthplace: this.birthplace,
                birthdate: this.birthdate,
                // insert into table role access
                mst_roles_id: this.mst_roles_id,
                // insert into table placement departemen
                departemen_code: this.departemen_code,
            };
            // console.log(post);
            // return;

            loader.show({
                loader: 'bars'
            });
            // post to api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/user/add',
                post,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
                .then((r) => {
                    if (r.status) {
                        this.nik = null;
                        this.profile_name = null;
                        this.username = null;
                        this.email = null;
                        this.mst_roles_id = null;
                        this.customer_code = null;
                        this.departemen_code = null;
                        this.departemenOptions = [];
                        this.profile_gender = null;
                        this.birthdate = null;
                        this.birthplace = null;
                        this.identity_number = null;
                        this.profile_address = null;
                        this.profile_phone = null;
                        this.items = [];
                        this.getData();
                        loader.hide();
                        toast.success("Data berhasil disimpan");
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    // if unauthorize then logout
                    if (err.code == 401) {
                        localStorage.clear()
                        sessionStorage.clear();
                        toast.error('Sesi anda telah berakhir');
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        async update() {
            var mailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            // run validation
            if (this.update_email == null || this.update_email == '') {
                if (!mailFilter.test(this.email)) {
                    toast.warning("Email tidak valid")
                    return false;
                } else {
                    toast.warning("Email tidak boleh kosong");
                    return false;
                }
            } else if (this.update_status == null || this.update_status == '') {
                toast.warning("Status tidak boleh kosong");
                return false;
            } else if (this.update_nik == null || this.update_nik == '') {
                toast.warning("NIK tidak boleh kosong");
                return false;
            } else if (this.update_profile_address == null || this.update_profile_address == '') {
                toast.warning("Alamat tidak boleh kosong");
                return false;
            } else if (this.update_profile_phone == null || this.update_profile_phone == '') {
                toast.warning("Telepon tidak boleh kosong");
                return false;
            } else if (this.update_departemen_code == null || this.update_departemen_code == '') {
                toast.warning("Departemen tidak boleh kosong");
                return false;
            } else if (this.update_mst_roles_id == null || this.update_mst_roles_id == '') {
                toast.warning("Role tidak boleh kosong");
                return false;
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                // update table user
                id: this.id,
                email: this.update_email,
                username: this.update_username,
                status: this.update_status_code,
                created_by: localStorage.getItem('id'),
                updated_by: localStorage.getItem('id'),
                // update table profiles
                nik: this.update_nik,
                profile_name: this.update_profile_name,
                profile_gender: this.update_profile_gender,
                profile_address: this.update_profile_address,
                profile_phone: this.update_profile_phone,
                birthdate: this.update_birthdate,
                birthplace: this.update_birthplace,
                identity_number: this.update_identity_number,
                // update table role access
                mst_roles_id: this.update_mst_roles_id,
                // update table customer
                customer_code: this.update_customer_code,
                // update table placement departemen
                departemen_code: this.update_departemen_code,
            };

            console.log(update)
            // return;
            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/user/edit', update,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // response success
                        this.items = [];
                        this.getData();
                        toast.success("Data berhasil diubah");
                        this.showModal = false;
                    }
                }).catch(async (e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    // if unauthorize then logout
                    if (err.code == 401) {
                        const post = {
                            token: localStorage.getItem('token')
                        }
                        await axios.post(import.meta.env.VITE_API_PATH + 'auth/out', post)
                            .then((r) => {
                                if (r.status) {
                                    loader.hide();
                                    localStorage.clear();
                                    sessionStorage.clear();
                                    toast.error('Sesi anda telah berakhir');
                                    setTimeout(function () {
                                        location.reload()
                                    }, 4000);
                                }
                            }).catch((e) => {
                                // if error / fail then show response
                                loader.hide();
                                const err = e.response.data;
                                toast.error(err.message);
                            });
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        resetPassword() {
            loader.show({
                loader: 'bars'
            });

            const reset = {
                id: this.id,
                email: this.update_email
            }

            axios.post(import.meta.env.VITE_API_PATH + 'mst/user/resetPassword', reset,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // response success
                        this.items = [];
                        this.getData();
                        toast.success("Berhasil, Password baru dikirim melalui email User");
                        this.showModal = false;
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    // if unauthorize then logout
                    if (err.code == 401) {
                        localStorage.clear()
                        sessionStorage.clear();
                        toast.error('Sesi anda telah berakhir');
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        close() {
            this.showModal = false;
        },
        sendVerification() {
            loader.show({
                loader: 'bars'
            });
            if (this.itemsSelected.length == 0) {
                toast.warning("Pilih user terlebih dahulu");
                loader.hide();
                return false;
            }
            const sendVerif = {
                users: this.itemsSelected
            }
            console.log(sendVerif)

            axios.post(import.meta.env.VITE_API_PATH + 'mst/user/sendVerification', sendVerif,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // response success
                        toast.success("Email verifikasi berhasil dikirim");
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    // if unauthorize then logout
                    if (err.code == 401) {
                        localStorage.clear()
                        sessionStorage.clear();
                        toast.error('Sesi anda telah berakhir');
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        },
        async unduh() {
            loader.show({
                loader: 'bars'
            });
            this.exportLink = '';
            const post = {
                token: localStorage.getItem('token')
            }
            return axios.post(import.meta.env.VITE_API_PATH + "mst/user/exportData", post,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                })
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
                    // if unauthorize then logout
                    if (err.code == 401) {
                        localStorage.clear()
                        sessionStorage.clear();
                        toast.error('Sesi anda telah berakhir');
                        setTimeout(function () {
                            location.reload()
                        }, 4000);
                    } else {
                        toast.error(err.message);
                    }
                });
        }
    }
}
</script>

<style scoped></style>