<template>
    <div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="form-group mb-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input class="form-control" type="search" @keyup="findsearch" v-model="search" placeholder="Search">
                                        </div>
                                    </div>
                                </div>
                                <div class="col text-center">
                                    <h2 class="mb-0">Users Table</h2>                                    
                                </div>
                                <div class="col text-right">
                                    <button class="btn btn-sm btn-info" @click="newModal"><i class="fas fa-plus"></i> Add new</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Registered at</th>
                                        <th>Manage</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="user in users.data" :key="user.id">
                                        <th>{{ user.name }}</th>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.type | upText }}</td>
                                        <td>{{ user.created_at | myDate }}</td>
                                        <td>
                                            <button class="btn btn-icon btn-sm btn-primary" type="button" @click="editModal(user)">
                                                <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i>
                                                </span>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-danger" type="button" @click="deleteUser(user.id)">
                                                <span class="btn-inner--icon"><i class="far fa-trash-alt"></i>
                                                </span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <nav>
                                <pagination class="justify-content-end mb-0" :data="users" @pagination-change-page="getResults"></pagination>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Add -->
        <div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
            	
                        <div class="card bg-white shadow border-0">
                            <div class="card-body px-4 py-4">
                                <div class="text-center text-muted mb-4">
                                    <span class="font-weight-bold" v-show="!editmode">Add User</span>

                                    <!-- When Edit mode -->
                                    <span class="font-weight-bold" v-show="editmode">Edit User's Info</span>

                                </div>
                                <form role="form" @submit.prevent="editmode ? updateUser() : createUser()">
                                    <div class="form-group">
                                        <input v-model="form.name" type="text" name="name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Name">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>

                                    <div class="form-group">
                                        <input v-model="form.email" type="text" name="email"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email Address">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>

                                    <div class="form-group">
                                        <textarea v-model="form.bio" name="bio" class="form-control" rows="3" :class="{ 'is-invalid': form.errors.has('bio') }" placeholder="Your Bio..."></textarea>
                                        <has-error :form="form" field="bio"></has-error>
                                    </div>

                                    <div class="form-group">
                                        <select v-model="form.type" type="text" name="type"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                            <option value="">Select user role</option>
                                            <option value="admin">Admin</option>
                                            <option value="user">Standard User</option>
                                            <option value="author">Author</option>
                                        </select>
                                        <has-error :form="form" field="type"></has-error>
                                    </div>

                                    <div class="form-group" v-show="!editmode">
                                        <input v-model="form.password" type="password" name="password"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password" autocomplete="off">
                                        <has-error :form="form" field="password"></has-error>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col text-right">
                                            <button type="button" class="btn btn-outline-secondary my-4" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary my-4" v-show="!editmode">Create</button>
                                            
                                            <!-- When Edit mode -->
                                            <button type="submit" class="btn btn-success my-4" v-show="editmode">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        methods: {
            findsearch: _.debounce(() => {
                Fire.$emit('searching')
            },500),
            getResults(page = 1) {
                axios.get('api/user?page=' + page)
				.then(response => {
                    this.users = response.data;
                })
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                this.form.clear();
                $('#modal-add').modal('show');
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                this.form.clear();
                $('#modal-add').modal('show');
                this.form.fill(user);
            },

            loadUser() {
                axios.get('api/user').then(({ data }) => (this.users = data));
            },

            createUser() {
                this.$Progress.start();

                this.form.post('api/user')
                .then(() => {
                    
                    Fire.$emit('AfterCreated');
                    $('#modal-add').modal('hide');
                    this.$Progress.finish();
                    
                })
                .catch(() => {
                    this.$Progress.fail();
                    swal('Error!','Something went wrong!','error')
                })

            },

            updateUser() {
                this.$Progress.start();
                
                this.form.put('api/user/' + this.form.id)
                .then(() => {
                    this.loadUser();
                    $('#modal-add').modal('hide');
                    this.$Progress.finish();
                    
                })
                .catch(() => {
                    this.$Progress.fail();
                    swal('Error!','Something went wrong!','error')

                })
            },

            deleteUser(id){
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                    if (result.value) {
                        this.form.delete('api/user/'+id)
                        .then(() => {
                            this.loadUser();
                        }).catch(() => {
                            swal('Error!','Something went wrong!','error')
                        })
                    }
                })
            }

        },
        created() {
            Fire.$on('searching', () => {
                let query = this.search;
                axios.get('api/findUser?q=' + query)
                .then((data) => {
                    this.users = data.data
                })
                .catch(() => {

                })
            }),

            Echo.private('user')
                    .listen('UserCreatedEvent', (event) =>{
                        if (event.operation == 'create') {
                            swal('Created!','User <b class="text-success">' + event.name +'</b> has been created','success')
                        }else if(event.operation == 'update'){
                            swal('Updated!','User <b class="text-info">' + event.name +'</b> has been updated','success')
                        }else{
                            swal('Deleted!','User <b class="text-danger">' + event.name +'</b> has been deleted','success')
                        }
                        this.loadUser();
                    })

            this.$Progress.start();
            this.loadUser();
            Fire.$on('AfterCreated', () => {
                this.loadUser();
            })
            this.$Progress.finish();

        }
    }
</script>
