<template>
    <Pages title="Halaman Profile">
        <div class="container-fluid">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">NIK</label>
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
                                    <FormInput v-model="username" readonly></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Email</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="email" readonly></FormInput>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Alamat</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="profile_address"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Telepon</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="profile_phone"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Telepon 2</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="profile_phone2"></FormInput>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Ubah Password</label>
                                </div>
                                <div class="col-md-6">
                                    <FormInput v-model="password"></FormInput>
                                    <span style="font-size: 11px; color: red;">*abaikan jika tidak mengubah password</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-3">
                                <Button type="button" @click="update">Perbarui</Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';
import FormInput from '@/components/forms/FormInput.vue';
import FormSelect from '@/components/forms/FormSelect.vue';
import Button from '@/components/forms/FormButton.vue';
import axios from 'axios';
import toast from '@/assets/js/toast';
import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();

export default {
    components: { Pages, FormInput, FormSelect, Button },
    props: {
        params: {
            default: null
        }
    },
    mounted() {
        this.getData();
    },
    data() {
        return {
            title: 'Dashboard',
            nik: null,
            profile_name: null,
            username: null,
            email: null,
            profile_address: null,
            profile_phone: null,
            profile_phone2: null,
            password: null,
            profile_gender: null,
        };
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData() {
            return axios
                .get(import.meta.env.VITE_API_PATH + "mst/user/getByUserId/" + localStorage.getItem('id'), {
                    dataType: "json",
                })
                .then((response) => {
                    console.log(response);
                    // binding data
                    const data = response.data.data;
                    this.nik = data.nik;
                    this.profile_name = data.profile_name;
                    this.username = data.username;
                    this.email = data.email;
                    this.profile_address = data.profile_address;
                    this.profile_phone = data.profile_phone;
                    this.profile_phone2 = data.profile_phone2;
                    this.profile_gender = data.profile_gender;
                })
                .catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        update: async function () {
            var mailFilter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            // run validation
            if (this.email == null || this.email == '') {
                if (!mailFilter.test(this.email)) {
                    toast.warning("Email tidak valid")
                    return false;
                } else {
                    toast.warning("Email tidak boleh kosong");
                    return false;
                }
            } else if (localStorage.getItem('token') == null) {
                toast.warning("Error. Silahkan login")
                localStorage.setItem('page', 'default')
                this.$root.goto('default');
            }

            let update = {
                // table user
                id: localStorage.getItem('id'),
                email: this.email,
                username: this.username,
                created_by: localStorage.getItem('id'),
                updated_by: localStorage.getItem('id'),
                // table profiles
                nik: this.nik,
                profile_name: this.profile_name,
                profile_address: this.profile_address,
                profile_phone: this.profile_phone,
                profile_phone2: this.profile_phone2,
                profile_gender: this.profile_gender,
            };

            if (this.password != null) {
                update['password'] = this.password
            }

            loader.show({
                loader: 'bars'
            });

            // hit api
            await axios.post(import.meta.env.VITE_API_PATH + 'mst/user/edit', update)
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        toast.success("Data berhasil diubah");
                    }
                }).catch((e) => {
                    loader.hide();
                    // if error / fail then show response
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
    }
}
</script>

<style scoped></style>