<template>
    <div class="container">
      <div class="row">
        <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0 mt-5">
          <div class="card card-profile shadow">
            <div class="row justify-content-center">
              <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                  <a href="#">
                    <img :src="getProfilePhoto()" class="rounded-circle">
                  </a>
                </div>
              </div>
            </div>
            <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
              <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-sm btn-primary" id="pick-avatar">Change Photo</a>
              </div>
            </div>
            <div class="card-body pt-0 pt-md-4">
              <div class="row">
                <div class="col">
                  <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                    <div>
                      <span class="heading">22</span>
                      <span class="description">Friends</span>
                    </div>
                    <div>
                      <span class="heading">10</span>
                      <span class="description">Photos</span>
                    </div>
                    <div>
                      <span class="heading">89</span>
                      <span class="description">Comments</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center">
                <h3>
                  {{ this.form.name }}<span class="font-weight-light">, 17</span>
                </h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i>Bogor, West Java
                </div>
                <div class="h5 mt-4">
                  <i class="ni business_briefcase-24 mr-2"></i>Front End Developer - Kodein 
                </div>
                <div>
                  <i class="ni education_hat mr-2"></i>University of Computer Science
                </div>
                <hr class="my-4">
                <p>{{ this.form.bio }}</p>
                <a href="#">Show more</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-8 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h3 class="mb-0">My account</h3>
                </div>
                <div class="col-4 text-right">
                  <a href="#!" class="btn btn-sm btn-primary">Settings</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form>
                <h6 class="heading-small text-muted mb-4">User information</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-username">Name</label>
                        <input type="text" id="input-username" class="form-control form-control-alternative" placeholder="Name" v-model="form.name">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-email">Email address</label>
                        <input type="email" id="input-email" class="form-control form-control-alternative" placeholder="email@example.com" v-model="form.email">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-first-name">Type</label>
                        <input type="text" id="input-first-name" class="form-control form-control-alternative disabled" disabled placeholder="Super" v-model="form.type">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-address">Change Photo</label>
                        <input id="input-address" class="form-control form-control-alternative" type="file" @change="updateProfile">
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-4">
                <!-- Description -->
                <h6 class="heading-small text-muted mb-4">About me</h6>
                <div class="pl-lg-4">
                  <div class="form-group focused">
                    <textarea rows="4" class="form-control form-control-alternative" placeholder="A few words about you ..." v-model="form.bio"></textarea>
                  </div>

                  <button class="btn btn-success" type="submit" @click.prevent="updateInfo">Update</button> 
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<style>

</style>


<script>
    export default {
        data() {
            return {
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
            getProfilePhoto() {
              let photo = (this.form.photo.length > 500) ? this.form.photo : "img/profile/" + this.form.photo;
              return photo;
            },
            loadProfile() {
              axios.get('api/profile')
                .then(({ data }) => (this.form.fill(data)));
            },
            updateProfile(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                
                if (file['size'] <= 2111775) {
                  reader.onloadend = (file) => {
                    // console.log('RESULT' ,reader.result)
                    this.form.photo = reader.result;
                  }
                  reader.readAsDataURL(file);
                } else {
                    swal('Oppss...','Your photo size is too big','error');
                }
            },
            updateInfo() {
              this.$Progress.start();
              this.form.put('api/profile')
              .then(() => {
                swal('Success','Your profile updated','success');
                this.loadProfile();
                this.$Progress.finish();
              })
              .catch(() => {
                this.$Progress.fail();
              })
            }
        },
        created() {
            this.$Progress.start();
            this.loadProfile();
            this.$Progress.finish();

        } 
    }
</script>
